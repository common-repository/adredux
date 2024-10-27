<?php
/*
Plugin Name: AdRedux - Insert Ads & Codes
Plugin URI: https://reduxthemes.com/adredux-wordpress-ad-plugin/
Description: Easily insert advertisements and codes such as Google Analytics & Google AdSense in your website. Automatically insert ads within posts. Add analytics codes, custom codes, newsletter forms, image banners, meta tags, verification codes, etc.
Version: 1.3.5
Author: ReduxThemes.com
Author URI: https://reduxthemes.com/
Text Domain: adredux
*/

/*
Copyright 2017 XtraPunch.com (https://xtrapunch.com)
Author - Team XtraPunch.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Exit if File Accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	die( "Cheating! You are not allowed to access this page directly." );
}

/**
 * Constants
 */
if( ! defined( 'ADREDUX_VERSION' ) ) {
	// Plugin version
	define( 'ADREDUX_VERSION', '1.1.0' );
}
if( ! defined( 'ADREDUX_DIR' ) ) {
	// Plugin directory path
	define( 'ADREDUX_DIR', plugin_dir_path( __FILE__ ) );
}
if( ! defined( 'ADREDUX_DIR_URI' ) ) {
	// Plugin directory URL
	define( 'ADREDUX_DIR_URI', plugin_dir_url( __FILE__ ) );
}
if( ! defined( 'ADREDUX_SETTINGS' ) ) {
	// Plugin settings name
	define( 'ADREDUX_SETTINGS', 'adredux_settings' );
}


include_once( ADREDUX_DIR .'defaults.php');
include_once( ADREDUX_DIR .'adredux_displayads.php');
include_once( ADREDUX_DIR .'admin/settings-page.php');

class Adredux_Plugin {
	
	public function __construct() {

		// Plugin activation
		register_activation_hook(__FILE__, array($this, 'adredux_plugin_activation'));

		// Plugin deactivation
		register_deactivation_hook(__FILE__, array( $this, 'adredux_plugin_deactivation'));
		
		// Activation notice
		add_action( 'admin_notices', array( $this, 'welcome_admin_notice' ) );

		// Add Settings and Fields
		add_action( 'admin_init', array( $this, 'adredux_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'adredux_setup_fields' ) );
		add_action( 'wp_enqueue_scripts', array( $this,'adredux_load_scripts') );
		add_action('wp_head', array($this, 'adredux_header_codes') );
		add_action('wp_head', array($this, 'adredux_pagelevel_adcode') );
		add_action( 'wp_body_open', array($this, 'adredux_body_codes') );
		add_action('wp_footer', array($this, 'adredux_footer_codes') );
		
		//add_action( 'admin_notices', array( $this, 'welcome_admin_notice' ) );

		// Real Magic that Inserts Ads the Right Way 
		// add the filter when main loop starts
		add_action( 'loop_start', function( WP_Query $query ) {
		   if ( $query->is_main_query() ) {
		     add_filter( 'the_content', 'adredux_insert_adonpage', 99 );
		   }
		} );
		
		// remove the filter when main loop ends
		add_action( 'loop_end', function( WP_Query $query ) {
		   if ( has_filter( 'the_content', 'adredux_insert_adonpage' ) ) {
		     remove_filter( 'the_content', 'adredux_insert_adonpage' );
		   }
		} );

	}

	public function adredux_load_scripts() {
		
		$stylesheet = adredux_get_option('adredux_stylesheet')[0];
		if ( isset ($stylesheet) && ($stylesheet == 'no') ) return;

		wp_register_style( 'adredux-adstyles', plugins_url( '/css/adstyles.css', __FILE__ ) );
    		wp_enqueue_style( 'adredux-adstyles' );

	}

	public function adredux_header_codes() {
		
		$headercode = adredux_get_option('adredux_header_codes', '');
		if ( isset ($headercode) && ($headercode != '') ){
			echo "\n";
			_e ( '<!--AdRedux Header Codes-->', 'adredux');
			echo "\n";
			echo $headercode;
			echo "\n";
		}

	}

	public function adredux_footer_codes() {
		
		$footercode = adredux_get_option('adredux_footer_codes');
		if ( !isset ($footercode) || ($footercode == '') ) return;

		echo "\n";
		_e ( '<!--AdRedux Footer Codes-->', 'adredux');
		echo "\n";
		echo $footercode;
		echo "\n";

	}

	public function adredux_body_codes() {
		
		$bodycode = adredux_get_option('adredux_body_codes');
		if ( !isset ($bodycode) || ($bodycode == '') ) return;

		echo "\n";
		_e ( '<!--AdRedux Body Open Codes-->', 'adredux');
		echo "\n";
		echo $bodycode;
		echo "\n";

	}


	public function adredux_pagelevel_adcode() {
		
		$pagelevelad = adredux_get_option('adredux_pagelevel_adcode', '');

		if ( !isset ($pagelevelad) || ($pagelevelad == '') ){
			return;
		} else {

			$showads = adredux_get_option('adredux_showads')[0];
			$ad_wordcount = adredux_get_option('ad_words','0');

			if ( isset ($showads) && ($showads == '0') ) {
				return;
			} elseif ( isset ($showads) && ($showads == '1') && is_user_logged_in() ) {
				return;
			} elseif ( isset ($showads) && ($showads == '2') && is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
				return;
			} else {
			
				$ad_page = adredux_get_option('ad_page')[0];
				$exclude_categories = adredux_get_option('exclude_categories', array('0'));
				$exclude_tags = adredux_get_option('exclude_tags', array('0'));
							
				if ( (is_singular()) && ( ($ad_page == 'posts') || ($ad_page == 'both') ) && (!in_category($exclude_categories)) && (!has_tag($exclude_tags)) ){
					$word_count = str_word_count(get_the_content());
					if ( $word_count < $ad_wordcount ) {
						return;
					} else {
						echo "\n";
						_e ( '<!--AdRedux Page-Level Ad Code-->', 'adredux');
						echo "\n";
						echo $pagelevelad;
						echo "\n";
					}
				} elseif ( (is_page()) && ( ($ad_page == 'pages') || ($ad_page == 'both') ) ) {
					$word_count = str_word_count(get_the_content());
					if ( $word_count < $ad_wordcount ) {
						return;
					} else {
						echo "\n";
						_e ( '<!--AdRedux Page-Level Ad Code-->', 'adredux');
						echo "\n";
						echo $pagelevelad;
						echo "\n";
					}
				} else {
					return;
				}
		
			}
		
		}
		
	}		


	public function adredux_setup_sections() {

		add_settings_section( 'adredux_insert_codes', __('Insert Codes in Head, Body & Footer', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_general_settings', __('Ad Settings', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_pagelevel_ads', __('Page-Level Ad Codes', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_one', __('Advertisement #1', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_two', __('Advertisement #2', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_three', __('Advertisement #3', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_four', __('Advertisement #4', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_five', __('Advertisement #5', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_six', __('Advertisement #6', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_seven', __('Advertisement #7', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );
		add_settings_section( 'adredux_adcode_eight', __('Advertisement #8', 'adredux'), array( $this, 'adredux_section_callback' ), ADREDUX_SETTINGS );

	}
	
	public function adredux_section_callback( $arguments ) {
		
		switch( $arguments['id'] ){
			case 'adredux_general_settings':
				echo __('Set up your advertisement display preferences for the website.<hr/>', 'adredux');
				break;
			case 'adredux_insert_codes':
				echo __('Add the codes, styles and scripts. Use for Google Analytics, Google Tags, etc. Displayed on all pages of the website. For Google AdSense and advertising codes, use the Advertising Code instead.<hr/>', 'adredux');
				break;
			case 'adredux_pagelevel_ads':
			case 'adredux_adcode_one':
			case 'adredux_adcode_two':
			case 'adredux_adcode_three':
			case 'adredux_adcode_four':
			case 'adredux_adcode_five':
			case 'adredux_adcode_six':
			case 'adredux_adcode_seven':
			case 'adredux_adcode_eight':
				echo __('Please add the advertisement codes (eg: Google Adsense) to start showing the ads.<hr/>', 'adredux');
				break;
		}

	}

	public function adredux_setup_fields() {

		// WordPress Categories via an Array
		$adredux_categories = array();  
		$categories = get_categories('hide_empty=0&orderby=name');
		$adredux_categories[0] = __(' - Select - ', 'adredux');
		foreach ($categories as $category_list) {
			$adredux_categories[$category_list->cat_ID] = $category_list->cat_name;
		}

		// WordPress Tags via an Array
		$adredux_tags = array();  
		$tags = get_tags('hide_empty=0&orderby=name');
		$adredux_tags[0] = __(' - Select - ', 'adredux');
		foreach ($tags as $tag_list) {
			$adredux_tags[$tag_list->term_id] = $tag_list->name;
		}
		
		$fields = array(

			array(
				'uid' => 'adredux_header_codes',
				'label' => __('Insert Code in Header', 'adredux'),
				'section' => 'adredux_insert_codes',
				'type' => 'textarea',
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Insert common styles and scripts (eg: <strong>Google Analytics, Tags</strong>) in the <strong> &lt;head&gt;</strong> tag, across the website.', 'adredux'),
				'default' => ''
			),
										
			array(
				'uid' => 'adredux_body_codes',
				'label' => __('Insert Code in Body', 'adredux'),
				'section' => 'adredux_insert_codes',
				'type' => 'textarea',
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Insert common scripts (eg: <strong>Google Tags</strong>) just after the opening of the <strong>&lt;body&gt;</strong> tag, across the website.', 'adredux'),
				'default' => ''
			),

			array(
				'uid' => 'adredux_footer_codes',
				'label' => __('Insert Code in Footer', 'adredux'),
				'section' => 'adredux_insert_codes',
				'type' => 'textarea',
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Insert common scripts for output just before the closing <strong>&lt;/body&gt;</strong> tag, across the website.', 'adredux'),
				'default' => ''
			),
						
			array(
				'uid' => 'ad_page',
				'label' => __('Display Ads On:', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'select',
				'options' => array(
					'posts' => __('Posts', 'adredux'),
					'pages' => __('Pages', 'adredux'),
					'both' => __('Both Posts & Pages', 'adredux'),
				),
				'placeholder' => '',
				'helper' => '',
				'supplimental' => '',
				'default' => array('posts')
			),
			
			array(
				'uid' => 'exclude_categories',
				'label' => __('Hide Ads on Categories', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'multiselect',
				'options' => $adredux_categories,
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Select the post categories to exclude from displaying ads.', 'adredux'),
				'default' => array('')
			),

			array(
				'uid' => 'exclude_tags',
				'label' => __('Hide Ads on Tags', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'multiselect',
				'options' => $adredux_tags,
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Select the post tags to exclude from displaying ads.', 'adredux'),
				'default' => array('')
			),

			array(
				'uid' => 'exclude_postids',
				'label' => __('Hide Ads on Specific Post & Page IDs', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'text',
				'placeholder' => '1,2,3',
				'helper' => '',
				'supplimental' => __('Add comma-separated list of post & page IDs to exclude from displaying ads.', 'adredux'),
				'default' => ''
			),
									
			array(
				'uid' => 'adredux_stylesheet',
				'label' => __('Ad Style', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'radio',
				'options' => array(
					'no' => __('Disable Plugin Styles', 'adredux'),
					'yes' => __('Enable Plugin Styles', 'adredux'),
				),
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Select the <strong>disable</strong> option if you want to use custom styles your advertisement blocks.', 'adredux'),
				'default' => array('yes')
			),
			
			array(
				'uid' => 'adredux_showads',
				'label' => __('Auto-Insert Ads', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'radio',
				'options' => array(
					'0' => __('Disable Ads', 'adredux'),
					'1' => __('Disable Ads for All Logged-in Users', 'adredux'),
					'2' => __('Disable Ads for Authors, Editors & Admin Users (Subscribers Excluded)', 'adredux'),
					'3' => __('Show Ads for Everyone', 'adredux'),
				),
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('By default, <strong>ads not shown to logged-in users</strong>, including subscribers, authors, editors & admins.', 'adredux'),
				'default' => array('1')
			),

			array(
				'uid' => 'ad_words',
				'label' => __('Minimum Word Count for Showing Ads', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'text',
				'placeholder' => '0',
				'helper' => '',
				'supplimental' => __('Set the minimum <strong>word count for displaying ads</strong> on posts and pages, including page-level ads. By default, ads are shown on all pages.', 'adredux'),
				'default' => '0'
			),
		
			array(
				'uid' => 'min_words',
				'label' => __('Minium Word Count for Mid Content Ad', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'text',
				'placeholder' => '300',
				'helper' => '',
				'supplimental' => __('Set the minimum <strong>word count for displaying ads</strong> in the middle of the articles. By default, we use <strong>300</strong> words.', 'adredux'),
				'default' => '300'
			),
		
			array(
				'uid' => 'min_paras',
				'label' => __('Paras for Mid Content Ad', 'adredux'),
				'section' => 'adredux_general_settings',
				'type' => 'text',
				'placeholder' => '5',
				'helper' => '',
				'supplimental' => __('Set the minimum number of <strong>paragraphs for displaying ads</strong> in the middle of the articles. By default, we use <strong>5</strong> paras.', 'adredux'),
				'default' => '5'
			),
			
		);

		foreach( $fields as $field ){
			
			add_settings_field( ADREDUX_SETTINGS.'['.$field['uid'].']', $field['label'], array( $this, 'field_callback' ), ADREDUX_SETTINGS, $field['section'], $field );
			
		}

		//Adsense Page_level Ad Code Field
		$adfields[] =
			array(
				'uid' => 'adredux_pagelevel_adcode',
				'label' => __('Page-Level Ads & Codes', 'adredux'),
				'section' => 'adredux_pagelevel_ads',
				'type' => 'textarea',
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Insert <strong>Google Adsense Page-Level Ads</strong> and advertisement script / JS codes to be included within <strong>&lt;head&gt;</strong>.', 'adredux'),
				'default' => ''
			);
			
		// Define the Number of Ad Slots Via Array
		// Must Be Same Number As the Number of Sections Defined Earlier
		$adslots = array('one','two','three','four', 'five','six','seven', 'eight');
						
		foreach ($adslots as $adslot) {
		
			$adfields[] = 
			array(
				'uid' => 'adcode_'.$adslot,
				'label' => __('Ad Code', 'adredux'),
				'section' => 'adredux_adcode_'.$adslot,
				'type' => 'textarea',
				'placeholder' => '',
				'helper' => '',
				'supplimental' => __('Add the advertisement code here. Do <strong>NOT</strong> include <code>&lt;p&gt&lt;/p&gt</code> in any of the ad codes when using the second para ad location.', 'adredux'),
				'default' => '',
			);

			$adfields[] =							
			array(
				'uid' => 'adcode_location_'.$adslot,
				'label' => __('Display Location', 'adredux'),
				'section' => 'adredux_adcode_'.$adslot,
				'type' => 'select',
				'options' => array(
					'before-content' => __('Before Content', 'adredux'),
					'after-para-one' => __('Content - After First Paragraph', 'adredux'),
					'after-para-two' => __('Content - After Second Paragraph', 'adredux'),
					'after-para-three' => __('Content - After Third Paragraph', 'adredux'),
					'after-para-secondlast' => __('Content - After Second Last Paragraph', 'adredux'),
					'mid-content' => __('Mid Content', 'adredux'),
					'after-content' => __('After Content', 'adredux'),
				),
				'placeholder' => '',
				'helper' => '',
				'supplimental' => '',
				'default' => array('before-content')
			);
		
			$adfields[] =
			array(
				'uid' => 'adcode_align_'.$adslot,
				'label' => __('Alignment', 'adredux'),
				'section' => 'adredux_adcode_'.$adslot,
				'type' => 'select',
				'options' => array(
					'alignleft' => __('Left', 'adredux'),
					'alignright' => __('Right', 'adredux'),
					'aligncenter' => __('Center', 'adredux'),
					'leftright' => __('Random (Left/Right)', 'adredux'),
					'random' => __('Random (All)', 'adredux'),
				),
				'placeholder' => '',
				'helper' => '',
				'supplimental' => '',
				'default' => array('center')
			);

			$adfields[] = 
			array(
				'uid' => 'adcode_maxwidth_'.$adslot,
				'label' => __('Max Width', 'adredux'),
				'section' => 'adredux_adcode_'.$adslot,
				'type' => 'text',
				'placeholder' => '',
				'helper' => __('Pixels', 'adredux'),
				'supplimental' => __('Set the <strong>maximum width (400px recommended)</strong> for responsive ads. Leave blank to use container width.', 'adredux'),
				'default' => ''
			);
		
		}
			
		//var_dump($adfields);
		
		foreach( $adfields as $key=>$adfield ){		
			add_settings_field( ADREDUX_SETTINGS.'['.$adfield['uid'].']', $adfield['label'], array( $this, 'field_callback' ), ADREDUX_SETTINGS, $adfield['section'], $adfield );		
		}

		register_setting( 'adredux_settings', ADREDUX_SETTINGS );
		register_setting( 'adredux_settings_ads', ADREDUX_SETTINGS );
			
	}
	
	public function field_callback( $arguments ) {
		
		$settings = get_option( ADREDUX_SETTINGS );

		if ( isset( $settings[$arguments['uid']] ) ) {

			$value = $settings[$arguments['uid']];
		
		} else {
			
			$value = $arguments['default'];

		}

		switch( $arguments['type'] ){
			case 'password':
			case 'text':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'textarea':
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $arguments['placeholder'], $value );
				break;
			case 'select':
			case 'multiselect':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$attributes = '';
					$options_markup = '';
					foreach( $arguments['options'] as $key => $label ){
						$options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, false ) ], $key, false ), $label );
					}
					if( $arguments['type'] === 'multiselect' ){
						$attributes = ' multiple="multiple" ';
					}
					printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $attributes, $options_markup );
				}
				break;
			case 'radio':
			case 'checkbox':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$options_markup = '';
					$iterator = 0;
					foreach( $arguments['options'] as $key => $label ){
						$iterator++;
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
					}
					printf( '<fieldset>%s</fieldset>', $options_markup );
				}
				break;
			case 'hidden':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" /><p>%4$s</p>', ADREDUX_SETTINGS.'['.$arguments['uid'].']', $arguments['type'], $arguments['placeholder'], $value );
				break;
		}
		if( $helper = $arguments['helper'] ){
			printf( '<span class="helper"> %s</span>', $helper );
		}
		if( $supplimental = $arguments['supplimental'] ){
			printf( '<p class="description">%s</p>', $supplimental );
		}
	}


	/**
	 * Display a welcome notice when the plugin is activated.
	 *
	 * @access public
	 */
	public function welcome_admin_notice() {

		/* Check transient, if available display notice */
		if( get_transient( 'adredux-activated' ) ){
		
		$plugininfo = get_plugin_data( ADREDUX_DIR .'adredux.php'); ?>

		<div class="updated notice notice-success notice-alt is-dismissible">
			<p>
			<?php
			/* translators: %1$s and %2$s are placeholders that will be replaced by variables passed as an argument. */
			printf( wp_kses( __( 'Welcome to %1$s! To get started, visit the <a href="%2$s">settings page</a>.', 'adredux' ), array( 'a' => array( 'href' => array() ) ) ), esc_attr( $plugininfo['Name'] ), esc_url( admin_url( 'admin.php?page=adredux' ) ) ); ?>
			</p>
		</div><!-- .notice -->

		<?php
		}
		
		/* Delete transient, only display this notice once. */
		delete_transient( 'adredux-activated' );
	
	}


    /**
     * Run when the plugin is activated.
  	  *
	  * @access public
     */
	public function adredux_plugin_activation() {

		/* Create transient data */
		set_transient( 'adredux-activated', true, 15 );
		
		do_action( 'adredux_activation' );
		
	}

	
	/**
	 * Run when the plugin is deactivated.
	 *
	 * @access public
	 */
	public function adredux_plugin_deactivation() {
		
		/* Delete plugin settings when deactivated */
		// delete_option(ADREDUX_SETTINGS);  

	}

}
new Adredux_Plugin();