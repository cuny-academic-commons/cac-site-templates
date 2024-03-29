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

		// Sanity check - don't clone if destination site is more than a minute old.
		$destination_site = get_site( $this->destination_site_id );
		if ( $destination_site ) {
			$updated = strtotime( $destination_site->last_updated );
			if ( ( time() - $updated ) > MINUTE_IN_SECONDS ) {
				return false;
			}
		}

		wp_installing( false );

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
			'blog_public',
			'blogname',
			'admin_email',
			'new_admin_email',
			'home',
			'upload_path',
			'db_version',
			$wpdb->get_blog_prefix( $this->get_template_site_id() ) . 'user_roles',
			$wpdb->get_blog_prefix( $this->destination_site_id ) . 'user_roles',
			'fileupload_url',
			'timezone_string',
		);

		$source_site_upload_dir = wp_upload_dir( null, true, true );

		restore_current_blog();

		// Now write them all back.
		switch_to_blog( $this->destination_site_id );

		$source_site_url = get_blog_option( $this->get_template_site_id(), 'home' );
		$dest_site_url   = get_blog_option( $this->destination_site_id, 'home' );

		$dest_site_upload_dir = wp_upload_dir( null, true, true );

		$dest_site_upload_dir   = $dest_site_upload_dir['baseurl'];
		$source_site_upload_dir = $source_site_upload_dir['baseurl'];

		foreach ( $options as $key => $value ) {
			if ( in_array( $key, $preserve_option ) ) {
				continue;
			}

			$value = $this->search_replace( $source_site_upload_dir, $dest_site_upload_dir, $value );
			$value = $this->search_replace( $source_site_url, $dest_site_url, $value );

			update_option( $key, $value );
		}

		// Override the new site's user roles with those from the source site.
		if ( isset( $options[ $wpdb->get_blog_prefix( $this->get_template_site_id() ) . 'user_roles'] ) ) {
			update_option( $wpdb->get_blog_prefix( $this->destination_site_id ) . 'user_roles', $options[ $wpdb->get_blog_prefix( $this->get_template_site_id() ) . 'user_roles' ] );
		}

		// add the theme mods
		update_option( 'mods_' . $theme, $mods );

		// Plugin package info.
		if ( is_plugin_active( 'cac-plugin-packages/cac-plugin-packages.php' ) ) {
			if ( ! function_exists( 'cac_get_plugin_packages' ) ) {
				include WP_PLUGIN_DIR . '/cac-plugin-packages/includes/functions.php';
			}

			$source_packages = get_blog_option( $this->get_template_site_id(), 'cac_plugin_packages' );
			if ( is_array( $source_packages ) ) {
				foreach ( $source_packages as $plugin_package ) {
					bp_blogs_add_blogmeta( $this->destination_site_id, "activated_plugin_package_{$plugin_package}", time() );
				}

				update_option( 'cac_plugin_packages', $source_packages, 'no' );
			}
		}

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

		remove_action( 'transition_post_status', 'bp_activity_catch_transition_post_type_status', 10, 3 );

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

			// Switch authorship of posts and pages.
			if ( 'post' === $sp->post_type || 'page' === $sp->post_type ) {
				$wpdb->update(
					$wpdb->posts,
					array(
						'post_author' => get_current_user_id(),
					),
					array(
						'ID' => $sp->ID,
					)
				);
			}
		}

		// Replace the site URL and upload URL in all post content.
		// For some reason a regular MySQL query is not working.
		$this_site_url = get_option( 'home' );

		$this_site_upload_dir   = $upload_dir['baseurl'];
		$source_site_upload_dir = str_replace( $this->destination_site_id, $this->get_template_site_id(), $upload_dir['baseurl'] );

		foreach ( $wpdb->get_col( "SELECT ID FROM $wpdb->posts" ) as $post_id ) {
			$post               = get_post( $post_id );
			$original_content   = $post->post_content;

			$new_content = str_replace( $source_site_url, $this_site_url, $original_content );
			$new_content = str_replace( $source_site_upload_dir, $this_site_upload_dir, $new_content );

			if ( $new_content !== $original_content ) {
				$post->post_content = $new_content;
				wp_update_post( $post );
			}
		}

		add_action( 'transition_post_status', 'bp_activity_catch_transition_post_type_status', 10, 3 );

		$GLOBALS['wp_rewrite']->init();
		flush_rewrite_rules();

		restore_current_blog();

		$GLOBALS['wp_rewrite']->init();
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
	protected function copyr( $source, $dest ) {
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

	/**
	 * Recursive-friendly search/replace.
	 */
	protected function search_replace( $search, $replace, $data ) {
		if ( is_string( $data ) && ! is_serialized( $data ) ) {
			return str_replace( $search, $replace, $data );
		}

		if ( is_array( $data ) ) {
			$keys = array_keys( $data );
			foreach ( $keys as $key ) {
				$data[ $key ] = $this->search_replace( $search, $replace, $data[ $key ] );
			}
		} elseif ( is_object( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data->{$key} = $this->search_replace( $search, $replace, $value );
			}
		} elseif ( is_string( $data ) ) {
			$unserialized = @unserialize( $data );
			if ( $unserialized ) {
				$data = $this->search_replace( $search, $replace, $unserialized );
			}
		}

		return $data;
	}
}
