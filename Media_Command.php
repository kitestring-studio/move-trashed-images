<?php

if ( ! defined( 'ABSPATH' ) )
    exit;

use WP_CLI\CommandWithDBObject;
use WP_CLI\Entity\Utils as EntityUtils;
use WP_CLI\Fetchers\Post as PostFetcher;
use WP_CLI\Fetchers\User as UserFetcher;
use WP_CLI\Utils;

/**
 * Manages posts, content, and meta.
 *
 * ## EXAMPLES
 *
 *     # Create a new post.
 *     $ wp post create --post_type=post --post_title='A sample post'
 *     Success: Created post 123.
 *
 *     # Update an existing post.
 *     $ wp post update 123 --post_status=draft
 *     Success: Updated post 123.
 *
 *     # Delete an existing post.
 *     $ wp post delete 123
 *     Success: Trashed post 123.
 *
 * @package wp-cli
 */

if ( ! class_exists( 'WP_CLI' ) || ! class_exists( 'Post_Command' ) ) {
    return;
}

//WP_CLI::add_command( 'media-library', 'Media_Library_Command' );


class Media_Library_Command extends Post_Command {

    /**
     * Deletes an existing post.
     *
     * ## OPTIONS
     *
     * <id>...
     * : One or more IDs of posts to delete.
     *
     * [--force]
     * : Skip the trash bin.
     *
     * [--defer-term-counting]
     * : Recalculate term count in batch, for a performance boost.
     *
     * ## EXAMPLES
     *
     *     # Delete post skipping trash
     *     $ wp post delete 123 --force
     *     Success: Deleted post 123.
     *
     *     # Delete multiple posts
     *     $ wp post delete 123 456 789
     *     Success: Trashed post 123.
     *     Success: Trashed post 456.
     *     Success: Trashed post 789.
     *
     *     # Delete all pages
     *     $ wp post delete $(wp post list --post_type='page' --format=ids)
     *     Success: Trashed post 1164.
     *     Success: Trashed post 1186.
     *
     *     # Delete all posts in the trash
     *     $ wp post delete $(wp post list --post_status=trash --format=ids)
     *     Success: Deleted post 1268.
     *     Success: Deleted post 1294.
     */
    public function delete( $args, $assoc_args ) {
        $defaults   = [ 'force' => false ];
        $assoc_args = array_merge( $defaults, $assoc_args );

        parent::_delete( $args, $assoc_args, [ $this, 'media_delete_callback' ] );
    }

    /**
     * Callback used to delete a post.
     *
     * @param $post_id
     * @param $assoc_args
     *
     * @return array
     */
    protected function media_delete_callback( $post_id, $assoc_args ) {
        $status    = get_post_status( $post_id );
        $post_type = get_post_type( $post_id );

        if ( 'attachment' !== $post_type ) {
            return [ 'error', "Posts of type '{$post_type}' cannot be deleted with this command." ];
        }

        if ( ! $assoc_args['force']
//             && ( 'post' !== $post_type && 'page' !== $post_type )
             && ( ! defined( 'MEDIA_TRASH' ) || ! MEDIA_TRASH ) ) {
            return [
                'error',
                "Posts of type '$post_type' do not support being sent to trash unless MEDIA_TRASH === true.\n"
                . 'Please use the --force flag to skip trash and delete them permanently.',
            ];
        } else {
            // move files here
        }

        if ( ! wp_delete_post( $post_id, $assoc_args['force'] ) ) {
            return [ 'error', "Failed deleting post {$post_id}." ];
        }

        $action = $assoc_args['force'] || 'trash' === $status || 'revision' === $post_type ? 'Deleted' : 'Trashed';

        return [ 'success', "{$action} post {$post_id}." ];
    }

}
