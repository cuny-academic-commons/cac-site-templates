<?php

namespace CAC\SiteTemplates;

class Blocks {
	public static function init() {
		add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
	}

	public static function enqueue_block_editor_assets() {
		// Scripts.
		wp_enqueue_script(
			'cac-site-templates-block-js',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/build/index.js',
			[ 'lodash', 'wp-edit-post', 'wp-plugins', 'wp-data', 'wp-components', 'wp-blocks', 'wp-i18n', 'wp-element' ],
			CAC_SITE_TEMPLATES_VERSION,
			true
		);
	}
}
