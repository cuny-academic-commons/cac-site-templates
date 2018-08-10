<?php

namespace CAC\SiteTemplates;

class API {
	protected $endpoints = array();

	private function __construct() {}

	public static function get_instance() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'init_endpoints' ) );
	}

	public static function init_endpoints() {
		$endpoints['site'] = new Endpoints\Site();

		foreach ( $endpoints as $endpoint ) {
			$endpoint->register_routes();
		}
	}
}
