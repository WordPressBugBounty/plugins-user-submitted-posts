<?php // User Submitted Posts - Core Functions

if (!defined('ABSPATH')) die();

function usp_auto_display_images($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_images']) ? $usp_options['auto_display_images'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_image_markup']) ? $usp_options['auto_image_markup'] : '';
		$author = get_post_meta(get_the_ID(), 'user_submit_name', true);
		
		$args = array(
				'post_type'   => 'attachment',
				'post_parent' => get_the_ID(),
				'numberposts' => -1,
		);
		
		$args = apply_filters('usp_image_args', $args);
		
		$attachments = get_posts($args);
		
		if ($attachments) {
			
			$images = '<p>';
			
			foreach ($attachments as $attachment) {
				
				$title  = apply_filters('usp_image_title',  $attachment->post_title);
				
				$thumb  = apply_filters('usp_image_thumb',  wp_get_attachment_image_src($attachment->ID, 'thumbnail', false));
				$medium = apply_filters('usp_image_medium', wp_get_attachment_image_src($attachment->ID, 'medium', false));
				$large  = apply_filters('usp_image_large',  wp_get_attachment_image_src($attachment->ID, 'large', false));
				$full   = apply_filters('usp_image_full',   wp_get_attachment_image_src($attachment->ID, 'full', false));
				
				$custom_size = apply_filters('usp_image_custom_size', 'custom');
				$custom = apply_filters('usp_image_custom', wp_get_attachment_image_src($attachment->ID, $custom_size, false));
				
				$parent_id = wp_get_post_parent_id($attachment->ID);
				$parent_title = get_the_title($parent_id);
				
				$url = apply_filters('usp_url_custom_field', get_post_meta(get_the_ID(), 'user_submit_url', true));
				
				$images .= usp_replace_image_vars($markup, $title, $thumb, $medium, $large, $full, $custom, $parent_title, $author, $url);
				
			}
			
			$images .= '</p>';
			
			if     ($enable === 'before') $content = $images . $content;
			elseif ($enable === 'after')  $content = $content . $images;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_images');



function usp_replace_image_vars($markup, $title, $thumb, $medium, $large, $full, $custom, $parent_title, $author, $url) {
	
	$patterns = array();
	$patterns[0]  = "/%%title%%/";
	$patterns[1]  = "/%%thumb%%/";
	$patterns[2]  = "/%%medium%%/";
	$patterns[3]  = "/%%large%%/";
	$patterns[4]  = "/%%full%%/";
	$patterns[5]  = "/%%custom%%/";
	$patterns[6]  = "/%%width%%/";
	$patterns[7]  = "/%%height%%/";
	$patterns[8]  = "/%%title_parent%%/";
	$patterns[9]  = "/%%author%%/";
	$patterns[10] = "/%%url%%/";
	
	$replacements = array();
	$replacements[0] = esc_attr($title);
	$replacements[1] = esc_url($thumb[0]);
	$replacements[2] = esc_url($medium[0]);
	$replacements[3] = esc_url($large[0]);
	$replacements[4] = esc_url($full[0]);
	$replacements[5] = esc_url($custom[0]);
	
	if (stripos($markup, '%%thumb%%')) {
		
		$replacements[6] = esc_attr($thumb[1]);
		$replacements[7] = esc_attr($thumb[2]);
		
	} elseif (stripos($markup, '%%medium%%')) {
		
		$replacements[6] = esc_attr($medium[1]);
		$replacements[7] = esc_attr($medium[2]);
		
	} elseif (stripos($markup, '%%large%%')) {
		
		$replacements[6] = esc_attr($large[1]);
		$replacements[7] = esc_attr($large[2]);
		
	} elseif (stripos($markup, '%%full%%')) {
		
		$replacements[6] = esc_attr($full[1]);
		$replacements[7] = esc_attr($full[2]);
		
	} elseif (stripos($markup, '%%custom%%')) {
		
		$replacements[6] = esc_attr($custom[1]);
		$replacements[7] = esc_attr($custom[2]);
	}
	
	$replacements[8]  = esc_attr($parent_title);
	$replacements[9]  = esc_attr($author);
	$replacements[10] = esc_url($url);
	
	$image = preg_replace($patterns, $replacements, $markup);
	
	return $image;
	
}



function usp_auto_display_email($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_email']) ? $usp_options['auto_display_email'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_email_markup']) ? $usp_options['auto_email_markup'] : '';
		$author = apply_filters('usp_author_custom_field', get_post_meta(get_the_ID(), 'user_submit_name', true));
		$email  = apply_filters('usp_email_custom_field', get_post_meta(get_the_ID(), 'user_submit_email', true));
		$title  = get_the_title(get_the_ID());
		
		if (!empty($email)) {
			
			$patterns = array();
			$patterns[0] = "/%%author%%/";
			$patterns[1] = "/%%email%%/";
			$patterns[2] = "/%%title%%/";
			
			$replacements = array();
			$replacements[0] = esc_attr($author);
			$replacements[1] = esc_attr($email);
			$replacements[2] = esc_attr($title);
			
			$markup = preg_replace($patterns, $replacements, $markup);
			
			if     ($enable === 'before') $content = $markup . $content;
			elseif ($enable === 'after')  $content = $content . $markup;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_email');



function usp_auto_display_name($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_name']) ? $usp_options['auto_display_name'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_name_markup']) ? $usp_options['auto_name_markup'] : '';
		
		$author = apply_filters('usp_author_custom_field', get_post_meta(get_the_ID(), 'user_submit_name', true));
		
		if (!empty($author)) {
			
			$patterns = array();
			$patterns[0] = "/%%author%%/";
			
			$replacements = array();
			$replacements[0] = esc_attr($author);
			
			$markup = preg_replace($patterns, $replacements, $markup);
			
			if     ($enable === 'before') $content = $markup . $content;
			elseif ($enable === 'after')  $content = $content . $markup;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_name');



function usp_auto_display_url($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_url']) ? $usp_options['auto_display_url'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_url_markup']) ? $usp_options['auto_url_markup'] : '';
		$author = apply_filters('usp_author_custom_field', get_post_meta(get_the_ID(), 'user_submit_name', true));
		$url    = apply_filters('usp_url_custom_field', get_post_meta(get_the_ID(), 'user_submit_url', true));
		$title  = get_the_title(get_the_ID());
		
		if (!empty($url)) {
			
			$patterns = array();
			$patterns[0] = "/%%author%%/";
			$patterns[1] = "/%%url%%/";
			$patterns[2] = "/%%title%%/";
			
			$replacements = array();
			$replacements[0] = esc_attr($author);
			$replacements[1] = esc_url($url);
			$replacements[2] = esc_attr($title);
			
			$markup = preg_replace($patterns, $replacements, $markup);
			
			if     ($enable === 'before') $content = $markup . $content;
			elseif ($enable === 'after')  $content = $content . $markup;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_url');



function usp_auto_display_custom_2($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_custom_2']) ? $usp_options['auto_display_custom_2'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_custom_markup_2']) ? $usp_options['auto_custom_markup_2'] : '';
		$label  = isset($usp_options['custom_label_2'])       ? $usp_options['custom_label_2']       : __('Custom Field 2', 'usp');
		$name   = isset($usp_options['custom_name_2'])        ? $usp_options['custom_name_2']        : 'usp_custom_field_2';
		
		$author = apply_filters('usp_author_custom_field_2', get_post_meta(get_the_ID(), 'user_submit_name', true));
		$value  = apply_filters('usp_custom_custom_field_2', get_post_meta(get_the_ID(), $name, true));
		$title  = get_the_title(get_the_ID());
		
		if (!empty($value)) {
			
			$value = htmlspecialchars_decode($value);
			$value = nl2br($value);
			
			$patterns = array();
			$patterns[0] = "/%%author%%/";
			$patterns[1] = "/%%custom_label_2%%/";
			$patterns[2] = "/%%custom_name_2%%/";
			$patterns[3] = "/%%custom_value_2%%/";
			$patterns[4] = "/%%title%%/";
			
			$replacements = array();
			$replacements[0] = esc_attr(wp_kses_post($author));
			$replacements[1] = esc_attr(wp_kses_post($label));
			$replacements[2] = esc_attr(wp_kses_post($name));
			$replacements[3] = esc_attr(wp_kses_post($value));
			$replacements[4] = esc_attr(wp_kses_post($title));
			
			$markup = preg_replace($patterns, $replacements, $markup);
			
			if     ($enable === 'before') $content = $markup . $content;
			elseif ($enable === 'after')  $content = $content . $markup;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_custom_2');



function usp_auto_display_custom($content) {
	
	global $usp_options;
	
	$enable = isset($usp_options['auto_display_custom']) ? $usp_options['auto_display_custom'] : 'disable';
	
	if (usp_is_public_submission() && ($enable === 'before' || $enable === 'after')) {
		
		$markup = isset($usp_options['auto_custom_markup']) ? $usp_options['auto_custom_markup'] : '';
		$label  = isset($usp_options['custom_label'])       ? $usp_options['custom_label']       : __('Custom Field 1', 'usp');
		$name   = isset($usp_options['custom_name'])        ? $usp_options['custom_name']        : 'usp_custom_field';
		
		$author = apply_filters('usp_author_custom_field', get_post_meta(get_the_ID(), 'user_submit_name', true));
		$value  = apply_filters('usp_custom_custom_field', get_post_meta(get_the_ID(), $name, true));
		$title  = get_the_title(get_the_ID());
		
		if (!empty($value)) {
			
			$value = htmlspecialchars_decode($value);
			$value = nl2br($value);
			
			$patterns = array();
			$patterns[0] = "/%%author%%/";
			$patterns[1] = "/%%custom_label%%/";
			$patterns[2] = "/%%custom_name%%/";
			$patterns[3] = "/%%custom_value%%/";
			$patterns[4] = "/%%title%%/";
			
			$replacements = array();
			$replacements[0] = esc_attr(wp_kses_post($author));
			$replacements[1] = esc_attr(wp_kses_post($label));
			$replacements[2] = esc_attr(wp_kses_post($name));
			$replacements[3] = esc_attr(wp_kses_post($value));
			$replacements[4] = esc_attr(wp_kses_post($title));
			
			$markup = preg_replace($patterns, $replacements, $markup);
			
			if     ($enable === 'before') $content = $markup . $content;
			elseif ($enable === 'after')  $content = $content . $markup;
			
		}
		
	}
	
	return $content;
	
}
add_filter('the_content', 'usp_auto_display_custom');
