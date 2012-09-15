<h1><?php echo __('users.add_user', 'Add a new user'); ?></h1>

<?php echo Notifications::read(); ?>

<section class="content">

	<form method="post" action="<?php echo Url::current(); ?>" novalidate autocomplete="off">

		<input name="token" type="hidden" value="<?php echo Csrf::token(); ?>">
		
		<fieldset>
			<p>
				<label for="real_name"><?php echo __('users.real_name', 'Real name'); ?>:</label>
				<input id="real_name" name="real_name" value="<?php echo Input::post('real_name'); ?>">
				
				<em><?php echo __('users.real_name_explain', 'The user&rsquo;s real name. Used in author bylines (visible to public).'); ?></em>
			</p>
						
			<p>
				<label for="bio"><?php echo __('users.bio', 'Biography'); ?>:</label>
				<textarea id="bio" name="bio"><?php echo Input::post('bio'); ?></textarea>
				
				<em><?php echo __('users.bio_explain', 'A short biography for your user. Accepts valid HTML.'); ?></em>
			</p>
			
			<p>
				<label for="status"><?php echo __('users.status', 'Status'); ?>:</label>
				<select id="status" name="status">
					<?php foreach(array(
						'inactive' => __('users.inactive', 'Inactive'),
						'active' => __('users.active', 'Active')
					) as $value => $status): ?>
					<?php $selected = (Input::post('status') == $value) ? ' selected' : ''; ?>
					<option value="<?php echo $value; ?>"<?php echo $selected; ?>>
						<?php echo $status; ?>
					</option>
					<?php endforeach; ?>
				</select>
				
				<em><?php echo __('users.status_explain', 'If inactive, the user will not be able to log in.'); ?></em>
			</p>
			
			<p>
				<label for="role"><?php echo __('users.role', 'Role'); ?>:</label>
				<select id="role" name="role">
					<?php foreach(array(
						'administrator' => __('users.administrator', 'Administrator'), 
						'editor' => __('users.editor', 'Editor'), 
						'user' => __('users.user', 'User')
					) as $value => $role): ?>
					<?php $selected = (Input::post('role') == $value) ? ' selected' : ''; ?>
					<option value="<?php echo $value; ?>"<?php echo $selected; ?>>
						<?php echo $role; ?>
					</option>
					<?php endforeach; ?>
				</select>
				
				<em><?php echo __('users.role_explain', 'The user&rsquo;s role. See <a href="//anchorcms.com/docs/roles">here</a> for more info.'); ?></em>
			</p>
		</fieldset>
		
		<fieldset>
		
			<legend><?php echo __('users.user_details', 'User details'); ?></legend>
			<em><?php echo __('users.user_details_explain', 'Create the details for your new user to log in to Anchor.'); ?></em>
		
			<p>
				<label for="username"><?php echo __('users.username', 'Username'); ?>:</label>
				<input id="username" name="username" value="<?php echo Input::post('username'); ?>">
				
				<em><?php echo __('users.username_explain', 'The desired username. Can be changed later.'); ?></em>
			</p>

			<p>
				<label for="password"><?php echo __('users.password', 'Password'); ?>:</label>
				<input id="password" type="password" name="password">
				
				<em><?php echo __('users.password_explain', 'And the matching password. Can be changed later.'); ?></em>
			</p>
			
			<p>
				<label for="email"><?php echo __('users.email', 'Email'); ?>:</label>
				<input id="email" name="email" value="<?php echo Input::post('email'); ?>">
				
				<em><?php echo __('users.email_explain', 'The user&rsquo;s email address. Needed if the user forgets their password.'); ?></em>
			</p>
		</fieldset>
			
		<p class="buttons">
			<button type="submit"><?php echo __('users.create', 'Create'); ?></button>
			<a href="<?php echo admin_url('users'); ?>"><?php echo __('users.return_users', 'Return to users'); ?></a>
		</p>
	</form>

</section>

