<?php
date_default_timezone_set('America/New_York');
function date3339($timestamp=0) {

    if (!$timestamp) {
        $timestamp = time();
    }
    $date = date('Y-m-d\TH:i:s', $timestamp);

    $matches = array();
    if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
        $date .= $matches[1].$matches[2].':'.$matches[3];
    } else {
        $date .= 'Z';
    }
    return $date;
}
date_default_timezone_set('America/New_York');
function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}
/*
Template Name: Homepage template
* @package Parish
*/

get_header(homepage); 
?>

	<div id="primary" class="content-area homeprimary">
		<main id="main" class="site-main" role="main">
	
		<?php while ( have_posts() ) : the_post(); ?>
	
		<div class="home-welcome">
      
      		<div class="welcome-img">
				<?php 
					$royalsliderid = get_field('royal_slider');
					if(get_field('royal_slider')):
					echo do_shortcode('[kingslider id="'.$royalsliderid.'"]');
					endif;
				?>
            </div>
            
            <div class="welcome-text">
				
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                    <div class="entry-content">
                        <?php the_content(); ?>
                        <?php
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . __( 'Pages:', 'parish' ),
                                'after'  => '</div>',
                            ) );
                        ?>
                    </div><!-- .entry-content -->
                    
                </article><!-- #post-## -->
                
                <div class="spanish-welcome"><?php if(get_field('middle_right_text')) { ?><?php the_field('middle_right_text'); ?><?php } ?></div>
            </div>
            <div class="clear"></div>

        </div><!-- home-welcome -->


		</main><!-- #main -->

	</div><!-- #primary -->

</div><!-- #content -->
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="menu" aria-expanded="false"><?php _e( '&equiv; Expand Menu &equiv;', 'parish' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #site-navigation -->
		
        <div class="clear"></div>
        
        <div class="home-bottom" style="position:relative;">
        	<div class="thirdcol left">
            	<h3><?php if(get_field('bottom_headline_1')) { ?><?php the_field('bottom_headline_1'); ?><?php } ?></h3>
                
                <!-- The widget containing the RSS Feeds. -->
                
					<?php dynamic_sidebar( 'home-rss' ); ?>
                                
            </div><!-- end thirdcol left -->
            <div class="thirdcol right">
	            	<h3><?php if(get_field('bottom_headline_2')) { ?><?php the_field('bottom_headline_2'); ?><?php } ?></h3>
					<?php if (get_field('right_column_events') == 'meetme') { ?>
            		
            			<div class="meet-me">
							<div class="googlecal">
							<aside class="widget_gce_widget">
							<div class="gce-list-grouped">			
							<?php // Get RSS Feed(s)
							include_once( ABSPATH . WPINC . '/feed.php' );
							
							// Get a SimplePie feed object from the specified feed source.
							$rss = fetch_feed( 'http://meetmein.church/feed/?post_type=event' );
							$rss->enable_order_by_date(false);
							$maxitems = 0;
							
							if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly
							
							    // Figure out how many total items there are, but limit it to 5. 
							    $maxitems = $rss->get_item_quantity( 3 ); 
							
							    // Build an array of all the items, starting with element 0 (first element).
							    $rss_items = $rss->get_items( 0, $maxitems );
							   
							
							endif;
							?>
							
							    <?php if ( $maxitems == 0 ) : ?>
							        
							    <?php else : ?>
							        <?php // Loop through each feed item and display each item as a hyperlink. ?>
							        <?php foreach ( $rss_items as $item ) : 
									?>
							            <div class="gce-event-day">
											<div class="gce-list-title"><?php $date = $item->get_item_tags( '', 'evstartdate' ); $date = $date[0]["data"];  echo date("F j, Y", strtotime($date)); ?></div> 
							                <div class="gce-feed gce-list-event gce-tooltip-event">
								                <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" title="<?php printf( __( '', 'my-text-domain' ), $item->get_date('j F Y | g:i a') ); ?>">
							                    <?php echo $item->get_title(); ?>
							                	</a>
											</div>
							            </div>
							        <?php endforeach; ?>
							    <?php endif; ?>
						</div>		
						</aside><!-- end googlecal -->		
		                </div><!-- end googlecal -->
		                <aside class="widget_text"><div class="textwidget view-all"><a href="http://meetmein.church/" target="_blank">View All Diocesan Events</a></div></aside>
		                
						</div><!-- end googlecal -->	
					<?php } else { ?>	            
		                <div class="googlecal">
		                	<?php 
  											$events = array();
                        $timestamp = urlencode(date3339());
  											$calendarUrl = 'https://www.googleapis.com/calendar/v3/calendars/'.get_field('calendar_id').'/events?singleEvents=true&maxResults=4&orderBy=startTime&timeMin='.$timestamp.'&key='.get_field('api_key');
                        $json = file_get_contents($calendarUrl);
                        $obj = json_decode($json);
                        foreach($obj->items as $event){
                          //print_r($event);
                          $startDate = date('F j, Y', strtotime($event->start->dateTime));
                          $timeCode = substr($event->start->dateTime, 0, -6);
                          $startTime = date('g:i a', strtotime($timeCode));
                          $timeCode2 = substr($event->end->dateTime, 0, -6);
                          $endTime = date('g:i a', strtotime($timeCode2));
                          $events[$startDate][] = '<div class="gce-list-event gce-tooltip-event"><a href="'.$event->htmlLink.'" target="_blank">'.$event->summary.'</a></div>Starts: '.$startTime.' Ends: '.$endTime;
                        }
  										  echo '<aside class="widget_gce_widget"><div class="gce-list-grouped">';
  											foreach($events as $key => $event){
                         echo '<div class="gce-list-title">'.$key.'</div><div class="gce-feed">';
                           foreach($event as $listing){
                             echo $listing; 
                           }
                          echo '</div>';
                        }
					    echo '</div></aside><div class="textwidget view-all">';
					 	dynamic_sidebar('Home Calendar');
					 	echo '</div>';
                        ?>
		                </div><!-- end googlecal -->
	                <?php } ?>
	        </div><!-- end thirdcol right -->
            <div class="thirdcol middle">
				
				<?php 
		            if (get_field('middle_column_replacement_type') == 'Custom Ad Image'){                            
		            	if (get_field('middle_ad_link')) { ?>
								<div class="middle_ad_img"><a href="<?php the_field('middle_ad_link'); ?>" <?php if (get_field('middle_ad_link_new')) { ?>target="_blank"<?php } ?>><img src="<?php the_field('middle_add'); ?>" /></a></div>
						<?php } else { ?>
								<div class="middle_ad_img"><img src="<?php the_field('middle_add'); ?>" /></div>
						<?php }
					} elseif(get_field('middle_column_replacement_type') == 'Double Click') { ?>
					              <div class="middle_ad_img">
					              	<?php include('./wp-content/themes/parish_child/doubleclick.php'); ?>
					              </div>  
		
	
					<?php } ?>
						
            	<div class="button left"><?php if(get_field('bottom_link_1')) { ?><?php the_field('bottom_link_1'); ?><?php } ?></div>
            	<div class="button right"><?php if(get_field('bottom_link_2')) { ?><?php the_field('bottom_link_2'); ?><?php } ?></div>
            
            </div><!-- end thirdcol middle -->
            
        </div><!-- home-bottom -->            
		<div class="clear"></div>                             
            
            <?php if(get_field('bottom_right_text')) { ?><?php the_field('bottom_right_text'); ?><?php } ?>

      
			<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
