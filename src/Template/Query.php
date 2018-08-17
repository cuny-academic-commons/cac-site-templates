<?php

namespace CAC\SiteTemplates\Template;

class Query {
	protected $query_vars;

	public function __construct( $args = [] ) {
		$this->query_vars = array_merge(
			[],
			$args
		);
	}

	public function get_results() {
		$wp_query_args = [
			'orderby'        => [ 'menu_order' => 'ASC' ],
			'post_type'      => 'cac_site_template',
			'posts_per_page' => -1,
		];

		$posts = get_posts( $wp_query_args );

		$templates = array_map(
			function( $post ) {
				$template = new Template();
				$template->populate_from_wp_post( $post );
				return $template;
			},
			$posts
		);

		return $templates;
	}
}
