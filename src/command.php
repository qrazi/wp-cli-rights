<?php

if ( ! defined( 'WP_CLI' ) ) {
	return;
}

require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

WP_CLI::add_command( 'rights', 'qrazi\\Rights\\Command' );