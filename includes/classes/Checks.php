<?php
/**
 * Runs the diagnostic checks
 *
 * @package WordPressDiagnostics
 */

namespace Eighteen73\WordPressDiagnostics;

/**
 * Runs the diagnostic checks
 */
class Checks extends Singleton {

	/**
	 * Runs should be initiated by a cron task
	 */
	public function run() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		ray()->clearAll();

		$theme = wp_get_theme();

		$plugins = $this->parse_plugins( get_plugins() );
		$mu_plugins = $this->parse_plugins( get_mu_plugins(), true );
		$plugins = array_merge( $plugins, $mu_plugins );
		$names  = array_column( $plugins, 'name' );
		array_multisort( $names, SORT_ASC, $plugins );

		ray($_SERVER);

		$data = [
			'url' => get_bloginfo( 'url' ),
			'cms' => [
				'name' => 'wordpress',
				'version' => get_bloginfo( 'version' ),
				'admin_email' => get_bloginfo( 'admin_email' ),
			],
			'theme' => [
				'name' => $theme->get('Name'),
				'version' => $theme->get('Version'),
				'uri' => $theme->get('ThemeURI'),
			],
			'plugins' => $plugins,
			'hosting' => [
				'ip' => $_SERVER['SERVER_ADDR'] ?? null,
				'os' => '// todo',
				'https' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
				'path' => $_SERVER['DOCUMENT_ROOT'] ?? null,
				'server' => $_SERVER['SERVER_SOFTWARE'] ?? null,
				'php' => phpversion(),
				'composer-dev' => '// todo - true or false',
				'git' => [
					'origin' => '// todo - e.g. git@code.orphans.co.uk:websites/repo-name.git',
					'last_commit_date' => '// todo',
					'local_changes' => '// todo - basic counts of modified/untracked files',
				],
			],
		];
		ray( $data );
	}

	/**
	 * Convert WordPress' plugin data into the data we want
	 *
	 * @param array $plugins A list of plugins
	 * @return array
	 */
	private function parse_plugins( array $plugins, $must_use = false ): array {
		$out = [];
		$active_plugins = get_option('active_plugins');
		foreach ( $plugins as $plugin_name => $plugin ) {
			$out[] = [
				'active' => $must_use || in_array($plugin_name, $active_plugins),
				'name' => $plugin['TextDomain'] ?: preg_replace( '/([^\/]+)(\.php|\/.+)/', '$1', $plugin_name ),
				'title' => $plugin['Title'] ?: $plugin['Name'],
				'version' => $plugin['Version'],
				'uri' => $plugin['PluginURI'],
			];
		}
		return $out;
	}
}
