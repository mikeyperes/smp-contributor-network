<?php

namespace SMP\ContributorNetwork\AdminDashboard;

use Hexa\PluginCore\Fields\Form;
use SMP\ContributorNetwork\Council\CouncilFields;
use SMP\ContributorNetwork\Council\CouncilSettings;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/** Program settings: name, email, logo, banner text, pages and council payments. */
final class ProgramTab {
    public static function render(): void {
        $return = admin_url( 'admin.php?page=' . PluginMetadata::ADMIN_PAGE_SLUG . '&tab=program&updated=1' );
        echo '<div class="hpc-ui">';
        echo '<h2>Program</h2>';
        echo '<p>The contributor network program for this site. Every name, address, page and product the council features use comes from here.</p>';
        if ( isset( $_GET['updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            echo '<div class="notice notice-success inline"><p>Saved.</p></div>';
        }

        echo '<h3>Member banner preview</h3>';
        echo '<p>' . self::banner_sample() . '</p>';

        Form::render(
            [
                'post_id'      => 'option',
                'field_groups' => [ CouncilFields::PROGRAM_GROUP, CouncilFields::OLDER_GROUP ],
                'return'       => $return,
                'submit_value' => 'Save program settings',
            ]
        );
        echo self::payments_form();
        echo '</div>';
    }

    private static function payments_form(): string {
        $s     = \SMP\ContributorNetwork\Council\CouncilPayments::settings();
        $ok    = \SMP\ContributorNetwork\Council\CouncilPayments::stripe_available();
        $ready = $ok && \HWS\BaseTools\Payments\Stripe::ready();
        $opt   = static fn( string $v, string $label ): string => '<option value="' . $v . '"' . selected( $s['interval'], $v, false ) . '>' . $label . '</option>';

        return '<h3>Council payments</h3>'
            . '<p>Members pay their own "Price Leadership Council" (on their user profile) through the one Stripe connection in HWS Base Tools. Off by default. ' . ( ! $ok ? '<strong>HWS Base Tools 13.46 or newer is needed.</strong>' : ( $ready ? 'Stripe is connected.' : '<strong>Stripe is off or has no key yet.</strong>' ) . ' <a href="' . esc_url( admin_url( 'options-general.php?page=hws-core-tools&tab=payments' ) ) . '">Open Payments in HWS Base Tools</a>' ) . '</p>'
            . '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">' . wp_nonce_field( 'smp_cn_payments_save', '_wpnonce', true, false )
            . '<input type="hidden" name="action" value="smp_cn_payments_save">'
            . '<p><label><input type="checkbox" name="enabled" value="1"' . checked( $s['enabled'], true, false ) . '> Take council payments through Stripe</label></p>'
            . '<p><label>Members pay <select name="interval">' . $opt( 'once', 'once' ) . $opt( 'month', 'every month' ) . $opt( 'year', 'every year' ) . '</select></label></p>'
            . '<p><label>Currency <input type="text" name="currency" value="' . esc_attr( $s['currency'] ) . '" size="4"></label></p>'
            . '<p><button type="submit" class="button button-primary">Save council payments</button></p></form>';
    }

    private static function banner_sample(): string {
        return '<span class="display_profile_council_banner"><i class="fas"></i><span>' . esc_html( CouncilSettings::banner_text() ) . '</span></span>';
    }
}
