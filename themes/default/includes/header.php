<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo page_title('Page can’t be found'); ?> - <?php echo site_name(); ?></title>

		<meta name="description" content="<?php echo site_description(); ?>">

		<link rel="stylesheet" href="<?php echo theme_url('/css/reset.css'); ?>">
		<link rel="stylesheet" href="<?php echo theme_url('/css/style.css'); ?>">
		
		<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo rss_url(); ?>">
		<link rel="shortcut icon" href="<?php echo theme_url('img/favicon.png'); ?>">

		<!--[if lt IE 9]>
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<script>var base = '<?php echo theme_url(); ?>';</script>
		<script src="<?php echo theme_url('/js/main.js'); ?>"></script>
		
	    <meta name="viewport" content="width=device-width">
	    <meta name="generator" content="Anchor CMS <?php echo ANCHOR_VERSION; ?>">
	    
	    <meta property="og:title" content="<?php echo site_name(); ?>">
	    <meta property="og:type" content="website">
	    <meta property="og:url" content="<?php echo absolute_url(); ?>">
	    <meta property="og:image" content="<?php echo absolute_url(theme_url('img/og_image.gif')); ?>">
	    <meta property="og:site_name" content="<?php echo site_name(); ?>">
	    <meta property="og:description" content="<?php echo site_description(); ?>">
		
		<?php if(customised()): ?>
		    <!-- Custom CSS -->
    		<style><?php echo article_css(); ?></style>
    		
    		<!--  Custom Javascript -->
    		<script><?php echo article_js(); ?></script>
		<?php endif; ?>
	</head>
	<body>
	
		<form id="search" action="<?php echo search_url(); ?>" method="post">
			<input type="search" name="term" placeholder="To search, type and hit enter&hellip;" value="<?php echo search_term(); ?>">
		</form>
	
		<header id="top">
			<div class="wrap">
				
				<a id="logo" href="<?php echo base_url(); ?>"><?php echo site_name(); ?></a>
	
				<?php if(has_menu_items()): ?>
				<nav id="main" role="navigation">
					<ul>
						<?php while(menu_items()): ?>
						<li <?php echo (menu_active() ? 'class="active"' : ''); ?>>
							<a href="<?php echo menu_url(); ?>" title="<?php echo menu_title(); ?>">
								<?php echo menu_name(); ?>
							</a>
						</li>
						<?php endwhile; ?>
					</ul>
				</nav>
				<?php endif; ?>
			</div>
		</header>
