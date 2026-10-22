<?php

namespace SMP\ContributorNetwork\AdminDashboard;

use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

final class OverviewTab {
    public static function render(): void {
        ?>
        <div class="hpc-ui">
            <h2>Contributor Network</h2>
            <p>Runs a site's contributor network program, such as the Her Forward Leadership Council.</p>
            <h3>Version</h3>
            <p><code><?php echo esc_html( PluginMetadata::VERSION ); ?></code></p>
            <h3>Repository</h3>
            <p><a href="<?php echo esc_url( 'https://github.com/' . PluginMetadata::GITHUB_REPOSITORY ); ?>" target="_blank" rel="noopener"><?php echo esc_html( 'https://github.com/' . PluginMetadata::GITHUB_REPOSITORY ); ?></a></p>
        </div>
        <?php
    }
}
