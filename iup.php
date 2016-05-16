<?php
/**
 * Plugin Name: Infinite Update Plugin
 * Plugin URI: https://github.com/turtlepod/iup
 * Description: Plugin with infinite updates. Over and over and over.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/


/* Add our update before it is set */
add_filter( 'pre_set_site_transient_update_plugins', 'iup_update_plugins', 10, 2 );


/**
 * Add this plugin to update array
 * @link https://developer.wordpress.org/reference/functions/set_site_transient/
 * @since 1.0.0
 */
function iup_update_plugins( $value, $transient ){

	/* Check if "response" object is set */
	if( isset( $value->response ) ){

		/* "folder/file.php" */
		$this_plugin = plugin_basename( __FILE__ );

		/* Required data */
		$update = new stdClass;
		$update->slug        = dirname( $this_plugin );
		$update->plugin      = $this_plugin;
		$update->new_version = '2.0.0';

		/* Optional */
		$update->id          = '';
		$update->package     = 'https://github.com/turtlepod/iup/archive/master.zip';
		$update->tested      = '4.5.2';

		/* Add to update data */
		$value->response[$this_plugin] = $update;
	}

	return $value;
}


/* Plugin API result */
add_filter( 'plugins_api_result', 'iup_plugins_api_result', 10, 3 );


/**
 * Plugins API Results
 */
function iup_plugins_api_result( $res, $action, $args ){

	/* "folder/file.php" */
	$this_plugin = plugin_basename( __FILE__ );

	/* Check if this plugin info requested. */
	if( 'plugin_information' == $action && isset( $args->slug ) && $this_plugin == $args->slug ){

		/* Required Data */
		$data = new stdClass;
		$data->name             = 'Infinite Plugin Update';
		$data->slug             = $slug;
		$data->external         = true; // self hosted.
		$data->sections         = array(
			'changelog' => file_get_contents( "changelog.txt", true ),
			'support'   => '<p>Need support? <a href="https://genbumedia.com/contact/?about=IUP">Contact Us</a>.</p>',
		);
		
		/* Optional */
		$data->version          = '2.0.0';
		$data->last_updated     = '2016-05-12'; // YYYY-MM-DD
		$data->download_link    = 'https://github.com/turtlepod/iup/archive/master.zip';
		$data->requires         = '4.0.0';
		$data->tested           = '4.5.2';

		/* Add it */
		$res = $data;

		/* Add it */
		$res = $data;
	}

	return $res;
}


/* Folder Name Fix */
add_filter( 'upgrader_post_install', 'iup_upgrader_post_install', 10, 3 );

/**
 * Fix plugin folder
 * @since 1.0.0
 */
function iup_upgrader_post_install( $true, $hook_extra, $result ){

	/* Check if hook extra is set */
	if ( isset( $hook_extra ) ){

		/* "folder/file.php" */
		$this_plugin = plugin_basename( __FILE__ );
		$plugin_folder = dirname( $this_plugin );

		/* Only filter folder in this plugin only */
		if ( isset( $hook_extra['plugin'] ) && $this_plugin == $hook_extra['plugin'] ){

			/* wp_filesystem api */
			global $wp_filesystem;

			/* Move & Activate */
			$proper_destination = trailingslashit( WP_PLUGIN_DIR ) . $plugin_folder;
			$wp_filesystem->move( $result['destination'], $proper_destination );
			$result['destination'] = $proper_destination;
			$activate = activate_plugin( $this_plugin );

			/* Update message */
			echo is_wp_error( $activate ) ? 'Plugin could not be reactivated.' : 'Plugin reactivated successfully.';
		}
	}

	return $true;
}

