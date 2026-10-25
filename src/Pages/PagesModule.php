<?php

namespace SMP\ContributorNetwork\Pages;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\WpAdminAjax\AjaxActionRegistry;
use Hexa\PluginCore\WpAdminAjax\AjaxRequest;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * [smp_contributor_network_page_template type="become_contributor"] prints a
 * page type's template; the Pages tab creates the page with it.
 * [smp_contributor_login] is the sign-in form (or, when signed in, links to
 * write and to the dashboard). wp smp-contributor-pages ensure publishes every
 * page that is missing.
 */
final class PagesModule implements ModuleInterface {
    public const CREATE_ACTION = PluginMetadata::HOOK_PREFIX . '_create_page';

    public function register(): void {
        add_action( 'init', static fn() => add_shortcode( 'smp_contributor_network_page_template', [ self::class, 'render' ] ), 20 );
        add_action( 'init', static fn() => add_shortcode( 'smp_contributor_login', [ self::class, 'login_form' ] ), 20 );
        add_action( 'init', static fn() => add_shortcode( 'smp_contributor_sign_in_link', [ self::class, 'sign_in_link' ] ), 20 );
        // Other plugins' page links ([hws_page_link], [hws_if page]) find this plugin's pages through Hexa WP Core.
        add_filter( 'hexa_site_structure_page_id', static fn( $id, string $key ): int => (int) $id > 0 ? (int) $id : PageTypes::page_id( $key ), 20, 2 );
        if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( '\WP_CLI' ) ) {
            \WP_CLI::add_command( 'smp-contributor-pages ensure', static function (): void {
                foreach ( array_keys( PageTypes::all() ) as $key ) {
                    $id = self::ensure_page( $key );
                    \WP_CLI::log( $key . ': page ' . $id . ' ' . (string) get_permalink( $id ) );
                }
            } );
        }

        AjaxActionRegistry::create(
            [
                'capability'   => PluginMetadata::ADMIN_CAPABILITY,
                'nonce_action' => PluginMetadata::nonce_action(),
                'verify_nonce' => true,
            ]
        )->register(
            [
                self::CREATE_ACTION => [ 'callback' => [ $this, 'ajax_create' ] ],
            ]
        );
    }

    /** @param array<string,string>|string $atts */
    public static function render( $atts ): string {
        $atts     = shortcode_atts( [ 'type' => '' ], $atts, 'smp_contributor_network_page_template' );
        $template = PageTypes::template( sanitize_key( $atts['type'] ) );
        if ( '' === $template ) {
            return '';
        }
        $html = do_shortcode( shortcode_unautop( wpautop( $template ) ) );
        // A paragraph left empty by a missing fact disappears with it.
        return (string) preg_replace( '#<p>(?:\s|&nbsp;|<br\s*/?>)*</p>\s*#i', '', $html );
    }

    /** [smp_contributor_sign_in_link text="Sign in"]: a link to where contributors sign in (PageTypes::sign_in_url()). */
    public static function sign_in_link( $atts ): string {
        $atts = shortcode_atts( [ 'text' => 'Sign in' ], (array) $atts, 'smp_contributor_sign_in_link' );
        return '<a href="' . esc_url( PageTypes::sign_in_url() ) . '">' . esc_html( (string) $atts['text'] ) . '</a>';
    }

    /** [smp_contributor_login]: WordPress's sign-in form, back to this page; when signed in, links to write and to the dashboard. */
    public static function login_form(): string {
        if ( is_user_logged_in() ) {
            return '<p class="smpcn-login__signed-in">You are signed in. <a href="' . esc_url( admin_url( 'post-new.php' ) ) . '">Write a new article</a> or <a href="' . esc_url( admin_url() ) . '">go to your dashboard</a>.</p>';
        }
        return '<div class="smpcn-login">' . wp_login_form( [ 'echo' => false, 'redirect' => admin_url() ] ) . '<p class="smpcn-login__lost"><a href="' . esc_url( wp_lostpassword_url() ) . '">Forgot your password?</a></p></div>';
    }

    /** The page for a page type, published from its template when missing. */
    public static function ensure_page( string $key ): int {
        $existing = PageTypes::page_id( $key );
        // Contributor Login is the site's Hexa Admin sign-in page: linked if a page exists, never created.
        if ( $existing || ! PageTypes::exists( $key ) || PageTypes::is_sign_in( $key ) ) {
            return $existing;
        }
        $type = PageTypes::all()[ $key ];
        $id   = wp_insert_post( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $type['title'], 'post_name' => $type['slugs'][0], 'post_content' => '[smp_contributor_network_page_template type="' . $key . '"]' ], true );
        if ( is_wp_error( $id ) ) {
            return 0;
        }
        PageTypes::link( $key, (int) $id );
        return (int) $id;
    }

    /** @return array<string,mixed> */
    public function ajax_create( AjaxRequest $request ): array {
        $key = $request->key( 'type', '' );
        if ( ! PageTypes::exists( $key ) ) {
            throw new \InvalidArgumentException( 'Unknown page type.' );
        }
        $existing = PageTypes::page_id( $key );
        if ( $existing ) {
            return [ 'page_id' => $existing, 'url' => get_permalink( $existing ), 'created' => false ];
        }
        if ( PageTypes::is_sign_in( $key ) ) {
            throw new \InvalidArgumentException( 'Contributors sign in on the site\'s Hexa Admin sign-in page; no page is created.' );
        }
        $type = PageTypes::all()[ $key ];
        $id   = wp_insert_post(
            [
                'post_type'    => 'page',
                'post_status'  => 'draft',
                'post_title'   => $type['title'],
                'post_name'    => $type['slugs'][0],
                'post_content' => '[smp_contributor_network_page_template type="' . $key . '"]',
            ],
            true
        );
        if ( is_wp_error( $id ) ) {
            throw new \RuntimeException( $id->get_error_message() );
        }
        PageTypes::link( $key, (int) $id );
        return [ 'page_id' => (int) $id, 'url' => get_edit_post_link( (int) $id, 'raw' ), 'created' => true ];
    }
}
