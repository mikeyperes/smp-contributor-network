<?php

namespace SMP\ContributorNetwork\Council;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\Fields\FieldGroups;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * The program settings and the two council user fields, moved from
 * SMP Verified Profiles. Field keys and names are unchanged, so values
 * already stored (options_contributor_network_*, user meta) keep working.
 */
final class CouncilFields implements ModuleInterface {
    public const PROGRAM_GROUP = 'group_smp_contributor_network_program';
    public const OLDER_GROUP   = 'group_smp_contributor_network_program_copy';
    public const USER_GROUP    = 'group_smp_contributor_network_user';

    public function register(): void {
        FieldGroups::ready( [ $this, 'add_groups' ] );
    }

    public function add_groups(): void {
        $on_tab = [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => PluginMetadata::ADMIN_PAGE_SLUG ] ] ];

        FieldGroups::add(
            [
                'key'      => self::PROGRAM_GROUP,
                'title'    => 'Contributor Network Program',
                'location' => $on_tab,
                'fields'   => [
                    [
                        'key'        => 'field_6850930552d8c',
                        'label'      => 'Program',
                        'name'       => 'contributor_network',
                        'type'       => 'group',
                        'layout'     => 'block',
                        'sub_fields' => [
                            [ 'key' => 'field_6850932e52d8d', 'label' => 'Program name', 'name' => 'program_name', 'type' => 'text', 'instructions' => 'Shown to readers, for example in the member banner.' ],
                            [ 'key' => 'field_6850935b52d8e', 'label' => 'Email', 'name' => 'email', 'type' => 'text' ],
                            [ 'key' => 'field_6850938a52d8f', 'label' => 'Logo', 'name' => 'logo', 'type' => 'image', 'return_format' => 'id' ],
                            [ 'key' => 'field_smp_cn_member_banner_text', 'label' => 'Member banner text', 'name' => 'member_banner_text', 'type' => 'text', 'instructions' => 'Leave empty to show the program name followed by "Member".' ],
                            [ 'key' => 'field_smp_cn_council_product_id', 'label' => 'Council product', 'name' => 'council_product_id', 'type' => 'number', 'instructions' => 'The WooCommerce product a member buys. Its price is replaced by the member\'s own council price when one is set.' ],
                            [
                                'key'        => 'field_smp_cn_council_pages',
                                'label'      => 'Council pages',
                                'name'       => 'council_pages',
                                'type'       => 'group',
                                'layout'     => 'block',
                                'sub_fields' => [
                                    [ 'key' => 'field_smp_cn_council_page_landing', 'label' => 'Council page', 'name' => 'landing', 'type' => 'post_object', 'post_type' => [ 'page' ], 'return_format' => 'id', 'ui' => 1 ],
                                    [ 'key' => 'field_smp_cn_council_page_application', 'label' => 'Application page', 'name' => 'application', 'type' => 'post_object', 'post_type' => [ 'page' ], 'return_format' => 'id', 'ui' => 1 ],
                                    [ 'key' => 'field_smp_cn_council_page_badges', 'label' => 'Badges page', 'name' => 'badges', 'type' => 'post_object', 'post_type' => [ 'page' ], 'return_format' => 'id', 'ui' => 1 ],
                                ],
                            ],
                            [
                                'key'        => 'field_6850940000001',
                                'label'      => 'Loop items',
                                'name'       => 'loop_items',
                                'type'       => 'group',
                                'layout'     => 'block',
                                'sub_fields' => [
                                    [ 'key' => 'field_6850940100001', 'label' => 'Press releases', 'name' => 'display_single_profile_press_releases', 'type' => 'post_object', 'post_type' => [ 'elementor_library' ], 'return_format' => 'id', 'ui' => 1 ],
                                    [ 'key' => 'field_6850940100002', 'label' => 'Articles written by', 'name' => 'display_single_profile_article_written_by', 'type' => 'post_object', 'post_type' => [ 'elementor_library' ], 'return_format' => 'id', 'ui' => 1 ],
                                    [ 'key' => 'field_6850940100003', 'label' => 'Articles featured in', 'name' => 'display_single_profile_articles_featured_in', 'type' => 'post_object', 'post_type' => [ 'elementor_library' ], 'return_format' => 'id', 'ui' => 1 ],
                                    [ 'key' => 'field_6850940100004', 'label' => 'Mentioned in article', 'name' => 'display_single_post_mentioned_in_article', 'type' => 'post_object', 'post_type' => [ 'elementor_library' ], 'return_format' => 'id', 'ui' => 1 ],
                                ],
                            ],
                            [
                                'key'        => 'field_6850940000002',
                                'label'      => 'Profile pages',
                                'name'       => 'pages',
                                'type'       => 'group',
                                'layout'     => 'block',
                                'sub_fields' => [
                                    [ 'key' => 'field_6850940200001', 'label' => 'Badges', 'name' => 'verified_profiles_badges', 'type' => 'post_object', 'post_type' => [ 'page' ], 'ui' => 1 ],
                                    [ 'key' => 'field_6850940200002', 'label' => 'Claim a profile', 'name' => 'verified_profiles_claim', 'type' => 'post_object', 'post_type' => [ 'page' ], 'ui' => 1 ],
                                    [ 'key' => 'field_6850940200003', 'label' => 'Apply for a profile', 'name' => 'verified_profiles_apply', 'type' => 'post_object', 'post_type' => [ 'page' ], 'ui' => 1 ],
                                    [ 'key' => 'field_6850940200004', 'label' => 'Welcome', 'name' => 'verified_profiles_welcome', 'type' => 'post_object', 'post_type' => [ 'page' ], 'ui' => 1 ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        // The older second copy of the program fields; kept so pages reading it still work.
        FieldGroups::add(
            [
                'key'      => self::OLDER_GROUP,
                'title'    => 'Older program copy',
                'location' => $on_tab,
                'fields'   => [
                    [ 'key' => 'contributor_network_program_name', 'name' => 'contributor_network_program_name', 'label' => 'Program name (older copy)', 'type' => 'text' ],
                    [ 'key' => 'contributor_network_program_email', 'name' => 'contributor_network_program_email', 'label' => 'Program email (older copy)', 'type' => 'text' ],
                    [ 'key' => 'contributor_network_program_logo', 'name' => 'contributor_network_program_logo', 'label' => 'Program logo (older copy)', 'type' => 'file', 'return_format' => 'url', 'library' => 'all' ],
                ],
            ]
        );

        FieldGroups::add(
            [
                'key'      => self::USER_GROUP,
                'title'    => 'Contributor Network',
                'location' => [
                    [
                        [ 'param' => 'current_user_role', 'operator' => '==', 'value' => 'administrator' ],
                        [ 'param' => 'user_role', 'operator' => '==', 'value' => 'all' ],
                    ],
                ],
                'fields'   => [
                    [ 'key' => 'field_65a45a45cdee7', 'label' => 'Price Leadership Council', 'name' => 'price_leadership_council', 'type' => 'text', 'instructions' => 'This member\'s price for the council product. Empty keeps the product price.' ],
                    [ 'key' => 'field_65a8aeaa48c6f', 'label' => 'Is Council Member', 'name' => 'is_council_member', 'type' => 'true_false', 'default_value' => 0 ],
                ],
            ]
        );
    }
}
