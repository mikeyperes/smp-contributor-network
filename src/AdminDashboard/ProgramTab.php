<?php

namespace SMP\ContributorNetwork\AdminDashboard;

use Hexa\PluginCore\Fields\Form;
use SMP\ContributorNetwork\Council\CouncilFields;
use SMP\ContributorNetwork\Council\CouncilSettings;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/** Program settings: name, email, logo, banner text, council product and pages. */
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
        echo '</div>';
    }

    private static function banner_sample(): string {
        return '<span class="display_profile_council_banner"><i class="fas"></i><span>' . esc_html( CouncilSettings::banner_text() ) . '</span></span>';
    }
}
