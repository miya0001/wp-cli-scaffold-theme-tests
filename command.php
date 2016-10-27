<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

use WP_CLI\Utils;
use WP_CLI\Process;

/**
 * Generate files needed for running tests for a WordPress theme.
 *
 * ## OVERVIEW
 *
 * The following files are generated for your theme by this command:
 *
 * * `phpunit.xml.dist` is the configuration file for PHPUnit.
 * * `.travis.yml` is the configuration file for Travis CI. Use `--ci=<provider>` to select a different service.
 * * `bin/install-wp-tests.sh` configures the WordPress test suite and a test database.
 * * `tests/bootstrap.php` is the file that makes the current plugin active when running the test suite.
 * * `tests/test-sample.php` is a sample file containing the actual tests.
 *
 * Learn more from the [plugin unit tests documentation](http://wp-cli.org/docs/plugin-unit-tests/).
 *
 * ## ENVIRONMENT
 *
 * The `tests/bootstrap.php` file looks for the WP_TESTS_DIR environment
 * variable.
 *
 * ## OPTIONS
 *
 * [<plugin>]
 * : The name of the plugin to generate test files for.
 *
 * [--dir=<dirname>]
 * : Generate test files for a non-standard plugin path. If no plugin slug is specified, the directory name is used.
 *
 * [--ci=<provider>]
 * : Choose a configuration file for a continuous integration provider.
 * ---
 * default: travis
 * options:
 *   - travis
 *   - circle
 *	 - gitlab
 * ---
 *
 * [--force]
 * : Overwrite files that already exist.
 *
 * ## EXAMPLES
 *
 *     $ wp scaffold plugin-tests sample-plugin
 *     Success: Created test files.
 *
 * @subcommand theme-tests
 */
$wp_cli_scaffold_theme_tests = function() {
	WP_CLI::success( "Hello world." );
};

WP_CLI::add_command( 'scaffold theme-tests', $wp_cli_scaffold_theme_tests );
