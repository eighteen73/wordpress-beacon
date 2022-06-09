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

		$plugins = $this->parse_plugins( get_plugins() );
		$mu_plugins = $this->parse_plugins( get_mu_plugins() );
		$plugins = array_merge( $plugins, $mu_plugins );
		$names  = array_column( $plugins, 'name' );
		array_multisort( $names, SORT_ASC, $plugins );

		$data = [
			'url' => get_bloginfo( 'url' ),
			'cms' => [
				'name' => 'wordpress',
				'version' => get_bloginfo( 'version' ),
				'admin_email' => get_bloginfo( 'admin_email' ),
			],
			'plugins' => $plugins,
			'php' => [
				'version' => phpversion(),
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
	private function parse_plugins( array $plugins ): array {
		$out = [];
		foreach ( $plugins as $plugin_name => $plugin ) {
			$out[] = [
				'name' => $plugin['TextDomain'] ?: preg_replace( '/([^\/]+)(\.php|\/.+)/', '$1', $plugin_name ),
				'title' => $plugin['Title'] ?: $plugin['Name'],
				'version' => $plugin['Version'],
				'uri' => $plugin['PluginURI'],
			];
		}
		return $out;
	}
}
