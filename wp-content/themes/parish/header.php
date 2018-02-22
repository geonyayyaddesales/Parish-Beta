<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package parish
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="shortcut icon" href="/wp-content/themes/parish/favicon.ico" />
<!-- Catholic Ad Extend tag mgr code. Paste this (asynchronous) code as high in the <head> (of all pages) as possible of the page as possible
-->
<script async="1">
(function() {
  __mtm = [ '59f16741ed30825252eb2c08', 'cdn01.mzbcdn.net/mngr' ];
  var s = document.createElement('script');
  s.async = 1;
  s.src = '//' + __mtm[1] + '/mtm.js';
  var e = document.getElementsByTagName('script')[0];
  (e.parentNode || document.body).insertBefore(s, e);
})();
</script>
<!-- End code -->
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<![endif]-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"> </script>
<script src="/wp-content/themes/parish/js/jquery.doubletaptogo.js"></script>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'parish' ); ?></a>

	<div class="header-wide">
    
    <header id="masthead" class="site-header" role="banner">
		
        <div class="site-branding">
            <div class="dioceselogo"> <img src="<?php bloginfo( 'template_directory' ); ?>/images/logo.png" alt="<?php bloginfo( 'name' ); ?>" /></a></div>
        </div><!-- .site-branding -->
        
        <div class="header-main">
        	<div class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></div>
        </div><!-- header-main -->
        
        <div class="diocese-icon"></div>
                
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="menu" aria-expanded="false"><?php _e( '&equiv; Expand Menu &equiv;', 'parish' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->
	</div>
    
	<div id="content" class="site-content">
