<?php

if(!function_exists('ba_edd_catalog_data')){
    function ba_edd_catalog_data($site = '') {

        $apiurl = sprintf('%s/edd-api/products/?number=-1',$site);

        $transientKey = 'EddCatalogs-Beta';

        $cached = get_transient($transientKey);

        if (false !== $cached) {
            return $cached;
        }

        $remote = wp_remote_get($apiurl);

        if (is_wp_error($remote)) {
            return 'No site URL provided.';
        }

        $data 			= json_decode( $remote['body'],true);
        $total 			= isset($data['products']) ? count($data['products']) : false;

        $opts           = get_option('ba_edd_catalog_settings');
        $getexcluded 	= isset($opts['exclude']) ? $opts['exclude'] : false;

        // action
        do_action('edd_catalog_before');

        // start output
        $output = sprintf('<div class="edd-catalog-wrapper">');

            $output .= sprintf('<div class="row">');

                do_action('edd_catalog_inside_top'); // action

                    for($i=0; $i<$total; $i++) {

                        $exclude         = $getexcluded == $data['products'][$i]['info']['slug'];

                        if ( !in_array($exclude, $data) ):

                            // get some vars ready
                            $getname       	= isset($data['products'][$i]['info']['title']) ? $data['products'][$i]['info']['title'] : false;
                            $getprice      	= isset($data['products'][$i]['pricing']['amount']) ? $data['products'][$i]['pricing']['amount'] : false;
                            $getimg        	= isset($data['products'][$i]['info']['thumbnail']) ? $data['products'][$i]['info']['thumbnail'] : false;
                            $getlink       	= isset($data['products'][$i]['info']['link']) ? $data['products'][$i]['info']['link'] : false;
                            $slug          	= isset($data['products'][$i]['info']['slug']) ? $data['products'][$i]['info']['slug'] : false;

                            // get plugin path check if installed
                            $plugin     	= sprintf('%s/%s.php',$slug,$slug);
                            $isinstalled   	= is_plugin_active($plugin);

                            $image          = true == $isinstalled ? sprintf('<a class="edd-catalog-img-link" target="_blank"><img src="%s"></a>',$getimg) : sprintf('<a class="edd-catalog-img-link" href="%s" target="_blank"><img src="%s"></a>',$getlink,$getimg);
                            $link           = true == $isinstalled ? sprintf('<a class="edd-catalog-notify installed">installed</a>') : sprintf('<a class="edd-catalog-notify" href="%s">Buy Now %s</a>',$getlink,$getprice);
                            $installclass   = true == $isinstalled ? 'is-installed' : false;

                            // title
                            $title          = sprintf('<h3 class="edd-catalog-item-title">%s</h3>',$getname);

                            // output
                            $output        .= sprintf('<div class="col-md-2"><div class="edd-catalog-item %s">%s<div class="edd-catalog-item-inner">%s%s</div></div></div>',$installclass,$title,$image,$link);

                            if ( ( 0 == $i % 6 ) && ( $i < $total )) {

                                $output .= sprintf('</div><div class="row">');
                            }

                        endif;
                    }

                do_action('edd_catalog_inside_bottom'); // action

            $output .= sprintf('</div>'); // end row

        $output .= sprintf('</div>'); // end wrapper

        set_transient($transientKey, $output, 12 * HOUR_IN_SECONDS);

        return apply_filters('ba_edd_catalog_output',$output);
    }
}