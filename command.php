<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

use WP_CLI\Utils;
use WP_CLI\Process;

class Scaffold_Theme_Tests_Command
{
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
	 * * `tests/bootstrap.php` is the file that makes the current theme active when running the test suite.
	 * * `tests/test-sample.php` is a sample file containing the actual tests.
	 *
	 * Learn more from the [theme unit tests documentation](http://wp-cli.org/docs/theme-unit-tests/).
	 *
	 * ## ENVIRONMENT
	 *
	 * The `tests/bootstrap.php` file looks for the WP_TESTS_DIR environment
	 * variable.
	 *
	 * ## OPTIONS
	 *
	 * [<theme>]
	 * : The name of the theme to generate test files for.
	 *
	 * [--dir=<dirname>]
	 * : Generate test files for a non-standard theme path. If no theme slug is specified, the directory name is used.
	 *
	 * [--force]
	 * : Overwrite files that already exist.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp scaffold theme-tests sample-theme
	 *     Success: Created test files.
	 *
	 * @subcommand theme-tests
	 */
	function __invoke( $args, $assoc_args )
	{
		$wp_filesystem = $this->init_wp_filesystem();

		if ( ! empty( $args[0] ) ) {
			$theme_slug = $args[0];
			$theme_dir = WP_CONTENT_DIR . '/themes' . "/$theme_slug";
			if ( empty( $assoc_args['dir'] ) && ! is_dir( $theme_dir ) ) {
				WP_CLI::error( 'Invalid theme slug specified.' );
			}
		}

		if ( ! empty( $assoc_args['dir'] ) ) {
			$theme_dir = $assoc_args['dir'];
			if ( ! is_dir( $theme_dir ) ) {
				WP_CLI::error( 'Invalid theme directory specified.' );
			}
			if ( empty( $theme_slug ) ) {
				$theme_slug = basename( $theme_dir );
			}
		}

		if ( empty( $theme_slug ) || empty( $theme_dir ) ) {
			WP_CLI::error( 'Invalid theme specified.' );
		}

		$theme_name    = ucwords( str_replace( '-', ' ', $theme_slug ) );
		$theme_package = str_replace( ' ', '_', $theme_name );

		$force = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force' );

		$theme_data = array();
		$template_dir = dirname( __FILE__ ) . '/templates';

		$files_to_create = array(
			"$theme_dir/.gitignore"   => Utils\mustache_render( $template_dir . '/.gitignore', $theme_data ),
			"$theme_dir/.travis.yml" => Utils\mustache_render( $template_dir . '/.travis.yml', $theme_data ),
			"$theme_dir/composer.json" => Utils\mustache_render( $template_dir . '/composer.json', $theme_data ),
			"$theme_dir/composer.lock" => Utils\mustache_render( $template_dir . '/composer.lock', $theme_data ),
		);

		$files_written = $this->create_files( $files_to_create, $force );

		$this->log_whether_files_written(
			$files_written,
			$skip_message = 'All test files were skipped.',
			$success_message = 'Created test files.'
		);
	}

	private function create_files( $files_and_contents, $force ) {
		$wp_filesystem = $this->init_wp_filesystem();
		$wrote_files = array();
		foreach ( $files_and_contents as $filename => $contents ) {
			$should_write_file = $this->prompt_if_files_will_be_overwritten( $filename, $force );
			if ( ! $should_write_file ) {
				continue;
			}
			$wp_filesystem->mkdir( dirname( $filename ) );
			if ( ! $wp_filesystem->put_contents( $filename, $contents ) ) {
				WP_CLI::error( "Error creating file: $filename" );
			} elseif ( $should_write_file ) {
				$wrote_files[] = $filename;
			}
		}
		return $wrote_files;
	}

	private function prompt_if_files_will_be_overwritten( $filename, $force ) {
		$should_write_file = true;
		if ( ! file_exists( $filename ) ) {
			return true;
		}
		WP_CLI::warning( 'File already exists.' );
		WP_CLI::log( $filename );
		if ( ! $force ) {
			do {
				$answer = cli\prompt(
					'Skip this file, or replace it with scaffolding?',
					$default = false,
					$marker = '[s/r]: '
				);
			} while ( ! in_array( $answer, array( 's', 'r' ) ) );
			$should_write_file = 'r' === $answer;
		}
		$outcome = $should_write_file ? 'Replacing' : 'Skipping';
		WP_CLI::log( $outcome . PHP_EOL );
		return $should_write_file;
	}

	private function log_whether_files_written( $files_written, $skip_message, $success_message ) {
		if ( empty( $files_written ) ) {
			WP_CLI::log( $skip_message );
		} else {
			WP_CLI::success( $success_message );
		}
	}

	private function init_wp_filesystem() {
		global $wp_filesystem;
		WP_Filesystem();
		return $wp_filesystem;
	}
}

WP_CLI::add_command( 'scaffold theme-tests', 'Scaffold_Theme_Tests_Command' );
