<?php

namespace CAC\SiteTemplates\Template;

use \WP_Post;

class Template {
	protected $data = [
		'id'               => 0,
		'name'             => '',
		'description'      => '',
		'template_site_id' => 0,
		'demo_site_id'     => 0,
		'menu_order'       => 0,
	];

	public function __construct( $template_id = null ) {
		if ( is_null( $template_id ) ) {
			return;
		}

		$post = get_post( $template_id );
		if ( ! $post || 'cac_site_template' !== $post->post_type ) {
			return;
		}

		$this->populate_from_wp_post( $post );
	}

	public function populate_from_wp_post( WP_Post $post ) {
		$this->data['id']          = $post->ID;
		$this->data['name']        = $post->post_title;
		$this->data['description'] = $post->post_content;
		$this->data['menu_order']  = $post->menu_order;

		$this->data['template_site_id'] = (int) get_post_meta( $post->ID, 'template-site-id', true );
		$this->data['demo_site_id']     = (int) get_post_meta( $post->ID, 'demo-site-id', true );
	}

	public function get_id() {
		return (int) $this->data['id'];
	}

	public function get_name() {
		return $this->data['name'];
	}

	public function get_description() {
		return $this->data['description'];
	}

	public function get_template_site_id() {
		return (int) $this->data['template_site_id'];
	}

	public function get_demo_site_id() {
		return (int) $this->data['demo_site_id'];
	}

	public function get_image_markup() {
		return get_the_post_thumbnail( $this->get_id(), 'medium' );
	}

	public function get_demo_site_url() {
		return get_blog_option( $this->get_demo_site_id(), 'home' );
	}

	public function is_default() {
		return 0 === $this->data['menu_order'];
	}

	public function clone_to_site( $new_site_id ) {
		$cloner = new Cloner();
		$cloner->set_template( $this );
		$cloner->set_destination_site_id( $new_site_id );
		$cloner->go();
	}
}
