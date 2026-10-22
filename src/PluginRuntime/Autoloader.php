<?php

namespace SMP\ContributorNetwork\PluginRuntime;

final class Autoloader {
    private const PREFIX = 'SMP\\ContributorNetwork\\';

    public static function register( string $source_root ): void {
        spl_autoload_register(
            static function ( string $class ) use ( $source_root ): void {
                if ( ! str_starts_with( $class, self::PREFIX ) ) {
                    return;
                }

                $file = $source_root . '/' . str_replace( '\\', '/', substr( $class, strlen( self::PREFIX ) ) ) . '.php';
                if ( is_readable( $file ) ) {
                    require_once $file;
                }
            }
        );
    }
}
