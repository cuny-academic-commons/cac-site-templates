<?php

namespace CAC\SiteTemplates\Template;

class Cloner {

	protected $template;
	protected $destination_site_id;

	public function __construct() {}

	public function set_template( Template $template ) {
		$this->template = $template;
	}

	public function set_destination_site_id( $id ) {
		$this->destination_site_id = (int) $id;
	}

	protected function get_template_site_id() {
		return $this->template->get_template_site_id();
	}

	/**
	 * Summary:
	 *
	 * 1) Copy settings from old blog, using blacklist
	 * 2) Copy admin-authored posts from old blog
	 */
	public function go() {
		if ( empty( $this->destination_site_id ) ) {
			return false;
		}

		$this->migrate_site_settings();
		$this->migrate_content();

		// Record source info.
		bp_blogs_update_blogmeta( $this->destination_site_id, 'cac_site_template_id', $this->template->get_id() );
	}

	/**
	 * Taken from site-template
	 */
	protected function migrate_site_settings() {
		global $wpdb;

		switch_to_blog( $this->get_template_site_id() );

		// get all old options
		$all_options = wp_load_alloptions();
		$options     = array();
		foreach ( array_keys( $all_options ) as $key ) {
			$options[ $key ] = get_option( $key );  // have to do this to deal with arrays
		}

		// theme mods -- don't show up in all_options.
		// Only add options for the current theme
		$theme = get_option( 'current_theme' );
		$mods  = get_option( 'mods_' . $theme );

		$preserve_option = array(
			'siteurl',
			'blogname',
			'admin_email',
			'new_admin_email',
			'home',
			'upload_path',
			'db_version',
			$wpdb->get_blog_prefix( $this->destination_site_id ) . 'user_roles',
			'fileupload_url',
		);

		// now write them all back
		switch_to_blog( $this->destination_site_id );
		foreach ( $options as $key => $value ) {
			if ( ! in_array( $key, $preserve_option ) ) {
				update_option( $key, $value );
			}
		}

		// add the theme mods
		update_option( 'mods_' . $theme, $mods );

		// Just in case
		create_initial_taxonomies();
		flush_rewrite_rules();

		restore_current_blog();
	}

	protected function migrate_content() {
		global $wpdb;

		$tables_to_copy = array(
			'posts',
			'postmeta',
			'comments',
			'commentmeta',
			'terms',
			'termmeta',
			'term_taxonomy',
			'term_relationships',
		);

		// Have to use different syntax for shardb
		$source_site_prefix = $wpdb->get_blog_prefix( $this->get_template_site_id() );
		$site_prefix        = $wpdb->get_blog_prefix( $this->destination_site_id );
		foreach ( $tables_to_copy as $ttc ) {
			$source_table = $source_site_prefix . $ttc;
			$table        = $site_prefix . $ttc;

			$wpdb->query( "TRUNCATE {$table}" );
			$wpdb->query( "INSERT INTO {$table} SELECT * FROM {$source_table}" );
		}

		switch_to_blog( $this->destination_site_id );

		$source_site_url = get_blog_option( $this->get_template_site_id(), 'home' );
		$dest_site_url   = get_option( 'home' );

		// Copy over attachments. Whee!
		$upload_dir = wp_upload_dir();
		$this->copyr( str_replace( $this->destination_site_id, $this->get_template_site_id(), $upload_dir['basedir'] ), $upload_dir['basedir'] );

		$site_posts = $wpdb->get_results( "SELECT ID, guid, post_author, post_status, post_title, post_type FROM {$wpdb->posts}" );
		foreach ( $site_posts as $sp ) {
			if ( 'nav_menu_item' === $sp->post_type ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'guid' => str_replace( $source_site_url, $dest_site_url, $sp->guid ),
					),
					array(
						'ID' => $sp->ID,
					)
				);

				$url = get_post_meta( $sp->ID, '_menu_item_url', true );
				if ( $url ) {
					update_post_meta( $sp->ID, '_menu_item_url', str_replace( $source_site_url, $dest_site_url, $url ) );
				}
			}
		}

		// Replace the site URL and upload URL in all post content.
		// For some reason a regular MySQL query is not working.
		$this_site_url = get_option( 'home' );

		$this_site_upload_dir   = $upload_dir['baseurl'];
		$source_site_upload_dir = str_replace( $this->destination_site_id, $this->get_template_site_id(), $upload_dir['baseurl'] );

		foreach ( $wpdb->get_col( "SELECT ID FROM $wpdb->posts" ) as $post_id ) {
			$post               = get_post( $post_id );
			$post->post_content = str_replace( $source_site_url, $this_site_url, $post->post_content );
			$post->post_content = str_replace( $source_site_upload_dir, $this_site_upload_dir, $post->post_content );
			wp_update_post( $post );
		}

		restore_current_blog();
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @author  Aidan Lister <aidan@php.net>
	 * @version 1.0.1
	 * @link    http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param   string $source Source path
	 * @param   string $dest   Destination path
	 * @return  bool     Returns TRUE on success, FALSE on failure
	 */
	function copyr( $source, $dest ) {
		// Check for symlinks
		if ( is_link( $source ) ) {
			return symlink( readlink( $source ), $dest );
		}

		// Simple copy for a file
		if ( is_file( $source ) ) {
			return copy( $source, $dest );
		}

		// Nothing to do here.
		if ( ! file_exists( $source ) ) {
			return;
		}

		// Make destination directory
		if ( ! is_dir( $dest ) ) {
			mkdir( $dest );
		}

		// Loop through the folder
		$dir = dir( $source );
		while ( false !== $entry = $dir->read() ) {
			// Skip pointers and hidden files.
			if ( 0 === strpos( $entry, '.' ) ) {
				continue;
			}

			// Deep copy directories
			$this->copyr( "$source/$entry", "$dest/$entry" );
		}

		// Clean up
		$dir->close();
		return true;
	}
}
