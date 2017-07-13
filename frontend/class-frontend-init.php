<?php

namespace WP_AMP_Themes\Frontend;

use \WP_AMP_Themes\Includes\Options;

/**
 * Frontend_Init class for initializing the admin area of the WP AMP Themes plugin.
 *
 * Displays menu & loads static files for the admin page.
 */
class Frontend_Init {


	public function __construct() {

		$this->integrate_template();

	}


	/**
	* Load a custom AMP template on top of the AMP plugin.
	*/
	public function integrate_template() {

		add_filter( 'amp_post_template_file', [ $this, 'set_wp_amp_theme_template' ], 10, 3 );

		$wp_amp_themes_options = new Options();

		if ( $wp_amp_themes_options->get_setting( 'analytics_id' ) !== '' ) {

			add_filter( 'amp_post_template_analytics', [ $this, 'add_analytics' ] );

		}

		add_filter( 'amp_content_embed_handlers', [ $this, 'set_wp_amp_post_social_embed' ], 10, 2 );
	}

	public function set_wp_amp_theme_template( $file, $type, $post ) {

		$wp_amp_themes_options = new Options();
		$theme = $wp_amp_themes_options->get_setting( 'theme' );

		if ( 'single' === $type ) {
			$file = WP_AMP_THEMES_PLUGIN_PATH . "frontend/themes/$theme/single.php";
		}

		if ( 'style' === $type ) {
			$file = WP_AMP_THEMES_PLUGIN_PATH . "frontend/themes/$theme/style.php";
		}

		if ( 'side-menu' === $type ) {
			$file = WP_AMP_THEMES_PLUGIN_PATH . "frontend/themes/$theme/side-menu.php";
		}

		return $file;
	}


	/**
	* Add filter for adding the social media share buttons to a post
	*
	* @param $embed_handler_classes
	* @param $post
	*/
	public function set_wp_amp_post_social_embed( $embed_handler_classes, $post ) {
		require_once( dirname( __FILE__ ) . '/class-embed-handler.php' );
		$embed_handler_classes[ 'WAT_Social_Media_Embed_Handler' ] = array();
		return $embed_handler_classes;
	}


	/**
	* Add Google Analytics ID to the template.
	*
	* @param array $analytics
	* @return array
	*/
	public function add_analytics( $analytics ) {

		$wp_amp_themes_options = new Options();
		$analytics_id = $wp_amp_themes_options->get_setting( 'analytics_id' );

		if ( ! is_array( $analytics ) ) {
			$analytics = [];
		}

		$analytics['wp-amp-themes-google-analytics'] = [
			'type' => 'googleanalytics',
			'attributes' => [],
			'config_data' => [
				'vars' => [
					'account' => $analytics_id,
				],
				'triggers' => [
					'trackPageview' => [
						'on' => 'visible',
						'request' => 'pageview',
					],
				],
			],
		];

		return $analytics;
	}



}
