<?php
	// Important: Check if the file is the one
    // that was registered during the uninstall hook.
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit ();

	if ( !current_user_can( 'activate_plugins' ) ) {
        exit ();
	}
    check_admin_referer( 'bulk-plugins' );

	global $wpdb;
	
	// UNAPPLYING ALL THE MOLDS
	$mold = new WC_Molds();
	$molds = $wpdb->get_results('SELECT meta_id FROM '.$wpdb->prefix.'postmeta WHERE meta_key = "_mold"');
	foreach($molds as $mold) {
		$mold->pm_load_from_db($mold->meta_id);
		$mold->pm_unapply_mold();
	}
	// DELETING ALL THE MOLDS
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'postmeta WHERE meta_key = "_mold"');
	