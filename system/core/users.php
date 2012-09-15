<?php defined('IN_CMS') or die('No direct access allowed.');

class Users {

	public static function authed() {
		return Session::get('user');
	}
	
	public static function list_all($params = array()) {
		$sql = "select * from users where 1 = 1";
		$args = array();
		
		if(isset($params['status'])) {
			$sql .= " and status = ?";
			$args[] = $params['status'];
		}
		
		if(isset($params['sortby'])) {
			$sql .= " order by " . $params['sortby'];
			
			if(isset($params['sortmode'])) {
				$sql .= " " . $params['sortmode'];
			}
		}

		return new Items(Db::results($sql, $args));
	}
	
	public static function find($where = array()) {
		$sql = "select * from users";
		$args = array();
		
		if(isset($where['hash'])) {
			$sql .= " where md5(concat(`id`, `email`, `password`)) = ? limit 1";
			$args[] = $where['hash'];
			
			// reset clause
			$where = array();
		}
		
		if(count($where)) {
			$clause = array();
			foreach($where as $key => $value) {
				$clause[] = '`' . $key . '` = ?';
				$args[] = $value;
			}
			$sql .= " where " . implode(' and ', $clause);
		}
		
		return Db::row($sql, $args);
	}

	public static function login() {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}

		// get posted data
		$post = Input::post(array('user', 'pass', 'remember'));
		$errors = array();

		// remove white space
		$post = array_map('trim', $post);
		
		if(empty($post['user'])) {
			$errors[] = Lang::line('users.missing_login_username', 'Please enter your username');
		}
		
		if(empty($post['pass'])) {
			$errors[] = Lang::line('users.missing_login_password', 'Please enter your password');
		}

		if(empty($errors)) {
			// find user
			if($user = Users::find(array('username' => $post['user']))) {
				// check password
				if(Hash::check($post['pass'], $user->password) === false) {
					$errors[] = 'Incorrect details';
				}
			} else {
				$errors[] = 'Incorrect details';
			}
		}
		
		if(count($errors)) {
			Notifications::set('error', $errors);
			return false;
		}
		
		// if we made it this far that means we have a winner
		Session::set('user', $user);

		// avoid session fixation vulnerability
		// https://www.owasp.org/index.php/Session_fixation
		Session::regenerate();
		
		return true;
	}

	public static function logout() {
		Session::forget('user');
	}
	
	public static function recover_password() {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}

		$post = Input::post(array('email'));
		$errors = array();

		if(Validator::validate_email($post['email']) === false) {
			$errors[] = Lang::line('users.invalid_email', 'Please enter a valid email address');
		} else {
			if(($user = static::find(array('email' => $post['email']))) === false) {
				$errors[] = Lang::line('users.invalid_account', 'Account not found');
			}
		}
		
		if(count($errors)) {
			Notifications::set('error', $errors);
			return false;
		}
		
		$hash = hash('md5', $user->id . $user->email . $user->password);

		$link = Url::build(array(
			'path' => Url::make('admin/users/reset/' . $hash)
		));
		
		$subject = '[' . Config::get('metadata.sitename') . '] ' . Lang::line('users.user_subject_recover', 'Password Reset');
		$plain = Lang::line('users.user_email_recover', 'You have requested to reset your password. To continue follow the link below.') . $link;
		$headers = array('From' => 'no-reply@' . Input::server('http_host'));
		
		Email::send($user->email, $subject, $plain, $headers);
		
		Notifications::set('notice', Lang::line('users.user_notice_recover', 'We have sent you an email to confirm your password change.'));
		
		return true;
	}
	
	public static function reset_password($id) {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}

		$post = Input::post(array('password'));
		$errors = array();

		if(empty($post['password'])) {
			$errors[] = Lang::line('users.missing_password', 'Please enter a password');
		}
		
		if(count($errors)) {
			Notifications::set('error', $errors);
			return false;
		}
		
		$password = Hash::make($post['password']);
		
		$sql = "update users set `password` = ? where id = ?";
		Db::query($sql, array($password, $id));
		
		Notifications::set('success', Lang::line('users.user_success_password', 'Your new password has been set'));
		
		return true;
	}
	
	public static function delete($id) {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}

		Db::delete('users', array('id' => $id));
		
		Notifications::set('success', Lang::line('users.user_success_deleted', 'User has been deleted'));
		
		return true;
	}
	
	public static function update($id) {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}

		$post = Input::post(array('username', 'password', 'email', 'real_name', 'bio', 'status', 'role', 'delete'));
		$errors = array();

		// delete
		if($post['delete'] !== false) {
			return static::delete($id);
		} else {
			// remove it frm array
			unset($post['delete']);
		}
		
		if(empty($post['username'])) {
			$errors[] = Lang::line('users.missing_username', 'Please enter a username');
		} else {
			if(($user = static::find(array('username' => $post['username']))) and $user->id != $id) {
				$errors[] = Lang::line('users.username_exists', 'Username is already being used');
			}
		}

		if(Validator::validate_email($post['email']) === false) {
			$errors[] = Lang::line('users.invalid_email', 'Please enter a valid email address');
		}

		if(empty($post['real_name'])) {
			$errors[] = Lang::line('users.missing_name', 'Please enter a display name');
		}
		
		if(strlen($post['password'])) {
			// encrypt new password
			$post['password'] = Hash::make($post['password']);
		} else {
			// remove it and leave it unchanged
			unset($post['password']);
		}
		
		if(count($errors)) {
			Notifications::set('error', $errors);
			return false;
		}

		// format email
		$post['email'] = strtolower(trim($post['email']));
		
		// strip tags on real_name (http://osvdb.org/show/osvdb/79659)
		$post['real_name'] = strip_tags($post['real_name']);
		
		// update record
		Db::update('users', $post, array('id' => $id));
		
		// update user session?
		if(Users::authed()->id == $id) {
			Session::set('user', static::find(array('id' => $id)));
		}
		
		Notifications::set('success', Lang::line('users.user_success_updated', 'User has been updated'));
		
		return true;
	}

	public static function add() {
		// verify Csrf token
		if(Csrf::verify(Input::post('token')) === false) {
			Notifications::set('error', 'Invalid token');
			return false;
		}
		
		$post = Input::post(array('username', 'password', 'email', 'real_name', 'bio', 'status', 'role'));
		$errors = array();
		
		if(empty($post['username'])) {
			$errors[] = Lang::line('users.missing_username', 'Please enter a username');
		} else {
			if(static::find(array('username' => $post['username']))) {
				$errors[] = Lang::line('users.username_exists', 'Username is already being used');
			}
		}
		
		if(empty($post['password'])) {
			$errors[] = Lang::line('users.missing_password', 'Please enter a password');
		}

		if(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = Lang::line('users.invalid_email', 'Please enter a valid email address');
		}

		if(empty($post['real_name'])) {
			$errors[] = Lang::line('users.missing_name', 'Please enter a display name');
		}
		
		if(count($errors)) {
			Notifications::set('error', $errors);
			return false;
		}
		
		// encrypt password
		$post['password'] = Hash::make($post['password']);
		
		// format email
		$post['email'] = strtolower(trim($post['email']));
		
		// strip tags on real_name (http://osvdb.org/show/osvdb/79659)
		$post['real_name'] = strip_tags($post['real_name']);
		
		// add record
		Db::insert('users', $post);
		
		Notifications::set('success', Lang::line('users.user_success_created', 'A new user has been added'));
		
		return true;
	}

}
