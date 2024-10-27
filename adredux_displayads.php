<?php

// Exit if File Accessed Directly
if ( ! defined( 'ABSPATH' ) ) {
	die( "Cheating! You are not allowed to access this page directly." );
}

function adredux_get_option($option, $default=NULL) {

	//Settings Name Must Be Same As Main File
	$adredux_settings_name = 'adredux_settings';

	$settings = get_option($adredux_settings_name);
	
	if ( isset($settings[$option]) )
		$value = $settings[$option];
	
	if ( ( isset($value) ) && ($value != '' ) ) {
		return $value;	
	} else {
		return $default;	
	}
	
}

function adredux_insert_adonpage($content) {

	$showads = adredux_get_option('adredux_showads')[0];

	$word_count = str_word_count($content);
	$ad_wordcount = adredux_get_option('ad_words','0');
	
	if ( ( isset ($showads) && ($showads == '0') ) || ($word_count < $ad_wordcount) ) {
		return $content;
	} elseif ( isset ($showads) && ($showads == '1') && is_user_logged_in() ) {
		return $content;
	} elseif ( isset ($showads) && ($showads == '2') && is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return $content;
	}
	
	
	$ad_page = adredux_get_option('ad_page')[0];
	$exclude_categories = adredux_get_option('exclude_categories', array('0'));
	$exclude_tags = adredux_get_option('exclude_tags', array('0'));
	$exclude_postids = adredux_get_option('exclude_postids', '');

	// Convert String to Array
	$exclude_postids_array = explode( ',', $exclude_postids );


	if ( is_singular( array('post') ) && ( ($ad_page == 'posts') || ($ad_page == 'both') ) && (!in_category($exclude_categories)) && (!has_tag($exclude_tags)) && !in_array(get_the_ID(), $exclude_postids_array) ){
		return adredux_insert_adverts($content);
	} elseif ( (is_page()) && ( ($ad_page == 'pages') || ($ad_page == 'both') ) && !in_array(get_the_ID(), $exclude_postids_array) ) {
		return adredux_insert_adverts($content);
	} else {
		return $content;
	}
	
}


function adredux_insert_adverts($content) {

		// Define the Number of Ad Slots Via Array
		// Must Be Same Number As the Number of Sections Defined Earlier
		$adslots = array('one','two','three','four', 'five','six','seven', 'eight');

		foreach ($adslots as $adcode) {

			$advert_code = adredux_get_option('adcode_'.$adcode);
			
			if ( isset($advert_code) && ($advert_code != '' ) ):
			
				$advert_code_location = adredux_get_option('adcode_location_'.$adcode)[0];
				$advert_code_align = adredux_get_option('adcode_align_'.$adcode)[0];
				$maxwidth = adredux_get_option('adcode_maxwidth_'.$adcode);
				if ( isset($maxwidth) && ($maxwidth != '' ) ) {
					$advert_code_maxwidth = ' style="max-width: '. $maxwidth . 'px;"';
				} else {
					$advert_code_maxwidth = '';				
				}
	
				if ($advert_code_align == 'leftright') {
	
					$array_align = array(
									'alignleft' => 'Left',
									'alignright'=> 'Right'
								);
					$advert_code_align = array_rand($array_align, 1);
	
				} elseif ($advert_code_align == 'random') {
	
					$array_align = array(
									'alignleft' => 'Left',
									'alignright'=> 'Right',
									'aligncenter'=> 'Center'
								);
					$advert_code_align = array_rand($array_align, 1);
	
				}
				
			
				$display_advert_code = '<div class="adredux '.$advert_code_location. ' '.$advert_code_align.'"><div class="adbox"'.$advert_code_maxwidth.'>'.$advert_code.'</div></div>';
				$content = adredux_insert_postads($content, $display_advert_code, $advert_code_location);
			
			endif;
		
		}
		
		return $content;

}


function adredux_insert_postads( $content, $adcode, $location) {

	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );
	$paragraph_count = count($paragraphs);

	$word_count = str_word_count($content);

	$midad_wordcount = adredux_get_option('min_words','300');
	$midad_paracount = adredux_get_option('min_paras','5');
		
	$midceil_paragraph_count = ceil($paragraph_count/2);

	if ($location == 'before-content') {
		$content = $adcode.$content;
	} elseif ($location == 'after-para-one') {
		$para_number = 1;
		$content = adredux_insert_afterparagraph( $adcode, $para_number, $content );
	} elseif ($location == 'after-para-two') {
		$para_number = 2;
		$content = adredux_insert_afterparagraph( $adcode, $para_number, $content );
	} elseif ($location == 'after-para-three') {
		$para_number = 3;
		$content = adredux_insert_afterparagraph( $adcode, $para_number, $content );
	} elseif ($location == 'after-para-secondlast') {
		$para_number = ($paragraph_count - 2);
		$content = adredux_insert_afterparagraph( $adcode, $para_number, $content );
	} elseif (($location == 'mid-content') && ($paragraph_count >= $midad_paracount) && ($word_count >= $midad_wordcount)){
		$para_number = $midceil_paragraph_count;
		$content = adredux_insert_afterparagraph( $adcode, $para_number, $content );
	} elseif ($location == 'after-content') {
		$content = $content.$adcode;		
	}

	return $content;

}
 
// Parent Function that makes the magic happen
 
function adredux_insert_afterparagraph( $insertion, $paragraph_id, $content ) {

	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );

	foreach ($paragraphs as $index => $paragraph) {

		if ( trim( $paragraph ) ) {
			$paragraphs[$index] .= $closing_p;
		}

		if ( $paragraph_id == $index+1 ) {
			$paragraphs[$index] .= $insertion;
		}
	}
	
	return implode( '', $paragraphs );

}

/* 
 Empty Paragraph Fix - Clean Empty Paragraphs
 Source: http://www.johannheyne.de/wordpress/shortcode-empty-paragraph-fix/
*/
function adredux_fix_empty_paras($content){   
        $array = array (
            '<p>[' => '[', 
            ']</p>' => ']', 
            ']<br />' => ']'
        );

        $content = strtr($content, $array);

	return $content;
}