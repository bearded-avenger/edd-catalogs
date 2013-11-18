<?php
include_once( ABSPATH . WPINC . '/feed.php' );

if(!function_exists('ba_edd_catalog_news_feed')){
	function ba_edd_catalog_news_feed( ){

		$opts 	= get_option('ba_edd_catalog_settings');
		$feed   = isset($opts['feed']) ? $opts['feed'] : 'http://easydigitaldownloads.com/blog/feed';


		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed( $feed );

		if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity( 3 ); 

		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items( 0, $maxitems );

		else :

		   $maxitems = false;

		endif;
		?>

		<ul class="ba-edd-catalog-news-list">
		    <?php if ( $maxitems == 0 ) : ?>
		        <li><?php _e( 'No feed supplied.', 'edd-catalogs' ); ?></li>
		    <?php else : ?>
		        <?php // Loop through each feed item and display each item as a hyperlink. ?>
		        <?php foreach ( $rss_items as $item ) : ?>
		            <li>
		                <h4><a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __( 'Posted %s', 'edd-catalogs' ), $item->get_date('j F Y | g:i a') ); ?>">
		                	<?php echo esc_html( $item->get_title() ); ?>
		                </a></h4>
		                <?php echo wp_trim_words( $item->get_content(), 40, '...' ); ?>
		            </li>
		        <?php endforeach; ?>
		    <?php endif; ?>
		</ul><?php
	}
}