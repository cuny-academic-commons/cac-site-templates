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

	public function init() {
		// Initialize Gutenberg blocks.
		Blocks::init();

		// Schema.
		add_action( 'init', [ __CLASS__, 'register_post_type' ] );

		// API endpoints.
		API::init();

		// Frontend template integration.
		Frontend::init();
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
					[
						'core/paragraph',
						[
							'placeholder' => 'Enter description',
						]
					],
				],
				'labels'       => [
					'name'          => 'Site Templates',
					'singular_name' => 'Site Template',
				],
				'supports'     => [
					'custom-fields',
					'editor',
					'page-attributes',
					'thumbnail',
					'title',
				],
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

		register_meta(
			'post',
			'template-site-id',
			[
				'object_subtype' => 'cac_site_template',
				'show_in_rest'   => true,
				'single'         => true,
				'type'           => 'demo-site-link-text',
			]
		);

		register_meta(
			'post',
			'template-site-id',
			[
				'object_subtype' => 'cac_site_template',
				'show_in_rest'   => true,
				'single'         => true,
				'type'           => 'demo-site-link-url',
			]
		);
	}
}
