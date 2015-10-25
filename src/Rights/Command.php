<?php

namespace qrazi\Rights;

use stdClass;
use WP_CLI;
use WP_CLI\Formatter;
use WP_CLI_Command;

/**
 * Lists extended information on `role` or `cap`
 *
 * @todo:
 * - cover complete API of `role` and `cap`.
 * - either make this extra info an optional argument or seperate command
 * - unittests
 * - also test fields... are they properly commented?
 */
class Command extends WP_CLI_Command {

	/**
	 * List all roles with all the capabilities of a role.
	 *
	 * ## OPTIONS
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific object fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each role:
	 *
	 * * roles
	 * * [any of the capabilities]
	 *
	 * There are no optional fields.
	 *
	 * ## EXAMPLES
	 *
	 *     wp rights list-role --fields=roles --format=csv
	 *
	 * @subcommand list-role
	 */
	public function list_role( $args, $assoc_args ) {
		global $wp_roles;

		$output_matrix = [ ];
		$capabilities  = [ ];

		foreach ( $wp_roles->roles as $key => $role ) {
			$capabilities = array_merge( $capabilities,
				array_keys( $role['capabilities'] ) );
			break;
		}
		$fields = array_merge( [ 'roles', ], $capabilities );
		$row_keys      = array_fill_keys( $fields, null );

		foreach ( $wp_roles->roles as $key => $role ) {
			if(!isset($output_matrix[$key])){
				// @todo: niet array merge, maar copy/clone/ etc...
				$output_matrix[$key] = array_merge($row_keys);
			}
			$output_matrix[$key]['roles'] = $key;
			foreach ( $role['capabilities'] as $capability => $has_it ) {
				$output_matrix[ $key ][ $capability ] = 'Y';
			}
		}

		$formatter = new Formatter( $assoc_args, $fields );
		$formatter->display_items( $output_matrix );
	}

	/**
	 * List all capabilities and show roles that have capability.
	 *
	 * ## OPTIONS
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific object fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each role:
	 *
	 * * capabilities
	 * * [any of the roles]
	 *
	 * There are no optional fields.
	 *
	 * ## EXAMPLES
	 *
	 *     wp rights list-cap --fields=role --format=csv
	 *
	 * @subcommand list-cap
	 */
	public function list_cap( $args, $assoc_args ) {
		global $wp_roles;

		$output_matrix = [ ];
		$roles         = array_keys( $wp_roles->role_names );
		$fields        = array_merge( [ 'capabilities', ], $roles );
		$row_keys      = array_fill_keys( $roles, null );

		foreach ( $wp_roles->roles as $key => $role ) {
			foreach ( $role['capabilities'] as $capability => $has_it ) {
				if ( ! isset( $output_matrix[ $capability ] ) ) {
					$output_matrix[ $capability ] = array_merge( [ 'capabilities' => $capability, ],
						$row_keys );
				}

				$output_matrix[ $capability ][ $key ] = 'Y';
			}
		}

		$formatter = new Formatter( $assoc_args, $fields );
		$formatter->display_items( $output_matrix );
	}
}