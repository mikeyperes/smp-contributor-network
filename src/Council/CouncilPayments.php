<?php

namespace SMP\ContributorNetwork\Council;

/**
 * Council payments through HWS Base Tools' Stripe connection, off by default:
 * how often a member pays (once, monthly or yearly) and in what currency.
 * Each member pays their own "Price Leadership Council".
 */
final class CouncilPayments {
    public const OPTION = 'smp_cn_payments';

    /** @return array{enabled:bool,interval:string,currency:string} */
    public static function settings(): array {
        $s = get_option( self::OPTION, [] );
        $s = is_array( $s ) ? $s : [];

        return [
            'enabled'  => ! empty( $s['enabled'] ),
            'interval' => in_array( $s['interval'] ?? '', [ 'once', 'month', 'year' ], true ) ? (string) $s['interval'] : 'year',
            'currency' => preg_match( '/^[a-z]{3}$/', (string) ( $s['currency'] ?? '' ) ) ? (string) $s['currency'] : 'usd',
        ];
    }

    /** @param array<string,mixed> $raw */
    public static function save( array $raw ): void {
        update_option( self::OPTION, [
            'enabled'  => ! empty( $raw['enabled'] ),
            'interval' => (string) ( $raw['interval'] ?? 'year' ),
            'currency' => strtolower( substr( preg_replace( '/[^a-zA-Z]/', '', (string) ( $raw['currency'] ?? 'usd' ) ), 0, 3 ) ),
        ], false );
    }

    public static function stripe_available(): bool {
        return class_exists( '\\HWS\\BaseTools\\Payments\\Stripe' );
    }

    public static function ready(): bool {
        return self::settings()['enabled'] && self::stripe_available() && \HWS\BaseTools\Payments\Stripe::ready();
    }
}
