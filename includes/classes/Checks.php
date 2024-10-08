<?php
/**
 * Gathers data that's useful for diagnostic checks. Nothing collected is PII nor does it contain information
 * that would be otherwise considered as highly sensitive.
 *
 * @package Beacon
 */

namespace Eighteen73\Beacon;

/**
 * Gathers data that's useful for diagnostic checks
 */
class Checks {

	/**
	 * The presumed project  root for this website.
	 *
	 * @var string|null
	 */
	private static ?string $root_path = null;

	/**
	 * Runs should be initiated by a cron task
	 */
	public static function run(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$theme = wp_get_theme();

		$plugins    = self::parse_plugins( get_plugins() );
		$mu_plugins = self::parse_plugins( get_mu_plugins(), true );
		$plugins    = array_merge( $plugins, $mu_plugins );
		$names      = array_column( $plugins, 'name' );
		array_multisort( $names, SORT_ASC, $plugins );

		self::$root_path = self::get_root_path();
		$git_origin      = self::git_origin();
		$git_date        = self::git_date();

		return [
			// We only pass the major version. Only breaking changes should prompt a new version.
			'beacon'    => [
				'version' => 1,
			],
			'cms'       => [
				'contact' => get_bloginfo( 'admin_email' ),
				'name'    => 'wordpress',
				'version' => get_bloginfo( 'version' ),
			],
			'theme'     => [
				'name'    => $theme->get( 'Name' ),
				'uri'     => $theme->get( 'ThemeURI' ),
				'version' => $theme->get( 'Version' ),
			],
			'plugins'   => $plugins,
			'technical' => [
				'git' => [
					'last_commit_date' => $git_date,
					'origin'           => $git_origin,
					'path'             => self::$root_path,
				],
				'os'  => [
					'architecture' => php_uname( 'm' ),
					'hostname'     => php_uname( 'n' ),
					'name'         => php_uname( 's' ),
					'version'      => php_uname( 'r' ),
				],
				'php' => [
					'composer-dev' => self::has_dev_packages(),
					'interface'    => php_sapi_name(),
					'version'      => phpversion(),
				],
				'web' => [
					'domain'   => $_SERVER['HTTP_HOST'],
					'https'    => isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on',
					'ip'       => $_SERVER['SERVER_ADDR'] ?? null,
					'path'     => $_SERVER['DOCUMENT_ROOT'] ?? null,
					'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? null,
					'server'   => $_SERVER['SERVER_SOFTWARE'] ?? null,
					'url'      => get_bloginfo( 'url' ),
				],
			],
		];
	}

	/**
	 * Convert WordPress' plugin data into the data we want
	 *
	 * @param array $plugins A list of plugins
	 * @param bool  $must_use Implies these are effectively active even if not actually activated
	 * @return array
	 */
	private static function parse_plugins( array $plugins, bool $must_use = false ): array {
		$out            = [];
		$active_plugins = get_option( 'active_plugins' );
		foreach ( $plugins as $plugin_name => $plugin ) {
			$out[] = [
				'active'  => $must_use || in_array( $plugin_name, $active_plugins, true ),
				'name'    => $plugin['TextDomain'] ?: preg_replace( '/([^\/]+)(\.php|\/.+)/', '$1', $plugin_name ),
				'title'   => $plugin['Title'] ?: $plugin['Name'],
				'uri'     => $plugin['PluginURI'],
				'version' => $plugin['Version'],
			];
		}
		return $out;
	}

	/**
	 * Get the presumed project root path for this website based on where the .git directory is found
	 *
	 * @return string
	 */
	private static function get_root_path(): ?string {
		// Try up to 3 levels
		$wp_dir   = '/' . trim( ABSPATH, '/' );
		$try_dirs = [
			$wp_dir,
			dirname( $wp_dir ),
			dirname( $wp_dir, 2 ),
		];
		foreach ( $try_dirs as $dir ) {
			$git_dir = "{$dir}/.git";
			if ( file_exists( $git_dir ) && is_dir( $git_dir ) ) {
				return $dir;
			}
			$dir = null;
		}

		return null;
	}

	/**
	 * Get the git origin address. This does presume it's called "origin"
	 *
	 * @return string|null
	 */
	private static function git_origin(): ?string {
		if ( ! self::$root_path ) {
			return null;
		}
		$cmd      = 'git -C ' . escapeshellarg( self::$root_path ) . ' remote get-url origin';
		$response = exec( $cmd );
		return $response ?: null;
	}

	/**
	 * Get the date of the most recent commit
	 *
	 * @return string|null
	 */
	private static function git_date(): ?string {
		if ( ! self::$root_path ) {
			return null;
		}
		$cmd      = 'git -C ' . escapeshellarg( self::$root_path ) . ' log -1 --format=%cd';
		$response = exec( $cmd );
		return $response ?: null;
	}

	/**
	 * Determine whether this website has dev packages installed or not
	 *
	 * @return bool
	 */
	private static function has_dev_packages(): bool {
		$cmd              = 'composer -d ' . escapeshellarg( self::$root_path ) . ' show -N';
		$all_packages     = shell_exec( $cmd );
		$cmd              = 'composer -d ' . escapeshellarg( self::$root_path ) . ' show -N --no-dev';
		$non_dev_packages = shell_exec( $cmd );
		return $all_packages !== $non_dev_packages;
	}
}
