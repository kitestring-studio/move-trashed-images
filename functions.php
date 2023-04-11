<?php

if ( ! defined( 'ABSPATH' ) ) {
    die;
}


function move_trashed_images( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type === 'attachment' ) {
        $attachment_meta = wp_get_attachment_metadata( $post_id );
        if ( ! empty( $attachment_meta ) ) {
            $upload_dir            = wp_upload_dir();
            $original_file_path    = trailingslashit( $upload_dir['basedir'] ) . $attachment_meta['file'];
            $original_file_name    = basename( $original_file_path );
            $original_file_dirname = trailingslashit( dirname( $original_file_path ) );
            $trash_images_dirname  = trailingslashit( str_replace( 'uploads', 'trash_images', $original_file_dirname ) );
            $trashed_image_path    = trailingslashit( $trash_images_dirname ) . $original_file_name;

            if ( ! file_exists( $trash_images_dirname ) ) {
                mkdir( $trash_images_dirname, 0777, true );
            }

            // Move original file to trash_images directory.
            if ( file_exists( $trash_images_dirname ) && rename( $original_file_path, $trashed_image_path ) ) {

                // Move resized images to trash_images directory.
                foreach ( $attachment_meta['sizes'] as $size_name => $size_info ) {
                    $resized_file_path = $original_file_dirname . $size_info['file'];
                    if ( file_exists( $resized_file_path ) ) {
                        $resized_file_name = basename( $resized_file_path );
                        rename( $resized_file_path, $trash_images_dirname . $resized_file_name );
                    }
                }
            }
        }
    }
}

// Move restored images back to original directory.
function move_restored_images( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type === 'attachment' ) {
        $attachment_meta = wp_get_attachment_metadata( $post_id );
        if ( ! empty( $attachment_meta ) ) {
            $upload_dir            = wp_upload_dir();
            $original_file_path    = trailingslashit( $upload_dir['basedir'] ) . $attachment_meta['file'];
            $original_file_name    = basename( $original_file_path );
            $original_file_dirname = trailingslashit( dirname( $original_file_path ) );
            $trash_images_dirname  = trailingslashit( str_replace( 'uploads', 'trash_images', $original_file_dirname ) );
            $trashed_image_path    = trailingslashit( $trash_images_dirname ) . $original_file_name;

            if ( file_exists( $trashed_image_path )
                 // Move original file back to original directory.
                 && rename( $trash_images_dirname . $original_file_name, $original_file_path )
            ) {
                // Move resized images back to original directory.
                foreach ( $attachment_meta['sizes'] as $size_name => $size_info ) {
                    $resized_file_path = $original_file_dirname . $size_info['file'];
                    if ( file_exists( $trash_images_dirname . basename( $resized_file_path ) ) ) {
                        rename( $trash_images_dirname . basename( $resized_file_path ), $resized_file_path );
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
