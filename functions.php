<?php

/*
	Kirjastokaista functions
*/

/* Execute Shortcodes */
function kirjastokaista_shortcode_handler($atts, $just_result) {

	global $kirjastokaista_url;
	
	/* Test connection to Kirjastokaista server first or otherwise display error*/
	if($fp = fsockopen($kirjastokaista_url,80,$errCode,$errStr,1)){

		/* If Shortcode has ID attribute */
		if (isset($atts['id'])) {
			
			$settings = get_option( "kirjastokaista_settings" );
			if (isset($settings['shortcodes'][$atts['id']])) {
				if (isset($settings['shortcodes'][$atts['id']]['cached']) && $settings['shortcodes'][$atts['id']]['cached'] == 0) {
					$settings['shortcodes'][$atts['id']]['cachedresult'] = do_shortcode($settings['shortcodes'][$atts['id']]['shortcode']);
					$settings['shortcodes'][$atts['id']]['cached'] = 1;
					$updated = update_option( "kirjastokaista_settings", $settings );
					return $settings['shortcodes'][$atts['id']]['cachedresult'];
				} else {
					if (isset($settings['shortcodes'][$atts['id']]['cached'])) {
						if ($settings['shortcodes'][$atts['id']]['cached'] == 1) {
							return $settings['shortcodes'][$atts['id']]['cachedresult'];
						} else {
							return do_shortcode($settings['shortcodes'][$atts['id']]['shortcode']);
						}
					} else {
						return do_shortcode($settings['shortcodes'][$atts['id']]['shortcode']);
					}
				}
			} else {
				return __("Requested Kirjastokaista listing is not available anymore.", "kirjastokaista");
			}
	
			die();
		/* If no id, parse attributes */
		} else {
			$shortcode_atts = array('ui' => '', 'categories' => '', 'types' => '', 'languages' => '', 'layout' => '', 'order' => '', 'results' => '', 'new_window' => '', 'enable_caption' => false, 'enable_fromtxt' => false);
			


			if ($atts['ui'] != "") {
				$shortcode_atts['ui'] = "&language=".$atts['ui'];
			}
			
			global $kirjastokaista_ui_list;
			$kirjastokaista_ui_name = $kirjastokaista_ui_list[$atts['ui']]['name'];
			$kirjastokaista_ui_url = $kirjastokaista_ui_list[$atts['ui']]['url'];
			if ($kirjastokaista_ui_name == "") {
				$kirjastokaista_ui_name = "Kirjastokaista";
			}
			if ($kirjastokaista_ui_url == "") {
				$kirjastokaista_ui_url = "http://www.kirjastokaista.fi";
			}
			
			if ($atts['categories'] != "") {
				$shortcode_atts['categories'] = "&cat=".$atts['categories'];
			}
			
			if (isset($atts['languages'])) {
				if ($atts['languages'] != "") {
					// Check if comma exists in languages attribute
					$commapos = strpos($atts['languages'], ",");
					if ($commapos !== false) {
						$meta_compare = "IN";
					} else {
						$meta_compare = "IS";
					}
					/* Convert language codes to the format they are in Kirjastokaista's database */
					$replace_langs = str_replace("fi", "suomi", $atts['languages']);
					$replace_langs = str_replace("no", "norsk", $replace_langs);
					$replace_langs = str_replace("en", "english", $replace_langs);
					$replace_langs = str_replace("sv", "svenska", $replace_langs);
					$replace_langs = str_replace("ru", "russian", $replace_langs);
					$shortcode_atts['languages'] = "&meta_key=video_language&meta_compare=".$meta_compare."&meta_value=".$replace_langs;
				}
			}

			if ($atts['order'] != "") {
				if ($atts['order'] == "newest") {
					$shortcode_atts['order'] = "&orderby=date&order=desc";
				} elseif ($atts['order'] == "mostpopular") {
					$shortcode_atts['order'] = "&orderby=meta_value_num&meta_key=number_of_plays_sum&order=desc";
					$shortcode_atts['languages'] = '';
				} elseif ($atts['order'] == "random") {
					$shortcode_atts['order'] = "&orderby=rand";
				}
			}
			
			if ($atts['results'] != "") {
				$shortcode_atts['results'] = "&count=".$atts['results'];
			}
			
			$api_file = "http://".$kirjastokaista_url."/api/?json=get_posts&dev=1".$shortcode_atts['categories']."&custom_fields=duration%2Cnumber_of_plays_sum%2Cvideoembed_videoembed%2Cvideo_language%2Cvideo_thumbnail_large%2Cvideo_thumbnail_medium%2Cvideo_thumbnail_small".$shortcode_atts['ui'].$shortcode_atts['languages'].$shortcode_atts['order'].$shortcode_atts['results'];
			
			//echo $api_file;
			//die();
			
			$api_request = file_get_contents($api_file);
			$JSON_api_request = json_decode($api_request);
			
			/* If asked function just for result, print it now as JSON and die */
			if ($just_result) {
				return $api_request;
				die();
			}

			$shortcode_output = "";
			
			if ($atts['layout'] != "") {
				$shortcode_atts['layout'] = $atts['layout'];
			} else {
				$shortcode_atts['layout'] = "list";
			}
			
			if (isset($atts['new_window'])) {
				if ($atts['new_window'] == "1") {
					$shortcode_atts['new_window'] = ' target="_blank"';
				}
			}

			if (isset($atts['enable_fromtxt'])) {
				if ($atts['enable_fromtxt'] == "1") {
					$shortcode_atts['enable_fromtxt'] = true;
				}
			}

			if (isset($atts['enable_caption'])) {
				if ($atts['enable_caption'] == "1") {
					$shortcode_atts['enable_caption'] = true;
				}
			}

			
			if (!$JSON_api_request->{'posts'}) {
				return "Nothing found from Kirjastokaista matching your search terms.";
			} else {
			
				switch ($shortcode_atts['layout']) {
			    case "list":
					foreach ($JSON_api_request->{'posts'} as $posti) {
						include 'kirjastokaista-api-info.php';
						if (file_exists(get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/list.php')) {
							include get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/list.php';	
						} else {
							include 'templates/list.php';	
						}
						$shortcode_output .= $list_content;
					}
					break;
			    case "thumbnail":
					foreach ($JSON_api_request->{'posts'} as $posti) {
						include 'kirjastokaista-api-info.php';
						if (file_exists(get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/thumbnail.php')) {
							include get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/thumbnail.php';	
						} else {
							include 'templates/thumbnail.php';	
						}
						$shortcode_output .= $thumbnail_content;
					}
			        break;
			    case "embed":
					foreach ($JSON_api_request->{'posts'} as $posti) {
						include 'kirjastokaista-api-info.php';
						if (file_exists(get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/embed.php')) {
							include get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/embed.php';	
						} else {
							include 'templates/embed.php';	
						}
						$shortcode_output .= $embed_content;
					}
			        break;
			    case "slideshow":
			    	global $settings;
			    	if ( $settings["kirjastokaista_use_jcarousel"] ) {
				        $shortcode_output .= '<div class="jcarousel-wrapper">
			                <div class="jcarousel">
			                    <ul>';
					}
					foreach ($JSON_api_request->{'posts'} as $posti) {
						include 'kirjastokaista-api-info.php';
						if (file_exists(get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/slideshow.php')) {
							include get_template_directory().'/'.plugin_basename(__FILE__).'/plugin-layout-templates/slideshow.php';	
						} else {
							include 'templates/slideshow.php';	
						}
						$shortcode_output .= $slideshow_content;
						
					}
					if ( $settings["kirjastokaista_use_jcarousel"] ) {
						$shortcode_output .= '</ul></div><a href="#" class="jcarousel-control-prev">&lsaquo;</a><a href="#" class="jcarousel-control-next">&rsaquo;</a>';
						if ($shortcode_atts['enable_fromtxt']) {
							$shortcode_output .= '<div class="from_kirjastokaista"><a href="'.$kirjastokaista_ui_url.'"'.$shortcode_atts['new_window'].'">'.__('from ', 'kirjastokaista').$kirjastokaista_ui_name.'</a></div>';
						}
						$shortcode_output .= '</div>';
					}
			        break;
				}
				
				return $shortcode_output;
			}
		}
	} else {
		return '<span class="kirjastokaista_connection_error">'.__('(Kirjastokaista content  here. Temporarily could not connect.)','kirjastokaista').'</span>';
	}
}

function kirjastokaista_slug_embed_handler($atts) {
	global $kirjastokaista_url;
	$api_file = "http://www.kirjastokaista.fi/api/?json=get_post&dev=1&post_slug=".$atts['slug'];
	$api_request = file_get_contents($api_file);
	$JSON_api_request = json_decode($api_request);
	return $JSON_api_request->{'post'}->{'custom_fields'}->{'videoembed_videoembed'}[0];
}

?>