<?php

namespace SMP\ContributorNetwork\Council;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\Fields\Field;

/**
 * [contributor_network field="..."] and [display_profile_council_banner],
 * moved from SMP Verified Profiles with the same tags and output.
 */
final class CouncilShortcodes implements ModuleInterface {
    private static bool $banner_styled = false;

    public function register(): void {
        add_action( 'init', [ $this, 'add_shortcodes' ], 20 );
    }

    public function add_shortcodes(): void {
        add_shortcode( 'contributor_network', [ self::class, 'field' ] );
        add_shortcode( 'display_profile_council_banner', [ self::class, 'banner' ] );
    }

    /**
     * field="program_name|email|logo|member_banner_text|council_product_id|loop_items.<x>|pages.<x>|council_pages.<x>" size="thumbnail"
     *
     * @param array<string,string>|string $atts
     */
    public static function field( $atts ): string {
        $atts  = shortcode_atts( [ 'field' => '', 'size' => 'thumbnail' ], $atts, 'contributor_network' );
        $field = sanitize_text_field( $atts['field'] );
        $size  = sanitize_key( $atts['size'] );
        $group = CouncilSettings::group();
        if ( '' === $field || [] === $group ) {
            return '';
        }

        $parts = explode( '.', $field, 2 );
        if ( 1 === count( $parts ) ) {
            $key = $parts[0];
            if ( empty( $group[ $key ] ) ) {
                return '';
            }
            if ( 'logo' === $key ) {
                return (string) ( wp_get_attachment_image_url( (int) $group['logo'], $size ) ?: '' );
            }
            return esc_html( (string) $group[ $key ] );
        }

        [ $parent, $child ] = $parts;
        if ( isset( $group[ $parent ][ $child ] ) && '' !== $group[ $parent ][ $child ] ) {
            $value = $group[ $parent ][ $child ];
        } elseif ( 'pages' === $parent && isset( $group[ $parent ][ 'page_' . $child ] ) && '' !== $group[ $parent ][ 'page_' . $child ] ) {
            $value = $group[ $parent ][ 'page_' . $child ];
        } else {
            return '';
        }

        if ( in_array( $parent, [ 'pages', 'loop_items', 'council_pages' ], true ) ) {
            return (string) ( is_object( $value ) && isset( $value->ID ) ? $value->ID : (int) $value );
        }

        return esc_html( (string) $value );
    }

    /** The member banner on a profile whose linked user is a council member. */
    public static function banner(): string {
        $user_id = absint( Field::get( 'contributor_profile' ) );
        if ( ! CouncilSettings::is_member( $user_id ) ) {
            return '';
        }

        $style = '';
        if ( ! self::$banner_styled ) {
            self::$banner_styled = true;
            $style = '<style>.display_profile_council_banner i:before{content:"\f058";font-family:"Font Awesome 5 Free";font-weight:900;color:red;font-size:19px;margin-right:10px;display:block;}</style>';
        }

        return $style . '<span class="display_profile_council_banner"><i class="fas"></i><span>' . esc_html( CouncilSettings::banner_text() ) . '</span></span>';
    }
}
