<?php

namespace SMP\ContributorNetwork\AdminDashboard;

use SMP\ContributorNetwork\Council\CouncilSettings;
use SMP\ContributorNetwork\Pages\PagesModule;
use SMP\ContributorNetwork\Pages\PageTypes;
use SMP\ContributorNetwork\PluginRuntime\PluginMetadata;

/** Pages this plugin owns: Become a Contributor and the three council pages. */
final class PagesTab {
    public static function render(): void {
        echo '<div class="hpc-ui">';
        echo '<h2>Pages</h2>';

        foreach ( PageTypes::all() as $key => $type ) {
            $id = PageTypes::page_id( $key );
            echo '<h3>' . esc_html( $type['label'] ) . '</h3>';
            echo '<p>' . esc_html( $type['description'] ) . '</p>';
            echo '<ul>';
            if ( $id ) {
                echo '<li>Page: <a href="' . esc_url( (string) get_permalink( $id ) ) . '" target="_blank" rel="noopener">' . esc_html( (string) get_permalink( $id ) ) . '</a> (' . esc_html( (string) get_post_status( $id ) ) . ')</li>';
            } elseif ( PageTypes::is_sign_in( $key ) ) {
                echo '<li>Sign-in: the site\'s Hexa Admin sign-in page, <a href="' . esc_url( PageTypes::sign_in_url() ) . '" target="_blank" rel="noopener">' . esc_html( PageTypes::sign_in_url() ) . '</a>. No page is needed.</li>';
            } else {
                echo '<li>Page: none yet. <button type="button" class="button smp-cn-create-page" data-type="' . esc_attr( $key ) . '">Create page</button></li>';
            }
            echo '<li>Template shortcode: <code>[smp_contributor_network_page_template type="' . esc_html( $key ) . '"]</code></li>';
            echo '</ul>';
            echo '<details><summary>Template</summary><pre style="white-space:pre-wrap">' . esc_html( PageTypes::template( $key ) ) . '</pre></details>';
        }

        echo '<h3>Council pages</h3>';
        echo '<p>Chosen on the Program tab.</p><ul>';
        foreach ( [ 'landing' => 'Council page', 'application' => 'Application page', 'badges' => 'Badges page' ] as $page => $label ) {
            $url = CouncilSettings::page_url( $page );
            echo '<li>' . esc_html( $label ) . ': ' . ( '' !== $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $url ) . '</a>' : 'not chosen' ) . '</li>';
        }
        echo '</ul></div>';
        ?>
        <script>
        document.querySelectorAll('.smp-cn-create-page').forEach(function (button) {
            button.addEventListener('click', function () {
                button.disabled = true;
                var body = new URLSearchParams({ action: <?php echo wp_json_encode( PagesModule::CREATE_ACTION ); ?>, nonce: <?php echo wp_json_encode( wp_create_nonce( PluginMetadata::nonce_action() ) ); ?>, type: button.dataset.type });
                fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', body: body }).then(function (r) { return r.json(); }).then(function (r) {
                    button.insertAdjacentText('afterend', r.success ? ' Created as a draft: ' + r.data.url : ' Failed: ' + (r.data && r.data.message ? r.data.message : 'unknown error'));
                });
            });
        });
        </script>
        <?php
    }
}
