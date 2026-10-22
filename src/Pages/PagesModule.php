<?php

namespace SMP\ContributorNetwork\Pages;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\WpAdminAjax\AjaxActionRegistry;
use Hexa\PluginCore\WpAdminAjax\AjaxRequest;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * [smp_contributor_network_page_template type="become_contributor"] prints a
 * page type's template; the Pages tab creates the page with it.
 */
final class PagesModule implements ModuleInterface {
    public const CREATE_ACTION = PluginMetadata::HOOK_PREFIX . '_create_page';

    public function register(): void {
        add_action( 'init', static fn() => add_shortcode( 'smp_contributor_network_page_template', [ self::class, 'render' ] ), 20 );

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
