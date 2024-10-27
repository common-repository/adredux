<?php
$plugininfo = get_plugin_data( ADREDUX_DIR.'adredux.php');
?>

<div class="tab-section">
	<h3 class="section-title"><?php _e('Ad Redux &mdash; Insert Ads & Codes', 'adredux'); ?></h3>


		<img src="<?php echo ADREDUX_DIR_URI .'assets/banner.png'; ?>" alt="<?php echo $plugininfo['Name'];?>" style="max-width: 99%; height: auto; border: 3px solid #f9f9f9;">				
	
		<div class="about-text">

				<ul>
				<li><strong><?php _e('Plugin:', 'adredux'); ?></strong> <a href="<?php echo $plugininfo['PluginURI'];?>" rel="designer"><?php echo $plugininfo['Name'];?></a></li>
				<li><strong><?php _e('Author:', 'adredux'); ?></strong> <a href="<?php echo $plugininfo['AuthorURI'];?>" rel="designer"><?php echo $plugininfo['Author'];?></a></li>
				<li><strong><?php _e('Plugin URL:', 'adredux'); ?></strong> <a href="<?php echo $plugininfo['PluginURI'];?>" rel="designer"><?php echo $plugininfo['PluginURI'];?></a></li>
				<li><strong><?php _e('Version:', 'adredux'); ?></strong> <?php echo $plugininfo['Version'];?></li>
				</ul>
				
				<hr>
				
			<?php _e('<ul style="list-style-type: square; margin-left: 20px;">
				<li>Add Google Analytics or other tracking codes</li>
				<li>Insert Google Tag Manager codes</li>
				<li>Use Google Adsense or other advertising codes</li>
				<li>Automatically insert ads within posts and pages at desired locations</li>
				<li>Insert advertisements at effective locations
				<ul style="list-style-type: circle; margin-left: 20px;"><li>Above content</li><li>After first/second para</li><li>Middle of posts</li><li>End of content</li></ul></li>
				<li>Define ad alignment styles: left, right, center or random</li>
				<li>Set ad section width to make responsive advertisement codes show large rectangle box for better visibility</li>
				<li>Exclude posts in certain categories from ads</li>
				<li>Saves all the settings in serialized form in just one <strong>adredux_settings</strong> option in WordPress settings to avoid clutter</li>
				<li>No clutter in database: the plugin data is deleted from your website when you <strong>deactive and delete</strong> the plugin</li>
			</ul>
			
			<hr/>','adredux'); ?>

		</div>

</div><!-- .tab-section -->