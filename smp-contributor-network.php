<?php
/*
Plugin Name: SMP Contributor Network
Description: Contributor Network (leadership council) program for Scale My Publication sites.
Author: Michael Peres
Plugin URI: https://github.com/mikeyperes/smp-contributor-network
Version: 1.3.5
Requires at least: 6.0
Requires PHP: 8.1
Text Domain: smp-contributor-network
Author URI: https://michaelperes.com
GitHub Plugin URI: https://github.com/mikeyperes/smp-contributor-network/
GitHub Branch: main
*/

defined( 'ABSPATH' ) || exit;

if ( defined( 'SMP_CONTRIBUTOR_NETWORK_BOOTSTRAPPED' ) ) {
    return;
}

define( 'SMP_CONTRIBUTOR_NETWORK_BOOTSTRAPPED', true );
define( 'SMP_CONTRIBUTOR_NETWORK_FILE', __FILE__ );

require_once __DIR__ . '/src/PluginRuntime/Autoloader.php';

\SMP\ContributorNetwork\PluginRuntime\Autoloader::register( __DIR__ . '/src' );

$smp_contributor_network_core_root = __DIR__ . '/lib/hexa-wordpress-plugin-core';
require_once $smp_contributor_network_core_root . '/bootstrap.php';
\hexa_plugin_core_register_package( 'smp-contributor-network', $smp_contributor_network_core_root );

add_action( 'plugins_loaded', [ \SMP\ContributorNetwork\PluginRuntime\CoreIntegration::class, 'boot' ], -PHP_INT_MAX + 100 );
