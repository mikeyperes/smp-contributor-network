<?php

namespace SMP\ContributorNetwork\Updates;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\CorePackageUpdates\CorePackageAjaxController;
use Hexa\PluginCore\CorePackageUpdates\CorePackageConfig;
use Hexa\PluginCore\CorePackageUpdates\CorePackagePanelRenderer;
use Hexa\PluginCore\PluginUpdates\GitHubPluginUpdater;
use Hexa\PluginCore\PluginUpdates\UpdaterAjaxController;
use Hexa\PluginCore\PluginUpdates\UpdaterConfig;
use Hexa\PluginCore\PluginUpdates\UpdaterPanelRenderer;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * The update-center tab: Core's plugin updater (with its Git Report) and
 * Core's bundled-package panel.
 */
final class UpdateCenter implements ModuleInterface {
    public function register(): void {
        ( new GitHubPluginUpdater( self::plugin_config() ) )->register();
        ( new UpdaterAjaxController( self::plugin_config() ) )->register();
        ( new CorePackageAjaxController( self::core_config() ) )->register();
    }

    public static function render(): void {
        ( new UpdaterPanelRenderer( self::plugin_config() ) )->render();
        ( new CorePackagePanelRenderer( self::core_config() ) )->render();
    }

    public static function plugin_config(): UpdaterConfig {
        static $config = null;

        return $config ??= UpdaterConfig::from_plugin_file(
            PluginMetadata::plugin_file(),
            PluginMetadata::GITHUB_REPOSITORY,
            [
                'github_branch'      => PluginMetadata::GITHUB_BRANCH,
                'nonce_action'       => PluginMetadata::nonce_action(),
                'nonce_param'        => 'nonce',
                'ajax_action_prefix' => PluginMetadata::HOOK_PREFIX,
            ]
        );
    }

    public static function core_config(): CorePackageConfig {
        static $config = null;

        return $config ??= CorePackageConfig::from_core_root(
            PluginMetadata::core_root(),
            [
                'github_repo'        => 'mikeyperes/hexa-wordpress-plugin-core',
                'github_branch'      => 'main',
                'nonce_action'       => PluginMetadata::nonce_action(),
                'nonce_param'        => 'nonce',
                'ajax_action_prefix' => PluginMetadata::HOOK_PREFIX . '_core_package',
                'cache_key'          => PluginMetadata::HOOK_PREFIX . '_hexa_plugin_core_package',
            ]
        );
    }
}
