<?php
/**
 * The header for the home page.
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

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<![endif]-->
<?php wp_head(); ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"> </script>
<script type="text/javascript">
	function myFunction() {
	$("a.rsswidget").attr('target','_blank');
	}
	$(window).load(myFunction);
</script>
<?php if(get_field('dcfp_code')) { the_field('dcfp_code'); } ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'parish' ); ?></a>

    
    <div class="header-wide">
    
    	<header id="masthead" class="site-header" role="banner">
		
        	<div class="site-branding">
				<div class="dioceselogo"><a href="http://dioceseofbrooklyn.org" target="_blank" title="Diocese of Brooklyn"><img src="<?php bloginfo( 'template_directory' ); ?>/images/logo.png" alt="<?php bloginfo( 'name' ); ?>" /></a></div>
			</div><!-- .site-branding -->
       
        	<div class="header-main">
        		<div class="intro-left">
                	<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php if(get_field('top_left_text')) { ?><?php the_field('top_left_text'); ?><?php } ?> 
                </div>
                <div class="intro-right">
					<?php if(get_field('top_right_text')) { ?><?php the_field('top_right_text'); ?><?php } ?>	
                </div>
                <div class="clear"></div>
        	</div><!-- header-main -->
        	
            <div class="diocese-icon"><a href="http://dioceseofbrooklyn.org" target="_blank" title="Diocese of Brooklyn"></a></div>
		
		</header><!-- #masthead -->
    	<?php if ( is_front_page()) { 
		    while ( have_rows('alert') ) : the_row();
  if( get_sub_field('alert_active')) {
    $banner = true;
  }
  endwhile;

   if($banner == true) { ?>
 <?php if( have_rows('alert') ){ ?>
   <div class="ticker-wrap">
     <div class="mask">
<ul class="ticker"><?php
$dater = date('M d, Y');
$dater = strtotime($dater);
$dateArray = array();
  while ( have_rows('alert') ) : the_row();
  if( get_sub_field('alert_active')) {
  if( get_sub_field('recurring_event') == "Not Recurring"){
   if( get_sub_field('alert_end_date')){
     $startString = get_sub_field('alert_start_date')." ".get_sub_field('alert_start_hour').":".get_sub_field('alert_start_minute')." ".get_sub_field('alert_start_time_am_pm');
     $startTime = strtotime( $startString );
     $startTest = strtotime (date('Ymd g:i A'));
     $endString = get_sub_field('alert_end_date')." ".get_sub_field('alert_end_time_hour').":".get_sub_field('alert_end_time_minute')." ".get_sub_field('alert_end_time_am_pm');
     $endTime = strtotime( $endString );
     $endTest = strtotime (date('Ymd g:i A'));
   if ( $startTime <= $startTest && $endTime > $endTest ) { ?><li class="ticker__item"><?php if (get_sub_field('alert_url')) { ?><a href="<?php the_sub_field('alert_url'); ?>" target="_blank" class="alert-link" onClick="ga('send', 'event', 'homepage', 'internalLink', 'alert');"><?php } ?><?php the_sub_field('alert_text'); ?><?php if (get_sub_field('alert_url')) { ?> <i class="fa fa-angle-double-right fa-1" aria-hidden="true"></i></a><?php } ?></li>
	<?php } } else {
	   if ( get_sub_field('alert_start_date') <= date('Y-m-d')) { ?><li class="ticker__item"><?php if (get_sub_field('alert_url')) { ?><a href="<?php the_sub_field('alert_url'); ?>" target="_blank" class="alert-link" onClick="ga('send', 'event', 'homepage', 'internalLink', 'alert');"><?php } ?><?php the_sub_field('alert_text'); ?><?php if (get_sub_field('alert_url')) { ?> <i class="fa fa-angle-double-right fa-1" aria-hidden="true"></i></a><?php } ?></li>
  <?php } } }
  else {
    $event_date = get_sub_field('alert_start_date');
    //echo $event_date;
 		$pastDayKeep = date('Ymd',strtotime("-3 day", $dater));
 		$year = date('Y');
 		$end = "01-01-".$year;
 		$toit =  strtotime(" +1 year" . $end);
 		$event_repetition_type = get_sub_field('recurring_event');
		
 		if($event_repetition_type != 'Not Recurring') {
 		$date_calculation = "";
 		switch ($event_repetition_type) {
 		    case "Daily":
 		    $date_calculation = " +1 day";
 		    break;
 		case "Weekly":
 		    $date_calculation = " +1 week";
 		    break;
 		case "Monthly":
 		    $date_calculation = " +1 month";
 		    break;
 		case "Yearly":
 			$date_calculation = " +1 year";
 			break;
 		default:
 		    $date_calculation = "none";
 		}
 		//$dateArray[] =  $event_date;
 		$day = strtotime($event_date);
      echo $day;
 		while( $day <= $toit ) 
 		{
 		    $day = strtotime(date("Ymd", $day) . $date_calculation);
 			$finalDay = date("Ymd" , $day);
 			if($finalDay >= $pastDayKeep){
 		    $dateArray[] = strtotime($finalDay);
 			}
 		}
 	if($event_date >= $pastDayKeep){
 		$dateArray[] =  date("Ymd", $event_date);
 	}
   ksort($dateArray);
   //print_r($dateArray);
  if ( in_array(strtotime(date('Ymd')), $dateArray)) { ?><li class="ticker__item"><?php if (get_sub_field('alert_url')) { ?><a href="<?php the_sub_field('alert_url'); ?>" target="_blank" class="alert-link" onClick="ga('send', 'event', 'homepage', 'internalLink', 'alert');"><?php } ?><?php the_sub_field('alert_text'); ?><?php if (get_sub_field('alert_url')) { ?> <i class="fa fa-angle-double-right fa-1" aria-hidden="true"></i></a><?php } ?></li>
	  <?php } } } }
  endwhile; ?> </ul>
     </div>
     <div class="ticker-left-mask">Alert</div>
  </div>
	<?php  } } ?>
  		  <script>
  jQuery(document).ready(function(){
    jQuery(".ticker").webTicker({ duplicate:true, hoverpause:false, startEmpty: false });
});
  </script>
		<?php  } ?>
	</div>
    
	<div id="content" class="site-content">
