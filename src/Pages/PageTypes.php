<?php

namespace SMP\ContributorNetwork\Pages;

use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * The publication page types this plugin owns. Templates are generic for
 * every publication: names and addresses come from shortcodes, never typed in.
 */
final class PageTypes {
    public const OPTION = PluginMetadata::HOOK_PREFIX . '_pages';

    /** @return array<string,array{label:string,title:string,slugs:array<int,string>,description:string}> */
    public static function all(): array {
        return [
            'become_contributor' => [
                'label'       => 'Become a Contributor',
                'title'       => 'Become a Contributor',
                'slugs'       => [ 'become-a-contributor', 'write-for-us' ],
                'description' => 'How to pitch and write for the publication.',
            ],
            'contributor_login' => [
                'label'       => 'Contributor Login',
                'title'       => 'Contributor Login',
                'slugs'       => [ 'contributor-login' ],
                'description' => 'Where contributors sign in: the site\'s own Hexa Admin sign-in page. A page is used only when one is linked here; none is created.',
                'sign_in'     => true,
            ],
        ];
    }

    /**
     * Where contributors sign in: the linked Contributor Login page when there is one, else the
     * site's own sign-in page (Hexa Admin, HWS Base Tools' sign-in address, through wp_login_url()).
     */
    public static function sign_in_url(): string {
        $page = self::page_id( 'contributor_login' );
        return $page ? (string) get_permalink( $page ) : wp_login_url();
    }

    /** A page type that is a sign-in address, not a page this plugin creates. */
    public static function is_sign_in( string $key ): bool {
        return ! empty( self::all()[ $key ]['sign_in'] );
    }

    public static function exists( string $key ): bool {
        return isset( self::all()[ $key ] );
    }

    /** The linked page: the saved choice, else the first page found at a usual address. */
    public static function page_id( string $key ): int {
        if ( ! self::exists( $key ) ) {
            return 0;
        }
        $saved = absint( ( (array) get_option( self::OPTION, [] ) )[ $key ] ?? 0 );
        if ( $saved && 'page' === get_post_type( $saved ) && 'trash' !== get_post_status( $saved ) ) {
            return $saved;
        }
        foreach ( self::all()[ $key ]['slugs'] as $slug ) {
            $page = get_page_by_path( $slug );
            if ( $page instanceof \WP_Post && 'trash' !== $page->post_status ) {
                return (int) $page->ID;
            }
        }
        return 0;
    }

    public static function link( string $key, int $page_id ): void {
        $saved         = (array) get_option( self::OPTION, [] );
        $saved[ $key ] = $page_id;
        update_option( self::OPTION, $saved, false );
    }

    public static function template( string $key ): string {
        $file = __DIR__ . '/templates/' . $key . '.html';
        return self::exists( $key ) && is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) . "\n" : '';
    }
}
