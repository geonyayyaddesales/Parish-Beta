<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package parish
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
  
  
        <div class="news-signup">
            <form class="validate" id="mc-embedded-subscribe-form" action="https://thetablet.us7.list-manage.com/subscribe/post?u=b324a80a4b8f3fe1299eb57a3&amp;id=30fa6e91f5" method="post" name="mc-embedded-subscribe-form" novalidate target="_blank">
            
                <h3>GET CATHOLIC DAILY HEADLINES</h3>
                
                <input class="required email" id="mce-EMAIL" type="email" name="EMAIL" value="" placeholder="Email Address" >
                
                <input class="button" id="mc-embedded-subscribe" type="submit" name="subscribe" value="Sign up">
				<span class="gdpr">I'm aware that my information is being collected for marketing purposes. <a href="/privacy-policy/">More info</a></span>
<a href="http://thetablet.org/emailsignup/" target="_blank" >See sample Newsletter</a>
                <div class="clear"></div>
                
                <div class="clear" id="mce-responses"></div>
            
            </form>

        </div><!-- news-signup -->
        
      <hr />  
  
  
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->
