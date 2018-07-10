<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package parish
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
	 
        
        <div class="footer-container">
        	<div class="footer-inner">
        	<?php echo do_shortcode("[shields]"); ?>
            <div class="footer-left">
                
                    <div class="site-info">
                        <?php wp_nav_menu( array( 'theme_location' => 'footer' ) ); ?>
                    </div><!-- site-info -->
                
                	<?php echo do_shortcode("[connect]"); ?>
            
            		<div class="clear"></div>
                    <p class="sm">&copy; <?php echo date("Y");?> <a href="http://desalesmedia.org/" title="DeSales Media Group, Inc." target="_blank">DeSales Media Group, Inc.</a> All Rights Reserved. Website by <a href="http://345design.com/" title="345 Design" target="_blank">345 Design.</a> | <a href="/privacy-policy/">Privacy Policy</a></p>
                    <p class="sm">A part of the <a href="http://dioceseofbrooklyn.org/" title="The Diocese of Brooklyn" target="_blank">Diocese of Brooklyn</a>, serving Brooklyn and Queens</p>
	            	<div class="clear"></div>
                    
                    </div><!-- .footer-left -->
			</div><!-- footer-inner -->
        </div><!-- footer-container -->

    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<script src="/wp-content/themes/parish/js/jquery.doubletaptogo.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function(){
    	tinysort('.meet-me li',{attr:'date'});
	});
  
 jQuery(document).ready(function(){
    jQuery(function () {
        jQuery('.main-navigation li:has(ul)').doubleTapToGo();
    });
    });
</script>
</body>
</html>
