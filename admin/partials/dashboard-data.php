<?php
$rawdata = esc_html(print_r(get_option('adredux_settings'), true));
$plugininfo = get_plugin_data( ADREDUX_DIR.'adredux.php');
?>
<div class="tab-section">
	<h3 class="section-title"><?php _e( 'Plugin Settings Data', 'adredux' ); ?></h3>

				<p><?php _e('Here is the raw data as saved in the WordPress options settings of your website. All of your Ad Redux settings are saved in WordPress options, in the adredux_settings option.','adredux');?></p>
				 
				<code><?php

						if ($rawdata=='') {
							_e('Empty! When you save your plugin settings, you will find the saved data here. Have fun.', 'adredux');						
						} else {
							echo $rawdata;						
						}

					?></code>

</div><!-- .tab-section -->
