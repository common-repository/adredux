<div class="tab-section">

		<div class="wrap adredux">

			<?php
					if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
						  $this->adredux_update_notice();
					} ?>
				
				<form method="POST" action="options.php">
					<?php
						settings_fields( 'adredux_settings');
						do_settings_sections( 'adredux_settings' );
						submit_button();
					?>
				</form>

		</div>


</div><!-- .tab-section -->