<?php
/*
Plugin Name: Move Trashed Images
Description: Move trashed images to a different directory on the server.
Version: 1.0.1
Author: Gabe Herbert
GitHub Plugin URI: https://github.com/kitestring-studio/move-trashed-images
*/

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

//require_once "Media_Command.php";
require_once "WP_Media_Delete_Command.php";
require_once "functions.php";

// Move trashed images to trash_images directory.

if ( defined( 'MEDIA_TRASH' ) && MEDIA_TRASH ) {
    add_action( 'untrash_post', 'move_restored_images' );
    add_action( 'wp_trash_post', 'move_trashed_images' );
}

function register_wp_media_delete_command() {
    WP_CLI::add_command( 'media delete', 'WP_Media_Delete_Command' );
}
add_action( 'cli_init', 'register_wp_media_delete_command' );
