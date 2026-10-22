<?php

namespace SMP\ContributorNetwork\PluginRuntime;

use Hexa\PluginCore\CoreBootstrap\CoreBootstrap;
use Hexa\PluginCore\CoreRuntime\CorePackageRuntime;
use Hexa\PluginCore\CoreRuntime\PluginContext;
use Hexa\PluginCore\WpAdminTabs\CoreTabConfig;
use Hexa\PluginCore\WpAdminTabs\CoreTabModule;
use SMP\ContributorNetwork\AdminDashboard\AdminPage;
use SMP\ContributorNetwork\Updates\UpdateCenter;

final class CoreIntegration {
    private static ?CoreBootstrap $bootstrap = null;

    public static function boot(): void {
        if ( self::$bootstrap instanceof CoreBootstrap ) {
            return;
        }

        $selected  = CorePackageRuntime::selected_root();
        $core_root = '' !== $selected ? $selected : PluginMetadata::core_root();

        self::$bootstrap = new CoreBootstrap(
            new PluginContext(
                [
                    'slug'        => PluginMetadata::SLUG,
                    'basename'    => plugin_basename( PluginMetadata::plugin_file() ),
                    'version'     => PluginMetadata::VERSION,
                    'path'        => PluginMetadata::root_path(),
                    'url'         => plugin_dir_url( PluginMetadata::plugin_file() ),
                    'github_repo' => PluginMetadata::GITHUB_REPOSITORY,
                    'admin_page'  => PluginMetadata::ADMIN_PAGE_SLUG,
                    'capability'  => PluginMetadata::ADMIN_CAPABILITY,
                ]
            )
        );

        self::$bootstrap
            ->add_module( new AdminPage() )
            ->add_module( new UpdateCenter() )
            ->add_module(
                new CoreTabModule(
                    new CoreTabConfig(
                        [
                            'tabs_filter'   => PluginMetadata::HOOK_PREFIX . '_dashboard_tabs',
                            'render_filter' => PluginMetadata::HOOK_PREFIX . '_render_dashboard_tab',
                            'capability'    => PluginMetadata::ADMIN_CAPABILITY,
                            'core_root'     => $core_root,
                            'readme_path'   => $core_root . '/README.md',
                            'library_path'  => PluginMetadata::root_path() . '/HEXA_PLUGIN_CORE_LIBRARY.md',
                        ]
                    )
                )
            )
            ->boot();
    }
}
