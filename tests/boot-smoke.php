<?php
/**
 * Boots the plugin without activating it and renders every admin tab.
 * Run from the site root: wp eval-file wp-content/plugins/smp-contributor-network/tests/boot-smoke.php
 */

$admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
wp_set_current_user( (int) ( $admins[0] ?? 0 ) );

require_once dirname( __DIR__ ) . '/smp-contributor-network.php';
\SMP\ContributorNetwork\PluginRuntime\CoreIntegration::boot();

$page   = new \SMP\ContributorNetwork\AdminDashboard\AdminPage();
$failed = 0;

foreach ( [ 'overview', 'program', 'pages', 'update-center', 'hexa-core' ] as $tab ) {
    ob_start();
    $page->render_tab( $tab );
    $html = (string) ob_get_clean();
    $ok   = strlen( $html ) > 200 && ( 'update-center' !== $tab || str_contains( $html, 'Git Report' ) );
    $failed += $ok ? 0 : 1;
    echo ( $ok ? 'PASS ' : 'FAIL ' ) . $tab . "\n";
}

exit( $failed ? 1 : 0 );
