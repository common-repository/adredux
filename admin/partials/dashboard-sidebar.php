<div class="tab-section">

		<div class="wrap" style="overflow: hidden; margin-left: 0;">

			<div class="postbox" style="overflow: hidden; padding-left: 30px;">

				<?php _e('<h3>Contribute & Support</h3>', 'adredux'); ?>

					<?php $plugininfo = get_plugin_data( ADREDUX_DIR . 'adredux.php' ); ?>

					<img src="<?php echo ADREDUX_DIR_URI .'assets/icon.png'; ?>" alt="<?php echo $plugininfo['Name'];?>" style="float: right; align: left; margin: 0 30px 20px 20px; max-width: 100px; height: auto; border: 3px solid #f9f9f9;">
	
					<div class="about-text">	
						<p class="about-description">🚨 <span class="dashicons dashicons-heart" style="color: #f21a1a;"></span>  <?php _e('<strong>Found the AdRedux plugin useful?</strong> If you like the plugin, keep the plugin development going through your contributions. Consider supporting us with some money for coffee. Send some love & contributions.','adredux');?> <span class="dashicons dashicons-editor-help"></span> <?php _e('For support, drop an email to','adredux');?> <code>contact@reduxthemes.com</code></p>
						<p><?php _e('Want some cool <strong>WordPress themes</strong> for you website? Preview the themes in your website:','adredux');?> <a href="<?php echo esc_url_raw( add_query_arg( array('search'=> 'undedicated'), admin_url( 'theme-install.php' ) ) ); ?>"><?php _e('Undedicated','adredux');?></a>, <a href="<?php echo esc_url_raw( add_query_arg( array('search'=> 'prakashan'), admin_url( 'theme-install.php' ) ) ); ?>"><?php _e('Prakashan','adredux');?></a></p>
				<a href="https://reduxthemes.com/donate/" class="button button-primary"><?php _e('Donate & Support Us','adredux');?></a>
	
					</div>

			</div>

		</div>
		
</div><!-- .tab-section -->