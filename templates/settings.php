<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('JWT Auth Settings', JWT_AUTH_LANG); ?></h2>
    <?php if( count(get_settings_errors()) == 0 && isset($_GET['settings-updated']) ) { ?>
        <div id="message" class="updated">
            <p><strong><?php _e('Settings saved.') ?></strong></p>
        </div>
    <?php } ?>
    <?php settings_errors(); ?>
	<form action="options.php" method="post">
		<?php settings_fields( JWT_AUTH_Options::OPTIONS_NAME ); ?>
		<?php do_settings_sections( JWT_AUTH_Options::OPTIONS_NAME ); ?>
		<?php submit_button(); ?>
	</form>
</div>
