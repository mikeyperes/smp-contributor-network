<?php

namespace SMP\ContributorNetwork\AdminDashboard;

use Hexa\PluginCore\CoreContracts\ModuleInterface;
use Hexa\PluginCore\WpAdminAjax\AjaxActionRegistry;
use Hexa\PluginCore\WpAdminAjax\AjaxRequest;
use Hexa\PluginCore\WpAdminTabs\HostTabsRenderer;
use Hexa\PluginCore\WpAdminTabs\TabDefinition;
use Hexa\PluginCore\WpAdminTabs\TabRegistry;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/**
 * The plugin's one admin page. Tabs come from Core's registry; a tab's
 * renderer runs only when that tab is opened.
 */
final class AdminPage implements ModuleInterface {
    private const TAB_ACTION = PluginMetadata::HOOK_PREFIX . '_load_tab';

    /** @var array<string,array<int,string>> */
    private const GROUPS = [
        'Overview'        => [ 'overview' ],
        'Operations'      => [ 'update-center' ],
        'WordPress Admin' => [ 'hexa-core' ],
    ];

    public function register(): void {
        add_action( 'admin_menu', [ $this, 'add_menu' ] );

        AjaxActionRegistry::create(
            [
                'capability'   => PluginMetadata::ADMIN_CAPABILITY,
                'nonce_action' => PluginMetadata::nonce_action(),
            ]
        )->register(
            [
                self::TAB_ACTION => [ 'callback' => [ $this, 'ajax_tab' ] ],
            ]
        );
    }

    public function add_menu(): void {
        add_menu_page(
            PluginMetadata::NAME,
            'Contributor Network',
            PluginMetadata::ADMIN_CAPABILITY,
            PluginMetadata::ADMIN_PAGE_SLUG,
            [ $this, 'render_page' ],
            'dashicons-groups'
        );
    }

    /** @return array<string,string|TabDefinition> */
    public function tabs(): array {
        $registry = ( new TabRegistry() )
            ->add( new TabDefinition( 'overview', 'Overview', [ OverviewTab::class, 'render' ] ) )
            ->add( new TabDefinition( 'update-center', 'Update Center', [ \SMP\ContributorNetwork\Updates\UpdateCenter::class, 'render' ] ) );

        return (array) apply_filters( PluginMetadata::HOOK_PREFIX . '_dashboard_tabs', $registry->all() );
    }

    /** @return array<int,array{label:string,tabs:array<int,string>}> */
    private function groups( array $tabs ): array {
        $groups = [];
        foreach ( self::GROUPS as $label => $ids ) {
            $ids = array_values( array_filter( $ids, static fn( string $id ): bool => isset( $tabs[ $id ] ) ) );
            if ( $ids ) {
                $groups[] = [ 'label' => $label, 'tabs' => $ids ];
            }
        }

        return $groups;
    }

    public function render_page(): void {
        $tabs = $this->tabs();
        echo '<div class="wrap"><h1>' . esc_html( PluginMetadata::NAME ) . '</h1>';
        ( new HostTabsRenderer() )->render(
            [
                'tabs'            => $tabs,
                'active'          => isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                'page_url'        => admin_url( 'admin.php?page=' . PluginMetadata::ADMIN_PAGE_SLUG ),
                'ajax_action'     => self::TAB_ACTION,
                'nonce'           => wp_create_nonce( PluginMetadata::nonce_action() ),
                'root_id'         => PluginMetadata::HOOK_PREFIX . '_tabs',
                'layout'          => 'sidebar',
                'groups'          => $this->groups( $tabs ),
                'render_callback' => [ $this, 'render_tab' ],
            ]
        );
        echo '</div>';
    }

    public function render_tab( string $id ): void {
        if ( apply_filters( PluginMetadata::HOOK_PREFIX . '_render_dashboard_tab', false, $id ) ) {
            return;
        }

        $tab = $this->tabs()[ $id ] ?? null;
        if ( $tab instanceof TabDefinition && is_callable( $tab->renderer ) ) {
            call_user_func( $tab->renderer );
        }
    }

    /** @return array<string,string> */
    public function ajax_tab( AjaxRequest $request ): array {
        $tabs = $this->tabs();
        $id   = $request->key( 'tab', 'overview' );
        $id   = isset( $tabs[ $id ] ) ? $id : 'overview';

        ob_start();
        $this->render_tab( $id );

        $tab = $tabs[ $id ];

        return [
            'tab'   => $id,
            'label' => $tab instanceof TabDefinition ? $tab->label : (string) $tab,
            'html'  => (string) ob_get_clean(),
        ];
    }
}
