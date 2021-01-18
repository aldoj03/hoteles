<?php

/**
 * Fired during plugin activation
 
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class Hoteles_Activator
{


	

	public static function activate()
	{

		$page = get_page_by_path('resultado-hoteles');

		if (!isset($page)) {


			$new_page = array(
				'slug' => 'resultado-hoteles',
				'title' => 'Resultado Hoteles',
				'content' => "[hot-results-page]"
			);
			$new_page_id = wp_insert_post(array(
				'post_title' => $new_page['title'],
				'post_type'     => 'page',
				'post_name'  => $new_page['slug'],
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_content' => $new_page['content'],
				'post_status' => 'publish',
				'post_author' => 1,
				'menu_order' => 0
			));
		}



		register_post_type(
			'hoteles',
			// CPT Options
			array(
				'labels' => array(
					'name' => __('Hoteles'),
					'singular_name' => __('Hotel')
				),
				'public' => false,
				'has_archive' => false,
				'rewrite' => array('slug' => 'hotel'),
				'show_in_rest' => true,
	
			)
		);
	}

}
