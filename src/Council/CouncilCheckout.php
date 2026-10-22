<?php

namespace SMP\ContributorNetwork\Council;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\Fields\Field;

/**
 * The member's own council price at checkout, and the council apply link in the
 * Verified Profiles dashboard (through its smp_verified_profiles_dashboard_links action).
 */
final class CouncilCheckout implements ModuleInterface {
    public function register(): void {
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'adjust_price' ], 10, 1 );
        add_action( 'smp_verified_profiles_dashboard_links', [ $this, 'dashboard_links' ] );
    }

    /** @param object $cart */
    public function adjust_price( $cart ): void {
        $product = CouncilSettings::product_id();
        $user    = wp_get_current_user();
        if ( ! $product || ! $user->exists() || ! is_object( $cart ) || ! method_exists( $cart, 'get_cart' ) ) {
            return;
        }

        $price = Field::get( 'price_leadership_council', 'user_' . $user->ID );
        if ( empty( $price ) || ! is_numeric( $price ) ) {
            return;
        }

        foreach ( $cart->get_cart() as $item ) {
            if ( (int) ( $item['product_id'] ?? 0 ) === $product && isset( $item['data'] ) ) {
                $item['data']->set_price( $price );
            }
        }
    }

    public function dashboard_links(): void {
        $apply = CouncilSettings::page_url( 'application' );
        if ( '' === $apply ) {
            return;
        }
        $name = CouncilSettings::program_name();
        echo '<a href="' . esc_url( $apply ) . '" target="_blank">' . esc_html( 'Apply for ' . ( '' !== $name ? $name : 'the council' ) ) . '</a>';
    }
}
