<?php

if(!function_exists('ba_edd_catalog_data')){
	function ba_edd_catalog_data($site = '', $key = '', $token ='') {

	    $apiurl = sprintf('%s/edd-api/products/?key=%s&token=%s&number=-1',$site,$key,$token);

	    $transientKey = 'baEddCatalog-2399rrxdf643';

	    $cached = get_transient($transientKey);

	    if (false !== $cached) {
	       	return $cached;
	    }

	    $remote = wp_remote_get($apiurl);

	    if (is_wp_error($remote)) {
	        return '256';
	    }

	    $data = json_decode( $remote['body'],true);
	    $total = isset($data['products']) ? count($data['products']) : false;

	    // action
	    do_action('edd_catalog_before');

	    // start output
	    $output = sprintf('<div class="edd-catalog-wrapper">');

			//action
		    do_action('edd_catalog_inside_top');

			    for($i=0; $i<$total; $i++) {

				    $getname 	= isset($data['products'][$i]['info']['title']) ? $data['products'][$i]['info']['title'] : false;
				    $getprice 	= isset($data['products'][$i]['pricing']['amount']) ? $data['products'][$i]['pricing']['amount'] : false;
				    $getimg 	= isset($data['products'][$i]['info']['thumbnail']) ? $data['products'][$i]['info']['thumbnail'] : false;
				    $getlink 	= isset($data['products'][$i]['info']['link']) ? $data['products'][$i]['info']['link'] : false;
				    $slug 		= isset($data['products'][$i]['info']['slug']) ? $data['products'][$i]['info']['slug'] : false;

				    $title 		= sprintf('<h3 class="edd-catalog-item-title">%s</h3>',$getname);
				    $image 		= sprintf('<a class="edd-catalog-img-link" href="%s"><img src="%s"></a>',$getlink,$getimg);

			   	 	$plugin 	= sprintf('%s/%s.php',$slug,$slug);
				    $link 		= is_plugin_active($plugin) ? sprintf('<a class="edd-catalog-notify installed">installed</a>') : sprintf('<a class="edd-catalog-notify" href="%s">Buy Now %s</a>',$getlink,$getprice);
				    $output 	.= sprintf('<div class="edd-catalog-item">%s<div class="edd-catalog-item-inner">%s%s</div></div>',$title,$image,$link);

				}

			do_action('edd_catalog_inside_bottom');

		$output .= sprintf('</div>');

	    set_transient($transientKey, $output, 600);

	    return apply_filters('ba_edd_catalog_output',$output);
	}
}