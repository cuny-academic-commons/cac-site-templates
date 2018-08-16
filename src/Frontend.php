<?php

namespace CAC\SiteTemplates;

class Frontend {
	public static function init() {
		add_action( 'signup_blogform', [ __CLASS__, 'signup_field' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
	}

	public static function register_assets() {
		wp_register_style(
			'cac-site-template-signup-field',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/assets/css/signup-field.css'
		);
	}

	public static function signup_field() {
		wp_enqueue_style( 'cac-site-template-signup-field' );

		$template_query = new Template\Query();
		$templates      = $template_query->get_results();

		?>

<div class="site-template-selector">
	<h2><?php esc_html_e( 'Site Template', 'cac-site-templates' ); ?></h2>

	<p class="site-template-selector-gloss">
		<?php esc_html_e( 'Select your template site. Your new site will be configured to match the template you have chosen. After your site has been created, you can customize the base configuration in any way you\'d like.', 'cac-site-templates' ); ?>
	</p>

	<ul>
		<?php foreach ( $templates as $template ) : ?>
			<li>
				<input type="radio" name="site-template" value="<?php echo esc_attr( $template->get_id() ); ?>" id="site-template-<?php echo esc_attr( $template->get_id() ); ?>" /> <label class="site-template-name" for="site-template-<?php echo esc_attr( $template->get_id() ); ?>"><?php echo esc_html( $template->get_name() ); ?></label>

				<div class="site-template-info">
					<div class="site-template-image">
						<?php echo $template->get_image_markup(); ?>
					</div>

					<div class="site-template-meta">
						<div class="site-template-demo-link">
							<?php
							if ( $template->get_demo_site_id() ) {
								$demo_site_url = $template->get_demo_site_url();
								printf(
									esc_html__( 'Demo: %s', 'cac-site-templates' ),
									sprintf(
										'<a href="%s">%s</a>',
										esc_attr( $demo_site_url ),
										esc_html( $demo_site_url )
									)
								);
							}
							?>
						</div>

						<div class="site-template-description">
							<?php echo $template->get_description(); ?>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

		<?php
	}
}
