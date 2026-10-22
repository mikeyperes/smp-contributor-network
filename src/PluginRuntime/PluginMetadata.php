<?php

namespace SMP\ContributorNetwork\PluginRuntime;

final class PluginMetadata {
    public const NAME = 'SMP Contributor Network';
    public const SLUG = 'smp-contributor-network';
    public const HOOK_PREFIX = 'smp_contributor_network';
    public const ADMIN_PAGE_SLUG = 'smp-contributor-network';
    public const ADMIN_CAPABILITY = 'manage_options';
    public const MAIN_FILE = 'smp-contributor-network.php';
    public const GITHUB_REPOSITORY = 'mikeyperes/smp-contributor-network';
    public const GITHUB_BRANCH = 'main';
    public const VERSION = '1.0.0';

    public static function root_path(): string {
        return dirname( __DIR__, 2 );
    }

    public static function plugin_file(): string {
        return self::root_path() . '/' . self::MAIN_FILE;
    }

    public static function core_root(): string {
        return self::root_path() . '/lib/hexa-wordpress-plugin-core';
    }

    public static function nonce_action(): string {
        return self::HOOK_PREFIX . '_admin';
    }
}
