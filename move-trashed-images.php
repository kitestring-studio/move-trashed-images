<?php
/*
Plugin Name: Move Trashed Images
Description: Move trashed images to a different directory on the server.
Version: 1.0
Author: Your Name
*/

// Move trashed images to trash_images directory.
add_action( 'wp_trash_post', 'move_trashed_images' );
function move_trashed_images( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type == 'attachment' ) {
        $attachment_meta = wp_get_attachment_metadata( $post_id );
        if ( ! empty( $attachment_meta ) ) {
            $upload_dir = wp_upload_dir();
            $original_file_path = trailingslashit( $upload_dir['basedir'] ) . $attachment_meta['file'];
            $original_file_name = basename( $original_file_path );
            $original_file_dirname = dirname( $original_file_path );
            $trash_images_dirname = trailingslashit( $original_file_dirname ) . 'trash_images';

            if ( ! file_exists( $trash_images_dirname ) ) {
                mkdir( $trash_images_dirname );
            }

            // Move original file to trash_images directory.
            rename( $original_file_path, trailingslashit( $trash_images_dirname ) . $original_file_name );

            // Move resized images to trash_images directory.
            foreach ( $attachment_meta['sizes'] as $size_name => $size_info ) {
                $resized_file_path = trailingslashit( $original_file_dirname ) . $size_info['file'];
                if ( file_exists( $resized_file_path ) ) {
                    $resized_file_name = basename( $resized_file_path );
                    rename( $resized_file_path, trailingslashit( $trash_images_dirname ) . $resized_file_name );
                }
            }
        }
    }
}

// Move restored images back to original directory.
add_action( 'untrash_post', 'move_restored_images' );
function move_restored_images( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type == 'attachment' ) {
        $attachment_meta = wp_get_attachment_metadata( $post_id );
        if ( ! empty( $attachment_meta ) ) {
            $upload_dir            = wp_upload_dir();
            $original_file_path    = trailingslashit( $upload_dir['basedir'] ) . $attachment_meta['file'];
            $original_file_name    = basename( $original_file_path );
            $original_file_dirname = dirname( $original_file_path );
            $trash_images_dirname  = trailingslashit( $original_file_dirname ) . 'trash_images';

            if ( file_exists( trailingslashit( $trash_images_dirname ) . $original_file_name ) ) {
                // Move original file back to original directory.
                rename( trailingslashit( $trash_images_dirname ) . $original_file_name, $original_file_path );

                // Move resized images back to original directory.
                foreach ( $attachment_meta['sizes'] as $size_name => $size_info ) {
                    $resized_file_path = trailingslashit( $upload_dir['basedir'] ) . $size_info['file'];
                    if ( file_exists( trailingslashit( $trash_images_dirname ) . basename( $resized_file_path ) ) ) {
                        rename( trailingslashit( $trash_images_dirname ) . basename( $resized_file_path ), $resized_file_path );
                    }
                }
// Remove the trash_images directory if it is empty.
                $files = scandir( $trash_images_dirname );
                if ( count( $files ) == 2 ) {
                    rmdir( $trash_images_dirname );
                }
            }
        }
    }
}
