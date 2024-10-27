<?php
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
?>

<div class="wrap smartzine-dashboard">

	<h2 class="nav-tab-wrapper wp-clearfix">
		<?php Adredux_Settings_Page::get_dashboard_page_tabs( $active_tab ); ?>
	</h2><!-- .nav-tab-wrapper -->

	<div class="tab-content wp-clearfix">
		<div class="tab-primary">
			<div class="inner">
				<?php Adredux_Settings_Page::get_dashboard_page_tab_content( $active_tab ); ?>
			</div><!-- .inner -->
		</div><!-- .tab-primary -->

		<div class="tab-secondary">
			<div class="inner">
				<?php require_once ADREDUX_DIR . '/admin/partials/dashboard-sidebar.php'; ?>
			</div><!-- .inner -->
		</div><!-- .tab-secondary -->
	</div><!-- .tab-content -->
</div><!-- .wrap.about-wrap -->
