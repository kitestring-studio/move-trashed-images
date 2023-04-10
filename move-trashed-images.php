<?php
/*
Plugin Name: Move Trashed Images
Description: Move trashed images to a different directory on the server.
Version: 1.0
Author: Your Name
*/

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

/*
add_filter( 'pre_delete_post', 'delete_trashed_images', 10, 3 );
add_filter( 'pre_delete_attachment', 'delete_trashed_images', 10, 3 );

function delete_trashed_images( $delete, $post, $force_delete ) {
    $post_type = get_post_type( $post );
    if ( $post_type == 'attachment' ) {
        $delete = false;
    }

    return false;
}*/
