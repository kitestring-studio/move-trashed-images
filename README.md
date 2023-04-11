# WordPress Trash Images

A WordPress plugin that moves trashed images to a separate directory on the server, and restores them to their original location if they are untrashed.

## Features

- Moves trashed images and their corresponding resized versions to a "trash_images" directory located next to the "uploads" directory.
- Automatically restores trashed images to their original location if they are untrashed.
- Logs the details of the trashed images and their new locations, if enabled via the `--log` option flag.
- Extends the `wp media` command with a new `delete` subcommand to trash images via the WP-CLI.

## Requirements

- WordPress 5.8 or later.
- PHP 7.4 or later.

## Installation

1. Download the plugin ZIP file from the latest release on the [releases page](https://github.com/kitestring-studio/wordpress-trash-images/releases).
2. Install the plugin via the WordPress dashboard or by uploading the ZIP file via FTP.

## Usage

Once the plugin is activated, trashed images and their resized versions will be moved to the "trash_images" directory. Untrashing an image will automatically restore it to its original location.

You can also use the WP-CLI to trash images via the `wp media delete` command:

`wp media delete <attachment-id>`


The `--log` option flag can be used to generate a log file of the trashed images and their new locations.

## Contributing

If you find any issues or have any feature requests, please file them in the [issue tracker](https://github.com/kitestring-studio/wordpress-trash-images/issues).

If you would like to contribute code changes, please fork the repository, create a new branch, and submit a pull request.

## License

This plugin is released under the MIT License.
