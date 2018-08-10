<?php

namespace CAC\SiteTemplates\Endpoints;

use \WP_REST_Controller;
use \WP_REST_Server;
use \WP_REST_Request;
use \WP_REST_Response;

use \WP_Site;

/**
 * site endpoint.
 */
class Site extends WP_REST_Controller {
	/**
	 * Register endpoint routes.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'cac-site-templates/v' . $version;

		register_rest_route(
			$namespace,
			'/site/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				),
			)
		);


		register_rest_route(
			$namespace,
			'/site',
			array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'search' ),
					'permission_callback' => array( $this, 'search_permissions_check' ),
					'args'            => $this->get_endpoint_args_for_item_schema( true ),
				),
			)
		);
	}

	/**
	 * Permission check for searching.
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function search_permissions_check( $request ) {
		return current_user_can( 'create_sites' );
	}

	/**
	 * Performs a search.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function search( $request ) {
		global $wpdb;

		$search_term = $request->get_param( 'search' );

		$results = array();
		if ( $search_term ) {
			$bp = buddypress();

			$like    = '%' . $wpdb->esc_like( $search_term ) . '%';
			$matches = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM {$bp->blogs->table_name_blogmeta} WHERE meta_key IN ( 'name', 'url' ) AND meta_value LIKE %s", $like ) );

			if ( $matches ) {
				$sites = get_sites(
					array(
						'site__in' => $matches,
					)
				);

				$results = array_map( [ $this, 'format_site' ], $sites );
			}
		}

		$response = rest_ensure_response( $results );

		return $response;
	}

	protected function format_site( WP_Site $site ) {
		return [
			'id'   => $site->id,
			'name' => $site->blogname,
			'url'  => $site->domain,
		];
	}

	/**
	 * Permission check for getting a single item.
	 *
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'create_sites' );
	}


	/**
	 * Gets a single site.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function get_item( $request ) {
		global $wpdb;

		$site_id = $request->get_param( 'id' );

		$results = $this->format_site( get_site( $site_id ) );

		return rest_ensure_response( $results );
	}

}
