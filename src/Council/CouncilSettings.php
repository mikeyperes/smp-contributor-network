<?php

namespace SMP\ContributorNetwork\Council;

use Hexa\PluginCore\Fields\Field;

/** Reads the program settings. Every site-specific value comes from here. */
final class CouncilSettings {
    /** @return array<string,mixed> */
    public static function group(): array {
        $group = Field::get( 'contributor_network', 'option' );
        return is_array( $group ) ? $group : [];
    }

    public static function program_name(): string {
        $name = trim( (string) ( self::group()['program_name'] ?? '' ) );
        return '' !== $name ? $name : trim( (string) Field::get( 'contributor_network_program_name', 'option' ) );
    }

    public static function banner_text(): string {
        $text = trim( (string) ( self::group()['member_banner_text'] ?? '' ) );
        if ( '' !== $text ) {
            return $text;
        }
        $name = self::program_name();
        return '' !== $name ? $name . ' Member' : 'Council Member';
    }

    public static function product_id(): int {
        return absint( self::group()['council_product_id'] ?? 0 );
    }

    /** @param string $page landing|application|badges */
    public static function page_id( string $page ): int {
        $value = self::group()['council_pages'][ $page ] ?? 0;
        return is_object( $value ) && isset( $value->ID ) ? (int) $value->ID : absint( $value );
    }

    public static function page_url( string $page ): string {
        $id = self::page_id( $page );
        return $id ? (string) get_permalink( $id ) : '';
    }

    public static function is_member( int $user_id ): bool {
        return $user_id > 0 && (bool) Field::get( 'is_council_member', 'user_' . $user_id );
    }
}
