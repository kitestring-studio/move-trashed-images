<?php

if ( ! defined( 'ABSPATH' ) ) {
    die();
}

if ( ! class_exists( 'WP_CLI' ) || ! class_exists( 'WP_CLI_Command' ) ) {
    return;
}

/**
 * Implements 'wp media delete' command.
 */
class WP_Media_Delete_Command extends WP_CLI_Command {

    /**
     * Trashes media file and moves it to trash directory.
     *
     * ## OPTIONS
     *
     * <id>
     * : ID of the media file to delete.
     *
     * [--force]
     * : Skip the trash bin.
     *
     * ## EXAMPLES
     *
     * # Trash a Media Library attachment
     * $ wp media delete 123
     * Success: Trashed post 123.
     *
     * @param array $args Command arguments.
     * @param array $assoc_args Command associative arguments.
     */
    public function __invoke( $args, $assoc_args ) {

        $defaults   = [
            'force' => false,
        ];
        $assoc_args = array_merge( $defaults, $assoc_args );

        $post_id   = absint( $args[0] );
        $status    = get_post_status( $post_id );
        $post_type = get_post_type( $post_id );

        if ( 'attachment' !== $post_type ) {
            WP_CLI::error( "Posts of type '{$post_type}' cannot be deleted with this command." );
        }

        if ( ! $assoc_args['force']
             //             && ( 'post' !== $post_type && 'page' !== $post_type )
             && ( ! defined( 'MEDIA_TRASH' ) || ! MEDIA_TRASH ) ) {
            WP_CLI::error( "Posts of type '$post_type' do not support being sent to trash unless MEDIA_TRASH === true.\n"
                            . 'Please use the --force flag to skip trash and delete them permanently.' );
        } else {
            // move files here?
        }

        if ( ! wp_delete_post( $post_id, $assoc_args['force'] ) ) {
            WP_CLI::error( "Error moving media attachment {$post_id} to trash." );
        } else {
            $action = $assoc_args['force'] || 'trash' === $status || 'revision' === $post_type ? 'Deleted' : 'Trashed';
            WP_CLI::success( "Media attachment {$post_id} {$action}" );
        }

    }
}
