<?php

namespace CAC\SiteTemplates;

class App {
	protected $post_type = 'cac_invitation';

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @return CAC\Onboarding\App
	 */
	private function __construct() {
		return $this;
	}

	public static function get_instance() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function init() {
		// Initialize Gutenberg blocks.
		Blocks::init();

		// Schema.
		add_action( 'init', [ __CLASS__, 'register_post_type' ] );

		// API endpoints.
		API::init();
	}

	public static function register_post_type() {
		register_post_type(
			'cac_site_template',
			[
				'public'       => false,
				'show_ui'      => current_user_can( 'activate_plugins' ),
				'show_in_rest' => true,
				'template'     => [
					[ 'cac-site-templates/cac-site-template-info' ],
				],
				'labels'       => [
					'name'          => 'Site Templates',
					'singular_name' => 'Site Template',
				],
				'supports'     => [ 'editor', 'title', 'custom-fields' ],
			]
		);

		register_meta(
			'post',
			'template-site-id',
			[
				'object_subtype' => 'cac_site_template',
				'show_in_rest'   => true,
				'single'         => true,
				'type'           => 'integer',
			]
		);

		register_meta(
			'post',
			'demo-site-id',
			[
				'object_subtype' => 'cac_site_template',
				'show_in_rest'   => true,
				'single'         => true,
				'type'           => 'integer',
			]
		);
	}
}
