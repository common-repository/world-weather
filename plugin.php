<?php
/*
Plugin Name: World Weather Widget
Plugin URI: http://blog.titacgs.com.ar/world-weather-plugin-para-wp/
Description: This plugin/widget allows you to insert weather status in your sidebar. It connects to World Weather Online to get weather forecasts.
Version: 2.1.1
Author: Vanessa Gutiérrez
Author URI: http://www.titacgs.com.ar/
License: This plugin is released under the GPLv2 license. The images packaged with this plugin are the property of
their respective owners, and do not, necessarily, inherit the GPLv2 license.
*/

/**
 * Register the Widget
 */
add_action('widgets_init', 'world_weather_widget_register');
function world_weather_widget_register() {
	register_widget('World_Weather_Widget');
}

//add_action( 'admin_init', 'wowe_js_admin_init' );
add_action('admin_print_scripts', 'wowe_js_admin_init');

 function wowe_js_admin_init() {
	/* Register our script. */
	// wp_register_script( 'myPluginScript', WP_PLUGIN_URL . '/myPlugin/script.js' );
	wp_enqueue_script( 'wowe', plugins_url('js/wowe.js', __FILE__) );
}


add_action('wp_ajax_get_location', 'get_location_ajax');
add_action('wp_ajax_nopriv_get_location', 'get_location_ajax');

function get_location_ajax() {
	$location = $_GET['location'];
	
	global $wpdb;
	$site_url = $wpdb->get_results('SELECT option_value FROM wp_options WHERE option_name = "widget_worldweather"');		
	$site_url = unserialize($site_url[0]->option_value);
	$apikey = $site_url[3]['apikey'];
	if(empty($apikey)) {
		$apikey = 'e9a33a76e4232506112002';
	}
	
	$url = 'http://free.worldweatheronline.com/feed/search.ashx?key='.$apikey.'&query='.urlencode($location).'&format=json';
	$wowe = wp_remote_get($url);
	$wowe = json_decode($wowe['body']);
	$wowe = $wowe->search_api->result;
	
	$html = '';
	foreach($wowe as $result) {
		$html.='<option value="'.urlencode($result->areaName[0]->value).','.urlencode($result->country[0]->value).'">'.$result->areaName[0]->value.', '.$result->country[0]->value.'</option>';
	}
	
	echo $html;
}

/**
 * The Widget Class
 */
 
if ( !class_exists('World_Weather_Widget') ) {
class World_Weather_Widget extends WP_Widget {
	
	function World_Weather_Widget() {
		$widget_ops = array( 'description' => __('Displays weather forecast', 'wowe') );
		$this->WP_Widget( 'worldweather', __('World Weather', 'wowe'), $widget_ops );
	}
	
	function wowe_fields_array( $instance = array() ) {
		
		return array(
			'wtitle' => array(
				'title' => __('Title', 'wowe'),
				'comment' => __('', 'wowe'),
				'ftype' => 'simple-input',
			),
			'apikey' => array(
				'title' => __('API Key', 'wowe'),
				'comment' => __('If you do not have an API Key go to http://www.worldweatheronline.com/register.aspx and sign up to get one.', 'wowe'),
				'ftype' => 'simple-input',
			),
			'flocation' => array(
				'title' => __('Find Location', 'wowe'),
				'comment' => __('Search by City or Town name.', 'wowe'),
				'ftype' => 'search-input',
				'blabel' => __('Search', 'wowe'),
			),
			'slocation' => array(
				'title' => __('Select Location', 'wowe'),
				'comment' => __('Select one location from the results available.', 'wowe'),
				'ftype' => 'select',
			),
			'scale' => array(
				'title' => __('Select Scale', 'wowe'),
				'comment' => __('Choose between Celsius and Fahrenheit.', 'wowe'),
				'ftype' => 'select-scale',
			),
			'icons' => array (
				'title' => __('Show icons', 'wowe'),
				'comment' => __('Display small images with weather info.'),
				'ftype' => 'select-icon',
			),
			'days' => array(
				'title' => __('Number of Days', 'wowe'),
				'comment' => __('Choose between 1 and 5.', 'wowe'),
				'ftype' => 'select-day',
			),
			'paypal' => array(
				'title' => __('Donate', 'wowe'),
				'comment' => __('If you like this plugin, please donate!'),
				'ftype' => 'paypal-button',
			)
		);
	}

	function widget($args, $instance) {
		
		extract($args);
		
		$instance = wp_parse_args($instance, array(
			'title' => '',
			'icon_set' => 'default',
			'size' => '48x48'
		));
		
		echo $before_widget;
		
		
		if ( !empty( $instance['wtitle'] ) )
			echo $before_title . $instance['wtitle'] . $after_title;
			
		if ( empty($instance['apikey']) )
			$instance['apikey'] = 'e9a33a76e4232506112002';
			
		$url = 'http://free.worldweatheronline.com/feed/weather.ashx?q='.$instance['slocation'].'&format=json&key='.$instance['apikey'].'&num_of_days='.$instance['days'];
		
		$wowe = wp_remote_get($url);
		$wowe = json_decode($wowe['body']);
	
		if($instance['scale'] == 'c') {
			if($instance['days'] == 1) {
?>
                <div id="temp_actual" >
					<?php
						if($instance['icons'] == 'y') echo '<img src="'.$wowe->data->weather[0]->weatherIconUrl[0]->value.'" width="25" style="float:left;margin:0px 10px 0px 0px"/>';
						echo '<p>'.$wowe->data->current_condition[0]->temp_C;
					?> °C</p>
				</div>
                <div id="temp_min">Min <?php echo $wowe->data->weather[0]->tempMinC; ?> °C</div>
                <div id="temp_max">Max <?php echo $wowe->data->weather[0]->tempMaxC; ?> °C</div>                
<?php
			}
			else {
?>
				<div id="temp_actual" >
					<?php
						if($instance['icons'] == 'y') echo '<img src="'.$wowe->data->weather[0]->weatherIconUrl[0]->value.'" width="25" style="float:left;margin:0px 10px 0px 0px"/>';
						echo '<p>'.$wowe->data->current_condition[0]->temp_C;
					?> °C</p>
				</div>
                
                <table>
                	<tr>
                    	<td width="45px">Date</td>
                    	<?php if($instance['icons'] == 'y') echo '<td width="45px"></td>';?>
                        <td width="45px">Min.</td>
                        <td width="45px">Max.</td>
                    </tr>
<?php
				for($i = 0; $i < $instance['days']; $i++) {
					$d = preg_split('/-/', $wowe->data->weather[$i]->date);
?>
					<tr>
						<td style="vertical-align:middle;">
							<?php 
								echo $d[1].'/'.$d[2]; 
								if($instance['icons'] == 'y') echo '<td style="vertical-align:middle;"><img src="'.$wowe->data->weather[$i]->weatherIconUrl[0]->value.'" width="25" /></td>';
							?>
						</td>
	                    <td style="vertical-align:middle;"><?php echo $wowe->data->weather[$i]->tempMinC; ?> °C</td>
						<td style="vertical-align:middle;"><?php echo $wowe->data->weather[$i]->tempMaxC; ?> °C</td>
                    </tr>
<?php
				}
?>
				</table>
<?php
			}
		}
		else {
			if($instance['days'] == 1) {
?>
                <div id="temp_actual" >
					<?php 
						if($instance['icons'] == 'y') echo '<img src="'.$wowe->data->weather[0]->weatherIconUrl[0]->value.'" width="25" style="float:left;margin:0px 10px 0px 0px;"/>';
						echo '<p>'.$wowe->data->current_condition[0]->temp_F;
					?> °F</p>
				</div>
                <div id="temp_min">Min <?php echo $wowe->data->weather[0]->tempMinF; ?> °F</div>
                <div id="temp_max">Max <?php echo $wowe->data->weather[0]->tempMaxF; ?> °F</div>                
<?php
			}
			else {
?>
				<div id="temp_actual" >
					<?php 
						if($instance['icons'] == 'y') echo '<img src="'.$wowe->data->weather[0]->weatherIconUrl[0]->value.'" width="25" style="float:left;margin:0px 10px 0px 0px;"/>';
						echo '<p>'.$wowe->data->current_condition[0]->temp_F;
					?> °F</p>
				</div>
                
                <table>
                	<tr>
                    	<td width="45px">Date</td>
                    	<?php if($instance['icons'] == 'y') echo '<td width="45px"></td>';?>
                        <td width="45px">Min.</td>
                        <td width="45px">Max.</td>
                    </tr>
<?php
				for($i = 0; $i < $instance['days']; $i++) {
					$d = preg_split('/-/', $wowe->data->weather[$i]->date);
?>
					<tr>
						<td style="vertical-align:middle;">
							<?php 
								echo $d[1].'/'.$d[2]; 
								if($instance['icons'] == 'y') echo '<td style="vertical-align:middle;"><img src="'.$wowe->data->weather[$i]->weatherIconUrl[0]->value.'" width="25" /></td>';
							?>
						</td>
	                    <td style="vertical-align:middle;"><?php echo $wowe->data->weather[$i]->tempMinF; ?> °F</td>
						<td style="vertical-align:middle;"><?php echo $wowe->data->weather[$i]->tempMaxF; ?> °F</td>
                    </tr>
<?php
				}
?>
				</table>
<?php
			}
		}		
		echo $after_widget;	
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
		global $wpdb;
		
		$site_url = $wpdb->get_results('SELECT option_value FROM wp_options WHERE option_name = "siteurl"');		
		$site_url = $site_url[0]->option_value;
		
		$instance = wp_parse_args($instance, array(
			'title' => '',
		) );
?>		
		<p><?php _e('Enter your API Key and select your location.', 'wowe'); ?></p>
		
<?php
		foreach ( $this->wowe_fields_array( $instance ) as $key => $data ) {
			echo '<p>';
			printf( '<label for="%s"> %s: %s</label><br/> ', esc_attr( $this->get_field_id($key) ), esc_attr( $data['title'] ), esc_attr( $data['comment'] ) );
			switch($data['ftype']) {
				case 'simple-input':
					printf( '<input id="%s" name="%s" value="%s" style="%s" />', esc_attr( $this->get_field_id($key) ), esc_attr( $this->get_field_name($key) ), esc_attr( $instance[$key] ), 'width:65%;' );
					break;
				case 'search-input':
					printf( '<input id="fweather" name="fweather" value="type a country" /> <input id="locsearch" type="button" value="%s" >', $data['blabel']);
					break;
				case 'select':
					echo '<select id="select-weather" name="'.esc_attr( $this->get_field_name($key) ).'" style="width:65%" >';
					echo '<option value="'.$instance[$key].'" selected="selected">'.$instance[$key].'</option>';
					echo '</select>';
					break;
				case 'select-day':
					echo '<select id="select-weather" name="'.esc_attr( $this->get_field_name($key) ).'" style="width:65%" >';
					?>
                    	<option value="1" <?php if($instance[$key] == 1) echo 'selected="selected"';?>>1</option>
                        <option value="2" <?php if($instance[$key] == 2) echo 'selected="selected"';?>>2</option>
                        <option value="3" <?php if($instance[$key] == 3) echo 'selected="selected"';?>>3</option>
                        <option value="4" <?php if($instance[$key] == 4) echo 'selected="selected"';?>>4</option>
                        <option value="5" <?php if($instance[$key] == 5) echo 'selected="selected"';?>>5</option>
                    <?php
					echo '</select>';
					break;
				case 'select-scale':
					echo '<select id="'.esc_attr( $this->get_field_name($key) ).'" name="'.esc_attr( $this->get_field_name($key) ).'" style="width:65%;" >';
					?>
						<option value="c" <?php if($instance[$key] == 'c') echo 'selected="selected"';?>>Celsius</option>
						<option value="f" <?php if($instance[$key] == 'f') echo 'selected="selected"';?>>Fahrenheit</option>
					<?php
					echo '</select>';
					break;
				case 'paypal-button':
					echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=QZTRL5AJ35EHG&lc=VE&item_name=World%20Weather%20Plugin&item_number=WP%2dPlugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"/></a><img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">';
					break;
				case 'select-icon':
					echo '<select id="'.esc_attr( $this->get_field_name($key) ).'" name="'.esc_attr( $this->get_field_name($key) ).'" style="width:65%;" >';
					?>
						<option value="y" <?php if($instance[$key] == 'y') echo 'selected="selected"';?>>Show</option>
						<option value="n" <?php if($instance[$key] == 'n') echo 'selected="selected"';?>>Hide</option>
					<?php
					echo '</select>';
					break;
			}
			echo '</p>' . "\n";
		}
	}
	
}
}
?>
