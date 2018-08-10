<?php

namespace CAC\SiteTemplates;

class Blocks {
	public static function init() {
		add_action( 'enqueue_block_assets', [ __CLASS__, 'enqueue_block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
	}

	protected static function get_blocks() {
		return [
			'site-template-info',
		];
	}

	public static function enqueue_block_assets() {
		/*
		// Styles.
		wp_enqueue_style(
			$block . '-style',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/dist/blocks.style.build.css',
			array( 'wp-blocks' ) // Dependency to include the CSS after it.
		);
		*/
	}

	public static function enqueue_block_editor_assets() {
		// Scripts.
		wp_enqueue_script(
			'cac-site-templates-block-js',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/dist/block.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' )
		);
	}
}
