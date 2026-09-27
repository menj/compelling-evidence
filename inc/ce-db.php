<?php
/**
 * Compelling Evidence — custom table helpers.
 *
 * Creates the theme's own tables with a plain CREATE TABLE IF NOT EXISTS
 * query instead of dbDelta(). dbDelta() requires wp-admin/includes/upgrade.php,
 * which in turn requires wp-admin/includes/schema.php; if either core file is
 * missing or unreadable, the require is fatal. The theme's tables are created
 * once and never altered, so dbDelta() adds nothing here.
 *
 * @since 2.6.21
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Whether a table exists in the current database.
 *
 * @param string $table Full table name, including prefix.
 */
function ce_table_exists( string $table ): bool {
    global $wpdb;
    $like = $wpdb->esc_like( $table );
    return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $like ) ) === $table;
}

/**
 * Create a table from a CREATE TABLE IF NOT EXISTS statement.
 *
 * @param string $table Full table name, including prefix.
 * @param string $sql   CREATE TABLE IF NOT EXISTS statement.
 * @return bool True when the table exists afterwards.
 */
function ce_create_table( string $table, string $sql ): bool {
    global $wpdb;
    if ( ce_table_exists( $table ) ) {
        return true;
    }
    $wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL -- static DDL, no user input.
    return ce_table_exists( $table );
}
