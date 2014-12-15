<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
</div><!-- #page -->

<footer id="colophon" class="footer" role="contentinfo">
	<div class='footer-inner-wrap inner-wrap'>
		<div class="site-info">
			<p id="web-copyright" class="web-copyright copyright-div"><a href="http://lowermedia.net/" title="Iowa Web Development and Design | Drupal Wordpress" rel="generator">A LowerMedia Site</a></p>
			<p id="film-copyright" class="film-copyright copyright-div">Copyright 2013 - &copy;<?php echo date("Y"); ?>, West Middle Productions, LLC, All Rights Reserved</p>
		</div>
		</div><!-- .site-info -->
	</div><!-- .inner-footer-wrap -->
</footer><!-- #colophon -->

<?php wp_footer(); 

/*

<?php do_action( 'twentytwelve_credits' ); ?>
		<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentytwelve' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentytwelve' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentytwelve' ), 'WordPress' ); ?></a>

*/
?>
</body>
</html>