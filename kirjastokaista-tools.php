<?php

/*
	Tools -> Kirjastokaista Page
*/


/* Save Shortcodes */
if (isset($_POST['save_shortcode'])) {
	$settings = get_option( "kirjastokaista_settings" );
	$shortcode_repl = preg_replace('/[^a-zA-ZäÄöÖåÅ0-9.,[]="\s]/', '', $_POST['save_shortcode']);
	$shortcode_name = $_POST['name'];
	$shortcode_description = $_POST['description'];
	$cachedresult = $_POST['cachedresult'];

	if (!isset($settings['shortcodes'])) {
		$settings['shortcodes'] = array(1 => array("shortcode" => $shortcode_repl, "name" => $shortcode_name, "description" => $shortcode_description, "cachedresult" => $cachedresult));
		$updated = update_option( "kirjastokaista_settings", $settings );
		echo "1";
	} else {
		ksort($settings['shortcodes']);
		$newid = key( array_slice( $settings['shortcodes'], -1, 1, TRUE ) )+1;
		$settings['shortcodes'][$newid]['shortcode'] = $shortcode_repl;
		$settings['shortcodes'][$newid]['name'] = $shortcode_name;
		$settings['shortcodes'][$newid]['description'] = $shortcode_description;
		if ($cachedresult == 1) {
			$settings['shortcodes'][$newid]['cached'] = 0;
			$settings['shortcodes'][$newid]['cachedresult'] = "";
		}

		$updated = update_option( "kirjastokaista_settings", $settings );
		echo $newid;
	}	
	die();
}



/* Handle View Shortcode Results GET results */
if (isset($_GET['just_result'])) {
	
	$just_result = array('ui' => '', 'categories' => '', 'types' => '', 'languages' => '', 'layout' => '', 'order' => '', 'results' => '');

	if (isset($_GET['ui'])) {
		$just_result['ui'] = $_GET['ui'];
	}
	if (isset($_GET['categories'])) {
		$just_result['categories'] = $_GET['categories'];
	}
	if (isset($_GET['types'])) {
		$just_result['types'] = $_GET['types'];
	}
	if (isset($_GET['languages'])) {
		$just_result['languages'] = $_GET['languages'];
	}
	if (isset($_GET['layout'])) {
		$just_result['layout'] = $_GET['layout'];
	}
	if (isset($_GET['order'])) {
		$just_result['order'] = $_GET['order'];
	}
	if (isset($_GET['results'])) {
		$just_result['results'] = $_GET['results'];
	}

	$defaultQuery = array("ui" => $just_result['ui'], "categories" => $just_result['categories'], "types" => $just_result['types'], "languages" => $just_result['languages'], "layout" => $just_result['layout'], "order" => $just_result['order'], "results" => $just_result['results']);
	echo kirjastokaista_shortcode_handler($defaultQuery, true);
	die();
}

/* Handle Embed single video from post editor GET results */
if (isset($_GET['single_embed'])) {
	/* Get the slug part of the given URL */
	$embed_base = basename($_GET['single_embed']);
	$slug_embed_attrb = array("slug" => $embed_base);
	echo kirjastokaista_slug_embed_handler($slug_embed_attrb);
	die();
}

/* Add Kirjastokaista button to WYSIWYG editor */
function kirjastokaista_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "kirjastokaista_add_tinymce_plugin");
        add_filter('mce_buttons', 'kirjastokaista_register_my_tc_button');
    }
}

function kirjastokaista_add_tinymce_plugin($plugin_array) {
    $plugin_array['kirjastokaista_tc_button'] = plugins_url( '/js/kirjastokaista.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
    return $plugin_array;
}

function kirjastokaista_register_my_tc_button($buttons) {
   array_push($buttons, "kirjastokaista_tc_button");
   return $buttons;
}


// Enqueue Kirjastokaista plugin required JS
add_action( 'admin_enqueue_scripts', 'kirjastokaista_queue_my_admin_scripts');
function kirjastokaista_queue_my_admin_scripts() {
	global $kirjastokaista_url;
    wp_enqueue_script ( 'kirjastokaista_admin_js' , plugin_dir_url( __FILE__ ).'/js/kirjastokaista-admin.js', array('jquery-ui-dialog'));

	wp_localize_script( 'kirjastokaista_admin_js', 'objectL10n', array(
		'kirjastokaista_plugin_path' => plugin_dir_url( __FILE__ ),
		'kirjastokaista_txt_close' => __('Close','kirjastokaista'),
		'kirjastokaista_txt_saveas' => __('Save Shortcode as...','kirjastokaista'),
		'kirjastokaista_txt_libraries_own' => __('Libraries own productions','kirjastokaista'),
		'kirjastokaista_txt_audio_categories' => __('Audio categories','kirjastokaista'),
		'kirjastokaista_txt_fetching' => __('Fetching...','kirjastokaista'),
		'kirjastokaista_txt_hits' => __('Hits','kirjastokaista'),
		'kirjastokaista_txt_notice_choose_some_categories' => __('Notice: You should choose some categories to get better results','kirjastokaista'),
		'kirjastokaista_txt_shortcode_saved_as' => __('Shortcode saved as','kirjastokaista'),
		'kirjastokaista_txt_please_type_name' => __('Please type a name for shortcode','kirjastokaista'),
		'kirjastokaista_txt_are_you_sure_delete' => __('Are you sure you want to delete shortcode ','kirjastokaista'),
		'kirjastokaista_url'  => $kirjastokaista_url )
	);

    wp_register_style ('kirjastokaista_font-awesome', plugin_dir_url( __FILE__ ).'css/font-awesome/css/font-awesome.min.css' );
	wp_enqueue_style('kirjastokaista_font-awesome');
    wp_enqueue_style (  'wp-jquery-ui-dialog');
}



add_action( 'init', 'kirjastokaista_admin_init' );
add_action( 'admin_menu', 'kirjastokaista_settings_page_init' );

function kirjastokaista_admin_init() {
	$settings = get_option( "kirjastokaista_settings" );
	if ( empty( $settings ) ) {
		$settings = array(
			'kirjastokaista_show_embed_editor' => false,
			'kirjastokaista_allow_text_widget' => false,
			'kirjastokaista_use_jcarousel' => false
		);
		add_option( "kirjastokaista_settings", $settings, '', 'yes' );
	}

	if ($settings['kirjastokaista_show_embed_editor'] == true) {
		add_action('admin_head', 'kirjastokaista_add_my_tc_button');
	}
}


function kirjastokaista_settings_page_init() {
	$settings_page = add_submenu_page('tools.php', 'WL Kirjastokaista', 'WL Kirjastokaista', 'edit_posts', 'kirjastokaista', 'kirjastokaista_settings_page');
	add_action( "load-{$settings_page}", 'kirjastokaista_load_settings_page' );
}



function kirjastokaista_load_settings_page() {
	if (isset($_POST["kirjastokaista-settings-submit"])) {
		if ( $_POST["kirjastokaista-settings-submit"] == 'Y' ) {
			check_admin_referer( "kirjastokaista-settings-page" );
			kirjastokaista_save_settings();
			$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
			wp_redirect(admin_url('tools.php?page=kirjastokaista&'.$url_parameters));
			exit;
		}
	}
}







function kirjastokaista_save_settings() {
	global $pagenow;
	$settings = get_option( "kirjastokaista_settings" );
	
	if ( $pagenow == 'tools.php' && $_GET['page'] == 'kirjastokaista' ){ 
		if ( isset ( $_GET['tab'] ) )
	        $tab = $_GET['tab']; 
	    else
	        $tab = 'shortcode-generator'; 

	    switch ( $tab ){ 
	        case 'settings' :
				$settings['kirjastokaista_show_embed_editor']	= $_POST['kirjastokaista_show_embed_editor'];
				$settings['kirjastokaista_allow_text_widget']	= $_POST['kirjastokaista_allow_text_widget'];
				$settings['kirjastokaista_use_jcarousel']		= $_POST['kirjastokaista_use_jcarousel'];
			break; 
	    }
	}
	
	if( !current_user_can( 'unfiltered_html' ) ){
		if ( $settings['kirjastokaista_ga']  )
			$settings['kirjastokaista_ga'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['kirjastokaista_ga'] ) ) );
		if ( $settings['kirjastokaista_intro'] )
			$settings['kirjastokaista_intro'] = stripslashes( esc_textarea( wp_filter_post_kses( $settings['kirjastokaista_intro'] ) ) );
	}

	$updated = update_option( "kirjastokaista_settings", $settings );
}





function kirjastokaista_admin_tabs( $current = 'shortcode-generator' ) { 
	
	$tabs['shortcode-generator'] = __('Shortcode generator', 'kirjastokaista');
    $settings = get_option( "kirjastokaista_settings" );
    if (isset($settings['shortcodes'])) {
    	$tabs['shortcodes'] = __('Shortcodes', 'kirjastokaista');
    }
	$tabs['settings'] = __('Settings', 'kirjastokaista');
	$tabs['plugin-info'] = __('Plugin info', 'kirjastokaista');

    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=kirjastokaista&tab=$tab'>$name</a>";
        
    }
    echo '</h2>';
}

function kirjastokaista_settings_page() {
	global $pagenow;
	
	$settings = get_option( "kirjastokaista_settings" );
	wp_enqueue_style( 'kirjastokaista-admin-styles', plugin_dir_url( __FILE__ ).'/css/kirjastokaista-admin-styles.css' );

	?>
	
	<div class="wrap">
		<h2>WL Kirjastokaista</h2>
		
		<?php
			if (isset($_GET['updated'])) {
				if ( 'true' == esc_attr( $_GET['updated'] )) {
					echo '<div class="updated" ><p>'._e('Kirjastokaista Settings updated.', 'kirjastokaista').'</p></div>';
				}
			}
			
			if ( isset ( $_GET['tab'] ) ) kirjastokaista_admin_tabs($_GET['tab']); else kirjastokaista_admin_tabs('shortcode-generator');
		?>
		
		<div id="modal_choose_categories" style="display:none;">
			<h3><?php echo _e('Select Categories from Kirjastokaista', 'kirjastokaista');?></h3>
			<div id="modal_available_categories"><?php echo _e('Fetching...', 'kirjastokaista');?></div>
		</div>
		<div id="modal_view_results" style="display:none;">
			<h3><?php echo _e('Shortcode Results', 'kirjastokaista');?></h3>
			<div id="modal_shortcode_results"><?php echo _e('Fetching...', 'kirjastokaista');?></div>
		</div>
		<div id="modal_generate_shortcode" style="display:none;">
			<h3><?php echo _e('Generated Kirjastokaista shortcode', 'kirjastokaista');?></h3>
			<div id="modal_generated_shortcode"><?php echo _e('Generating...', 'kirjastokaista');?></div>
			<div id="modal_save_shortcode_as">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="shortcode_name"><?php echo _e('Shortcode name', 'kirjastokaista');?></label></th>
							<td><input type="text" name="shortcode_name" id="shortcode_name" /></td>
						</tr>
						<tr>
							<th><label for="shortcode_description"><?php echo _e('Shortcode description', 'kirjastokaista');?></label></th>
							<td><textarea name="shortcode_description" id="shortcode_description" /></textarea></td>
						</tr>
						<tr>
							<th><label for="shortcode_cachedresults"><?php echo _e('Cached results', 'kirjastokaista');?></label></th>
							<td><input type="checkbox" id="shortcode_cachedresults" /></td>
						</tr>
						<tr>
							<th></th>
							<td><button id="save_shortcode"><?php echo _e('Save Shortcode', 'kirjastokaista');?></button></td>
						</tr>

					</tbody>
				</table>
			</div>
		</div>
		
		
		<div id="poststuff">
			<form method="post" action="<?php admin_url( 'tools.php?page=kirjastokaista' ); ?>">
				<?php
				wp_nonce_field( "kirjastokaista-settings-page" ); 
				
				if ( $pagenow == 'tools.php' && $_GET['page'] == 'kirjastokaista' ){ 
				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'shortcode-generator'; 
					
					if ($tab == 'shortcode-generator') {
						echo "<p>".__("This shortcode generator sends query to Kirjastokaista server and asks for available media based on the options selected below.", "kirjastokaista")."</p>";
					}
					

					echo '<table class="form-table">';
					switch ( $tab ){
						case 'shortcodes' :
							$settings = get_option( "kirjastokaista_settings" );
							/* Remove saved shortcode action */
							if (isset($_GET['remove'])) {
								if (is_numeric($_GET['remove'])) {
									$removeId = intval($_GET['remove']);
									unset($settings['shortcodes'][$removeId]);
									$updated = update_option( "kirjastokaista_settings", $settings );
								}
							}
							/* Show remaining shortcodes */
							foreach ($settings['shortcodes'] as $key => $value) {
							?>
							<tr>
								<th valign="top"><br />
									[kirjastokaista id="<?php echo $key; ?>"]<br /><br />
									<a href="#" class="removekey" data-id="<?php echo $key; ?>"><i class="fa fa-times"> <?php _e('Remove', 'kirjastokaista'); ?></i></a>
								</th>
								<td valign="top"><strong><?php echo $value['name']; ?></strong><br /><i><?php echo $value['description']; ?></i><br /><?php _e('Translates to ', 'kirjastokaista'); ?><br /><?php echo $value['shortcode']; ?><?php if (isset($value['cached'])) {
									if ($value['cached'] == 1) {
										?><br /><strong><?php _e('Cached content', 'kirjastokaista'); ?></strong><?php
									}
								} ?></td>
							</tr>
							<?php
							}
						break; 
						case 'plugin-info' :
							
							?>
							<tr>
								<th><?php echo _e('Version number', 'kirjastokaista'); ?></th>
								<td><?php echo get_option( "kirjastokaista_version" ) ?></td>
							</tr>
						</table>

						<?php include "kirjastokaista-plugininfo-text.php" ; ?>

						<table>
							<?php
						break; 
						case 'settings' :
							?>
							<tr>
								
								<th><label for="kirjastokaista_show_embed_editor"><?php _e('Show Kirjastokaista embed button on content editor', 'kirjastokaista'); ?></label></th>
								<td>
									<input id="kirjastokaista_show_embed_editor" name="kirjastokaista_show_embed_editor" type="checkbox" <?php if ( isset($settings["kirjastokaista_show_embed_editor"] )) echo 'checked="checked"'; ?> value="true" /> 
									<span class="description"><?php echo _e('Yes', 'kirjastokaista'); ?></span>
								</td>
							</tr>
							
							<tr>
								<th><label for="kirjastokaista_allow_text_widget"><?php _e('Allow Kirjastokaista and other shortcodes to run in Text Widget', 'kirjastokaista'); ?></label></th>
								<td>
									<input id="kirjastokaista_allow_text_widget" name="kirjastokaista_allow_text_widget" type="checkbox" <?php if ( isset($settings["kirjastokaista_allow_text_widget"] )) echo 'checked="checked"'; ?> value="true" /> 
									<span class="description"><?php echo _e('Yes', 'kirjastokaista'); ?></span>
								</td>
							</tr>

							<tr>
								<th><label for="kirjastokaista_use_jcarousel"><?php _e('Use jCarousel for Slideshows', 'kirjastokaista'); ?><!--<br /><a href="#">(<?php _e('more info', 'kirjastokaista'); ?></a>)--></label></th>
								<td>
									<input id="kirjastokaista_use_jcarousel" name="kirjastokaista_use_jcarousel" type="checkbox" <?php if ( isset($settings["kirjastokaista_use_jcarousel"] )) echo 'checked="checked"'; ?> value="true" /> 
									<span class="description"><?php echo _e('Yes', 'kirjastokaista'); ?></span>
								</td>
							</tr>

							
							<tr>
								<td style="padding: 0;">
								<p class="submit" style="clear: both;">
									<input type="submit" name="Submit"  class="button-primary" value="<?php echo _e('Update Settings', 'kirjastokaista'); ?>" />
									<input type="hidden" name="kirjastokaista-settings-submit" value="Y" />
								</p>
								</td>
							</tr>
							<?php
						break; 
						case 'shortcode-generator' : 
						
						global $kirjastokaista_ui_list;
						
						global $kirjastokaista_medialanguages;
						
						global $kirjastokaista_mediatypes;
						
						

							?>

							<tr>
								<th><label for="kirjastokaista_ui_class"><?php echo _e('User Interface', 'kirjastokaista'); ?></label></th>
								<td>
									<select id="kirjastokaista_tools_ui">
									<?php foreach ($kirjastokaista_ui_list as $ui_key => $ui_value) { ?>
										<option value="<?php echo $ui_key; ?>"><?php echo $ui_key; ?> - <?php echo $ui_value['name']; ?></option>
									<?php } ?>
									</select>
								</td>
							</tr>
							
							<tr>
								<th><label for="kirjastokaista_categories"><?php echo _e('Categories', 'kirjastokaista'); ?></label></th>
								<td>
									<button id="open_modal_choose_categories"><?php echo _e('Choose Categories', 'kirjastokaista'); ?></button>
									<div id="chosen_categories"></div>
								</td>
							</tr>


							<tr>
								<th><label for="kirjastokaista_mediatypes"><?php echo _e('Media Types', 'kirjastokaista'); ?></label></th>
								<td>
									<?php foreach ($kirjastokaista_mediatypes as $kirjastokaista_mediatype_key => $kirjastokaista_mediatype_value) { ?>
									<label><input class="kirjastokaista_mediatypes" type="checkbox" value="<?php echo $kirjastokaista_mediatype_key; ?>" checked /> 
									<span class="description"><?php echo $kirjastokaista_mediatype_value; ?></span></label>
									<br />
									<?php } ?>
								</td>
							</tr>
							
							<tr>
								<th><label for="kirjastokaista_medialanguages"><?php echo _e('Media Languages', 'kirjastokaista'); ?></label></th>
								<td>
									<?php foreach ($kirjastokaista_medialanguages as $kirjastokaista_medialanguage_key => $kirjastokaista_medialanguage_value) { ?>
									<label><input class="kirjastokaista_medialanguages" type="checkbox" value="<?php echo $kirjastokaista_medialanguage_key; ?>" checked /> 
									<span class="description"><?php echo $kirjastokaista_medialanguage_value; ?></span></label>
									<br />
									<?php } ?>
								</td>
							</tr>

							<tr>
								<th><label for="kirjastokaista_layout"><?php echo _e('Layout', 'kirjastokaista'); ?></label></th>
								<td>
									<div class="kirjastokaista_layout_radios">
										<label>
										<i class="fa fa-list"></i><br />
										<input type="radio" name="kirjastokaista_tools_layout" class="kirjastokaista_tools_layout" value="list" checked /><br />List</label>
									</div>
									<div class="kirjastokaista_layout_radios">
										<label>
										<i class="fa fa-picture-o"></i><br />
										<input type="radio" name="kirjastokaista_tools_layout" class="kirjastokaista_tools_layout" value="thumbnail" /><br />Thumbnail</label>
									</div>
									<div class="kirjastokaista_layout_radios">
										<label>
										<i class="fa fa-play-circle-o"></i><br />
										<input type="radio" name="kirjastokaista_tools_layout" class="kirjastokaista_tools_layout" value="embed" /><br />Embed</label>
									</div>
									<div class="kirjastokaista_layout_radios">
										<label>
										<i class="fa fa-forward"></i><br />
										<input type="radio" name="kirjastokaista_tools_layout" class="kirjastokaista_tools_layout" value="slideshow" /><br />Slideshow</label>
									</div>
									<div style="clear:both;"></div>
									
									<div id="slideshow_options">
										<label>
											<input type="checkbox" id="kirjastokaista_slideshow_enable_caption" checked /> <?php echo _e('Enable caption text', 'kirjastokaista'); ?>
										</label><br />
										<label>
											<input type="checkbox" id="kirjastokaista_slideshow_enable_kirjastokaistatext" checked /> <?php echo _e('Enable "from Kirjastokaista" text', 'kirjastokaista'); ?>
										</label><br />
									</div>
									<div id="layout_options">
										<label>
											<input type="checkbox" id="kirjastokaista_layout_newwindow" checked /> <?php echo _e('Open link in new window', 'kirjastokaista'); ?>
										</label><br />										
									</div>
								</td>
							</tr>
							
							<tr>
								<th><label for="kirjastokaista_orderby"><?php echo _e('Order by', 'kirjastokaista'); ?></label></th>
								<td>
									<select name="kirjastokaista_tools_orderby" id="kirjastokaista_tools_orderby">
										<option value="newest"><?php echo _e('Latest', 'kirjastokaista'); ?></option>
										<option value="mostpopular"><?php echo _e('Most Popular', 'kirjastokaista'); ?></option>
										<option value="random"><?php echo _e('Random', 'kirjastokaista'); ?></option>
									</select>
									<div id="kirjastokaista_tools_orderby_notice"></div>
								</td>
							</tr>
							
							<tr>
								<th><label for="kirjastokaista_results"><?php echo _e('Results', 'kirjastokaista'); ?></label></th>
								<td>
									<input type="number" size="4" value="5" id="kirjastokaista_tools_results" />
								</td>
							</tr>


							
							<tr>
								<th></th>
								<td>
									<button id="open_modal_view_results"><?php echo _e('View Shortcode Results', 'kirjastokaista'); ?></button>
									<button id="open_modal_generate_shortcode"><?php echo _e('Generate Shortcode', 'kirjastokaista'); ?></button>
								</td>
								
							</tr>
							<?php
						break;
					}
					echo '</table>';
				}
				?>
				
				
			</form>
			
			<!--<p>&copy; Copyright here</p>-->
		</div>

	</div>
<?php
}


?>