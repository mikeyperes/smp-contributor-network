<?php

namespace SMP\ContributorNetwork\Council;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\Fields\Field;

/**
 * The council links in the Verified Profiles dashboard (through its
 * smp_verified_profiles_dashboard_links action): apply, and with payments on
 * "Pay council membership" at the member's own price through Stripe. A paid
 * checkout makes them a council member; a cancelled subscription ends it.
 */
final class CouncilCheckout implements ModuleInterface {
    public function register(): void {
        add_action( 'smp_verified_profiles_dashboard_links', [ $this, 'dashboard_links' ] );
        add_action( 'admin_post_smp_cn_council_pay', [ self::class, 'handle_pay' ] );
        add_action( 'admin_post_smp_cn_payments_save', [ self::class, 'handle_save' ] );
        add_action( 'hws_base_tools_stripe_event', [ self::class, 'handle_event' ] );
    }

    public function dashboard_links(): void {
        $apply = CouncilSettings::page_url( 'application' );
        $name  = CouncilSettings::program_name();
        if ( '' !== $apply ) {
            echo '<a href="' . esc_url( $apply ) . '" target="_blank">' . esc_html( 'Apply for ' . ( '' !== $name ? $name : 'the council' ) ) . '</a>';
        }
        $user = get_current_user_id();
        if ( self::price( $user ) > 0 && CouncilPayments::ready() && ! CouncilSettings::is_member( $user ) ) {
            echo ' <a class="button button-primary" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=smp_cn_council_pay' ), 'smp_cn_council_pay' ) ) . '">Pay council membership</a>';
        }
    }

    public static function price( int $user_id ): float {
        $price = $user_id > 0 ? Field::get( 'price_leadership_council', 'user_' . $user_id ) : '';

        return is_numeric( $price ) ? (float) $price : 0.0;
    }

    public static function handle_pay(): void {
        check_admin_referer( 'smp_cn_council_pay' );
        $user  = wp_get_current_user();
        $price = self::price( (int) $user->ID );
        if ( $price <= 0 || ! CouncilPayments::ready() ) {
            wp_die( 'Council payment is not available right now.' );
        }
        $s       = CouncilPayments::settings();
        $name    = CouncilSettings::program_name();
        $session = \HWS\BaseTools\Payments\Stripe::checkout( [
            'amount'      => (int) round( $price * 100 ),
            'currency'    => $s['currency'],
            'name'        => '' !== $name ? $name : 'Council membership',
            'mode'        => 'once' === $s['interval'] ? 'payment' : 'subscription',
            'interval'    => $s['interval'],
            'email'       => (string) $user->user_email,
            'success_url' => admin_url( '?smp_cn_paid=1' ),
            'cancel_url'  => admin_url(),
            'metadata'    => [ 'smp_cn_action' => 'council', 'user_id' => (string) $user->ID ],
        ] );
        if ( is_wp_error( $session ) || empty( $session['url'] ) ) {
            wp_die( 'Payment could not start: ' . esc_html( is_wp_error( $session ) ? $session->get_error_message() : 'no address returned' ) );
        }
        wp_redirect( (string) $session['url'] );
        exit;
    }

    public static function handle_save(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Not allowed.' );
        }
        check_admin_referer( 'smp_cn_payments_save' );
        CouncilPayments::save( wp_unslash( $_POST ) );
        wp_safe_redirect( (string) wp_get_referer() ?: admin_url() );
        exit;
    }

    /** @param array<string,mixed> $event */
    public static function handle_event( array $event ): void {
        $object = (array) ( $event['data']['object'] ?? [] );
        $meta   = (array) ( $object['metadata'] ?? [] );
        if ( 'council' !== ( $meta['smp_cn_action'] ?? '' ) ) {
            return;
        }
        $user_id = (int) ( $meta['user_id'] ?? 0 );
        if ( $user_id <= 0 ) {
            return;
        }
        if ( 'checkout.session.completed' === ( $event['type'] ?? '' ) ) {
            Field::update( 'is_council_member', true, 'user_' . $user_id );
        } elseif ( 'customer.subscription.deleted' === ( $event['type'] ?? '' ) ) {
            Field::update( 'is_council_member', false, 'user_' . $user_id );
        }
    }
}
