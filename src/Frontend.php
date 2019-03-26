<?php

namespace CAC\SiteTemplates;

class Frontend {
	public static function init() {
		add_action( 'signup_blogform', [ __CLASS__, 'signup_field' ] );
		add_action( 'wpmu_new_blog', [ __CLASS__, 'process_site_template'] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
	}

	public static function register_assets() {
		wp_register_style(
			'cac-site-template-signup-field',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/assets/css/signup-field.css'
		);

		wp_register_script(
			'cac-site-templates-site-create',
			CAC_SITE_TEMPLATES_PLUGIN_URL . '/assets/js/site-create.js',
			[ 'jquery' ],
			false,
			true
		);

		wp_localize_script(
			'cac-site-templates-site-create',
			'CACSiteTemplatesSiteCreate',
			[
				'confirm' => __( 'By leaving this page, you will cancel the site creation process. Are you sure you want to leave?', 'cac-site-templates' ),
			]
		);
	}

	public static function signup_field() {
		if ( bp_is_group_admin_page() ) {
			return;
		}

		wp_enqueue_style( 'cac-site-template-signup-field' );
		wp_enqueue_script( 'cac-site-templates-site-create' );

		$template_query = new Template\Query();
		$templates      = $template_query->get_results();

		?>

<div class="site-template-selector">
	<h3><?php esc_html_e( 'Site Layout', 'cac-site-templates' ); ?></h3>

	<p class="site-template-selector-gloss">
		<?php esc_html_e( 'The Site Layout tool is designed to make the process of creating a Commons site a little easier by helping you choose a design and a collection of plugins that correspond to the purpose of your new site. The following layouts have been suggested by previous Commons users based on their experience.', 'cac-site-templates' ); ?>
	</p>

	<p class="site-template-selector-gloss">
		<?php esc_html_e( 'Your new site will be configured to match the layout you choose below. These configurations are defaults only; they can be changed after your site has been created.', 'cac-site-templates' ); ?>
	</p>

	<p class="site-template-selector-gloss">
		<?php esc_html_e( 'To open demo sites in a new tab without disrupting the site creation process, right-click the URLs.', 'cac-site-templates' ); ?>
	</p>

	<ul>
		<?php foreach ( $templates as $template ) : ?>
			<li>
				<input type="radio" name="site-template" value="<?php echo esc_attr( $template->get_id() ); ?>" id="site-template-<?php echo esc_attr( $template->get_id() ); ?>" <?php checked( $template->is_default() ); ?> /> <label class="site-template-name" for="site-template-<?php echo esc_attr( $template->get_id() ); ?>"><?php echo esc_html( $template->get_name() ); ?></label>

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
									'<a href="%s" target="_blank">%s</a>',
									esc_attr( $demo_site_url ),
									sprintf(
										esc_html__( '%s Demo', 'cac-site-templates' ),
										esc_html( $template->get_name() )
									)
								);

								echo '<span class="site-template-new-window-gloss">' . esc_html__( '(link will open in a new window)', 'cac-site-templates' ) . '</span>';

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

	public static function process_site_template( $new_site_id ) {
		if ( ! isset( $_POST['site-template'] ) ) {
			return;
		}

		$site_template_id = intval( $_POST['site-template'] );
		$site_template    = new Template\Template( $site_template_id );

		$site_template->clone_to_site( $new_site_id );
	}
}
