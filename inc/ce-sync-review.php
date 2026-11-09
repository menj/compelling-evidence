<?php
/**
 * Bundled articles follow the bundle, safely.
 *
 * The content sync (ce-content-sync.php) writes the articles in inc/articles/ into the database. This file
 * decides what it may touch and lets the owner review what it may not.
 *
 * Fingerprints. When the sync writes an article it records two fingerprints in post meta: _ce_seed_hash
 * (the text it wrote, as stored: text, title, excerpt and FAQ items) and _ce_seed_bfp (the fingerprint of
 * the bundled entry it wrote from). An article counts as edited when its stored text no longer matches
 * _ce_seed_hash, and also when no record exists: without proof that it is untouched, it is treated as edited.
 *
 * What a sync does with an article it created (marked _ce_seed_key):
 *
 *   current     the bundle has not changed since the last write, or equals the stored text: nothing to do
 *   update      untouched by the owner, bundle text changed: takes the new text
 *   newer       edited by the owner, bundle text changed: listed with a diff; the owner takes the bundled
 *               text or keeps their own
 *   trash       dropped from the bundle and untouched: moved to the Trash, never deleted
 *   removed     dropped from the bundle and edited: listed
 *
 * Supersedes. inc/data/supersedes.json (and the ce_supersedes filter) is { post type: { old slug: new
 * slug } }. While the new article is published and the old one is untouched, the old one answers with a 301
 * (at any address, not only under /articles/), is hidden from lists, search, feeds and sitemaps, carries
 * noindex, and is moved to the Trash. An old article the owner edited is listed instead. The map ships empty.
 *
 * Guards. Removals stop when a batch file fails its checksum, when the bundle lists more than 10 percent
 * fewer articles than at the last good sync, or when CE_DISABLE_CONTENT_SYNC is true. The constant also
 * switches off updates, the redirect and the hiding.
 *
 * Delivery. The sync runs when the theme version, a batch checksum, the supersedes map or the guard state
 * changes, so a corrected batch reaches a site without a version bump.
 *
 * @package CE_Theme
 * @since   2.18.22
 */

defined( 'ABSPATH' ) || exit;

/**
 * The kill switch.
 *
 * @return bool
 */
function ce_sync_disabled(): bool {
	return defined( 'CE_DISABLE_CONTENT_SYNC' ) && CE_DISABLE_CONTENT_SYNC;
}

/** The post types the bundle creates. */
function ce_sync_types(): array {
	return [ 'ce_article' ];
}

/**
 * Fingerprint of the texts the sync owns, from their values. The same layout is used for a stored post and
 * for a bundled entry, so the two compare.
 *
 * @param string $title   Title.
 * @param string $excerpt Excerpt.
 * @param string $content Content.
 * @param mixed  $faq     FAQ items: a list of q and a pairs, or '' for none.
 * @return string
 */
function ce_sync_fp_values( $title, $excerpt, $content, $faq ): string {
	return md5( (string) $content . "\0" . (string) $title . "\0" . (string) $excerpt . "\0" . (string) wp_json_encode( empty( $faq ) ? '' : $faq ) );
}

/**
 * Fingerprint of a bundled entry.
 *
 * @param array $article Loader output.
 * @return string
 */
function ce_sync_bundle_fp( array $article ): string {
	return ce_sync_fp_values( $article['title'] ?? '', $article['excerpt'] ?? '', $article['content'] ?? '', $article['faq'] ?? '' );
}

/**
 * What a sync does with one existing article. Pure.
 *
 * @param string $bundle_fp      Fingerprint of the bundled entry.
 * @param string $stored_fp      Fingerprint of the stored text now.
 * @param string $recorded_fp    Fingerprint recorded when the sync last wrote it, '' when none.
 * @param string $last_bundle_fp Fingerprint of the bundled entry at that write, '' when none.
 * @param string $ack            Bundle fingerprint the owner chose to keep their text against.
 * @return string current|update|newer|kept
 */
function ce_sync_decide( string $bundle_fp, string $stored_fp, string $recorded_fp, string $last_bundle_fp, string $ack ): string {
	if ( '' !== $last_bundle_fp && $last_bundle_fp === $bundle_fp ) {
		return 'current';
	}
	if ( $stored_fp === $bundle_fp ) {
		return 'current';
	}
	if ( '' !== $recorded_fp && $recorded_fp === $stored_fp ) {
		return 'update';
	}
	return $ack === $bundle_fp ? 'kept' : 'newer';
}

/**
 * What a sync does with an article the bundle no longer carries. Pure.
 *
 * @param string $stored_fp   Fingerprint of the stored text now.
 * @param string $recorded_fp Fingerprint recorded when the sync last wrote it, '' when none.
 * @param string $ack         The owner's earlier choice ('removed' keeps it quiet).
 * @return string trash|removed|kept
 */
function ce_sync_decide_removed( string $stored_fp, string $recorded_fp, string $ack ): string {
	if ( '' !== $recorded_fp && $recorded_fp === $stored_fp ) {
		return 'trash';
	}
	return 'removed' === $ack ? 'kept' : 'removed';
}

/**
 * Whether removals may run. Pure.
 *
 * @param bool $disabled    The kill switch.
 * @param bool $intact      Every batch matches its checksum and loaded.
 * @param int  $count_now   Bundled articles now.
 * @param int  $count_prior Bundled articles at the last good sync, 0 when none.
 * @return string '' when allowed, otherwise the reason.
 */
function ce_sync_guard( bool $disabled, bool $intact, int $count_now, int $count_prior ): string {
	if ( $disabled ) {
		return 'the kill switch CE_DISABLE_CONTENT_SYNC is on';
	}
	if ( ! $intact ) {
		return 'a batch file is missing or fails its checksum';
	}
	if ( $count_prior > 0 && $count_now * 10 < $count_prior * 9 ) {
		return 'the bundle lists more than 10 percent fewer articles than at the last good sync';
	}
	return '';
}

/**
 * The manifest, read cheaply.
 *
 * @return array
 */
function ce_sync_manifest(): array {
	$raw = @file_get_contents( get_stylesheet_directory() . '/inc/articles/manifest.json' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- a missing file is a guard reason.
	$m   = is_string( $raw ) ? json_decode( $raw, true ) : null;
	return is_array( $m ) ? $m : [];
}

/**
 * Whether every batch file matches the checksum in the manifest.
 *
 * @return bool
 */
function ce_sync_batches_intact(): bool {
	$m = ce_sync_manifest();
	if ( empty( $m['batches'] ) || ! is_array( $m['batches'] ) ) {
		return false;
	}
	foreach ( $m['batches'] as $b ) {
		$file = get_stylesheet_directory() . '/inc/articles/' . basename( (string) ( $b['file'] ?? '' ) );
		if ( ! is_readable( $file ) || empty( $b['checksum'] ) || hash_file( 'sha256', $file ) !== $b['checksum'] ) {
			return false;
		}
	}
	return true;
}

/**
 * The number of bundled articles the manifest declares.
 *
 * @return int
 */
function ce_sync_bundle_count(): int {
	$n = 0;
	foreach ( (array) ( ce_sync_manifest()['batches'] ?? [] ) as $b ) {
		$n += (int) ( $b['articles'] ?? 0 );
	}
	return $n;
}

/**
 * Whether removals may run now.
 *
 * @return string '' when allowed, otherwise the reason.
 */
function ce_sync_removal_block(): string {
	$why = ce_sync_guard( ce_sync_disabled(), ce_sync_batches_intact(), ce_sync_bundle_count(), (int) get_option( 'ce_sync_count', 0 ) );
	if ( '' === $why && get_option( 'ce_sync_loader_errors' ) ) {
		return 'the article loader reported errors at the last sync';
	}
	return $why;
}

/**
 * The supersedes map, cleaned: type => ( old slug => new slug ).
 *
 * @return array<string,array<string,string>>
 */
function ce_supersedes(): array {
	$raw = @file_get_contents( get_stylesheet_directory() . '/inc/data/supersedes.json' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- an absent file is an empty map.
	$raw = is_string( $raw ) ? json_decode( $raw, true ) : [];
	$raw = apply_filters( 'ce_supersedes', is_array( $raw ) ? $raw : [] );
	$map = [];
	foreach ( is_array( $raw ) ? $raw : [] as $type => $pairs ) {
		if ( ! in_array( $type, ce_sync_types(), true ) || ! is_array( $pairs ) ) {
			continue;
		}
		foreach ( $pairs as $old => $new ) {
			$old = sanitize_title( (string) $old );
			$new = sanitize_title( (string) $new );
			if ( '' !== $old && '' !== $new && $old !== $new ) {
				$map[ $type ][ $old ] = $new;
			}
		}
	}
	return $map;
}

/**
 * The post the sync created for a slug, or null. Articles the owner made are never returned.
 *
 * @param string $slug Slug.
 * @return WP_Post|null
 */
function ce_sync_post( string $slug ) {
	$posts = get_posts( [ 'name' => $slug, 'post_type' => 'ce_article', 'post_status' => 'any', 'numberposts' => 1 ] );
	$post  = $posts ? $posts[0] : null;
	return ( $post && get_post_meta( $post->ID, '_ce_seed_key', true ) ) ? $post : null;
}

/**
 * The state of a post against its own record.
 *
 * @param WP_Post $post Post.
 * @return array{stored:string,recorded:string,bfp:string,ack:string}
 */
function ce_sync_state( $post ): array {
	return [
		'stored'   => ce_sync_seed_hash( $post->ID ),
		'recorded' => (string) get_post_meta( $post->ID, '_ce_seed_hash', true ),
		'bfp'      => (string) get_post_meta( $post->ID, '_ce_seed_bfp', true ),
		'ack'      => (string) get_post_meta( $post->ID, '_ce_sync_ack', true ),
	];
}

/**
 * The bundle, keyed by slug: slug => article. Loader errors are returned in the second element.
 *
 * @return array{0:array<string,array>,1:bool} Articles and whether the loader reported errors.
 */
function ce_sync_bundle(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	require_once get_stylesheet_directory() . '/inc/class-ce-article-loader.php';
	$loader = new CE_Article_Loader();
	$list   = $loader->load_all_articles();
	$out    = [];
	foreach ( (array) $list as $a ) {
		if ( ! empty( $a['slug'] ) ) {
			$out[ $a['slug'] ] = $a;
		}
	}
	$cache = [ $out, (bool) $loader->has_errors() ];
	return $cache;
}

/**
 * Superseded pairs that qualify now: old post ID => new post ID. The new one is published, the old one was
 * made by the sync and has not been edited.
 *
 * @return array<int,int>
 */
function ce_superseded_pairs(): array {
	static $cache = null;
	static $busy  = false;
	if ( null !== $cache ) {
		return $cache;
	}
	if ( $busy ) {
		return []; // The queries below fire pre_get_posts, which asks the unlist module, which asks here.
	}
	// The redirect and the hiding stand behind the same guards as the removal they lead to: while a batch fails
	// its checksum or the count fell by more than 10 percent, the old article stays visible.
	$map = ( ce_sync_disabled() || '' !== ce_sync_removal_block() ) ? [] : ce_supersedes();
	if ( ! $map ) {
		$cache = [];
		return $cache;
	}
	$busy  = true;
	$cache = [];
	foreach ( $map as $pairs ) {
		foreach ( $pairs as $old => $new ) {
			$old_post = ce_sync_post( $old );
			$new_list = get_posts( [ 'name' => $new, 'post_type' => 'ce_article', 'post_status' => 'publish', 'numberposts' => 1 ] );
			if ( ! $old_post || ! $new_list || 'publish' !== $old_post->post_status || $old_post->ID === $new_list[0]->ID ) {
				continue;
			}
			$s = ce_sync_state( $old_post );
			if ( '' !== $s['recorded'] && $s['recorded'] === $s['stored'] ) {
				$cache[ (int) $old_post->ID ] = (int) $new_list[0]->ID;
			}
		}
	}
	$busy = false;
	return $cache;
}

/**
 * IDs of the superseded posts, for the unlist module.
 *
 * @return int[]
 */
function ce_superseded_ids(): array {
	return array_keys( ce_superseded_pairs() );
}

/**
 * The plan: one row per article that needs attention.
 *
 * @return array<int,array{key:string,slug:string,title:string,post_id:int,action:string}>
 */
function ce_sync_plan(): array {
	[ $bundle ] = ce_sync_bundle();
	$rows       = [];
	foreach ( $bundle as $slug => $article ) {
		$posts = get_posts( [ 'name' => $slug, 'post_type' => 'ce_article', 'post_status' => 'any', 'numberposts' => 1 ] );
		$post  = $posts ? $posts[0] : null;
		if ( ! $post || 'trash' === $post->post_status ) {
			continue; // Not created yet, or removed: the sync decides, never this plan.
		}
		$s    = ce_sync_state( $post );
		$mark = (bool) get_post_meta( $post->ID, '_ce_seed_key', true );
		if ( ! $mark ) {
			$s['recorded'] = ''; // Without a record, the article counts as edited.
		}
		$rows[] = [ 'key' => $slug, 'slug' => $slug, 'title' => $post->post_title, 'post_id' => (int) $post->ID, 'action' => ce_sync_decide( ce_sync_bundle_fp( $article ), $s['stored'], $s['recorded'], $mark ? $s['bfp'] : '', $s['ack'] ) ];
	}
	$marked = get_posts( [ 'post_type' => 'ce_article', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_ce_seed_key', 'meta_compare' => 'EXISTS' ] );
	foreach ( $marked as $id ) {
		$slug = (string) get_post_field( 'post_name', $id );
		if ( isset( $bundle[ $slug ] ) ) {
			continue;
		}
		$post = get_post( $id );
		$s    = ce_sync_state( $post );
		$rows[] = [ 'key' => $slug, 'slug' => $slug, 'title' => $post->post_title, 'post_id' => (int) $id, 'action' => ce_sync_decide_removed( $s['stored'], $s['recorded'], $s['ack'] ) ];
	}
	$pairs = ce_superseded_pairs();
	foreach ( ce_supersedes() as $map ) {
		foreach ( $map as $old => $new ) {
			$old_post = ce_sync_post( $old );
			$new_list = get_posts( [ 'name' => $new, 'post_type' => 'ce_article', 'post_status' => 'publish', 'numberposts' => 1 ] );
			if ( ! $old_post || ! $new_list || 'publish' !== $old_post->post_status ) {
				continue;
			}
			$ack    = (string) get_post_meta( $old_post->ID, '_ce_sync_ack', true );
			$rows[] = [ 'key' => $old, 'slug' => $old, 'title' => $old_post->post_title, 'post_id' => (int) $old_post->ID, 'action' => isset( $pairs[ $old_post->ID ] ) ? 'supersede' : ( 'superseded' === $ack ? 'kept' : 'superseded' ) ];
		}
	}
	return $rows;
}

/**
 * Append to the change log (the last 100 lines).
 *
 * @param string $key    Article slug.
 * @param string $action What happened.
 * @return void
 */
function ce_sync_log( string $key, string $action ): void {
	$log   = (array) get_option( 'ce_sync_log', [] );
	$log[] = [ 't' => time(), 'key' => $key, 'action' => $action ];
	update_option( 'ce_sync_log', array_slice( $log, -100 ), false );
}

/**
 * Write a bundled article into its post and record the fingerprints.
 *
 * @param WP_Post $post    Post.
 * @param array   $article Loader entry.
 * @return bool
 */
function ce_sync_write( $post, array $article ): bool {
	$scope = function_exists( 'ce_sec_seeding_scope' ) ? ce_sec_seeding_scope() : null; // Seeding run: exempt from the spam gate.
	$done  = wp_update_post( [ 'ID' => $post->ID, 'post_title' => $article['title'], 'post_excerpt' => $article['excerpt'], 'post_content' => $article['content'] ], true );
	unset( $scope );
	if ( is_wp_error( $done ) || ! $done ) {
		return false;
	}
	ce_sync_save_faq( $post->ID, $article ); // Saves the bundled FAQ, or clears one the bundle dropped.
	ce_sync_mark_seeded( $post->ID, $article['slug'], $article );
	return true;
}

/**
 * Run the review steps: take the bundled text for untouched articles, retire what the bundle dropped or
 * superseded when no guard stops it, and list everything that waits for the owner.
 *
 * The orphan pass inside ce_sync_article_content() does the trashing of dropped articles; this runs the
 * supersede step and the pending list, and is safe to call on its own.
 *
 * @return array{trashed:int,waiting:int,blocked:string}
 */
function ce_sync_review_apply(): array {
	$out = [ 'trashed' => 0, 'waiting' => 0, 'blocked' => '' ];
	if ( ce_sync_disabled() ) {
		$out['blocked'] = ce_sync_removal_block();
		return $out;
	}
	[ , $errors ] = ce_sync_bundle();
	if ( $errors ) {
		update_option( 'ce_sync_loader_errors', 1, false );
	} else {
		delete_option( 'ce_sync_loader_errors' );
	}
	$blocked = ce_sync_removal_block();
	$out['blocked'] = $blocked;
	foreach ( ce_sync_plan() as $row ) {
		if ( 'supersede' === $row['action'] && '' === $blocked ) {
			$post = get_post( $row['post_id'] );
			if ( $post && 'trash' !== $post->post_status && wp_trash_post( $post->ID ) ) {
				++$out['trashed'];
				ce_sync_log( $row['key'], 'moved to the Trash, replaced' );
			}
		}
	}
	$pending = [];
	foreach ( ce_sync_plan() as $row ) {
		if ( in_array( $row['action'], [ 'newer', 'removed', 'superseded', 'trash', 'supersede' ], true ) ) {
			if ( in_array( $row['action'], [ 'trash', 'supersede' ], true ) && '' === $blocked ) {
				continue;
			}
			$pending[ $row['key'] ] = $row;
		}
	}
	update_option( 'ce_sync_pending', $pending, false );
	$out['waiting'] = count( $pending );
	if ( '' === $blocked ) {
		update_option( 'ce_sync_count', ce_sync_bundle_count(), false );
	}
	return $out;
}

/**
 * Run the sync when the theme, a batch, the supersedes map or the guard state changed, and only for someone
 * who may manage options.
 *
 * @return void
 */
function ce_sync_maybe_run(): void {
	if ( ce_sync_disabled() || wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$sums = [];
	foreach ( (array) ( ce_sync_manifest()['batches'] ?? [] ) as $b ) {
		$sums[] = (string) ( $b['checksum'] ?? '' );
	}
	// The guard state is part of the fingerprint, so a sync that removals blocked runs again once the block clears.
	$fp = md5( wp_get_theme()->get( 'Version' ) . wp_json_encode( $sums ) . wp_json_encode( ce_supersedes() ) . ce_sync_removal_block() );
	if ( get_option( 'ce_sync_fp' ) === $fp ) {
		return;
	}
	ce_sync_article_content( true, true ); // Respect a running sync's lock: two requests must not both create articles.
	ce_sync_review_apply();
	update_option( 'ce_sync_fp', $fp, false );
}
add_action( 'admin_init', 'ce_sync_maybe_run', 30 );

/* -------------------------------------------------------------------------
 * The owner's choices, the notice and the card.
 * ---------------------------------------------------------------------- */

/** Where the review lives: Theme Options, Tools. */
function ce_sync_screen_url(): string {
	return add_query_arg( [ 'page' => 'ce-theme-options', 'tab' => 'tools' ], admin_url( 'themes.php' ) ) . '#ce-content-sync';
}

/** Take the bundled text for one article, retire it, or keep the owner's own. All are logged. */
function ce_sync_handle_item(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'compelling-evidence' ), 403 );
	}
	check_admin_referer( 'ce_sync_item' );
	if ( ce_sync_disabled() ) {
		wp_safe_redirect( ce_sync_screen_url() ); // The switch comes before any choice: nothing is written or trashed.
		exit;
	}
	$slug   = isset( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
	$choice = isset( $_POST['choice'] ) && 'take' === $_POST['choice'] ? 'take' : 'keep';
	[ $bundle ] = ce_sync_bundle();
	$posts  = '' !== $slug ? get_posts( [ 'name' => $slug, 'post_type' => 'ce_article', 'post_status' => 'any', 'numberposts' => 1 ] ) : [];
	$post   = $posts ? $posts[0] : null;
	if ( $post ) {
		$article = $bundle[ $slug ] ?? null;
		if ( 'take' === $choice && $article ) {
			ce_sync_write( $post, $article );
			ce_sync_log( $slug, 'took the bundled text' );
		} elseif ( 'take' === $choice && get_post_meta( $post->ID, '_ce_seed_key', true ) ) {
			wp_trash_post( $post->ID ); // Dropped or superseded: the bundled choice is the Trash, never a deletion.
			ce_sync_log( $slug, 'moved to the Trash by choice' );
		} elseif ( 'keep' === $choice ) {
			$superseded = false;
			foreach ( ce_supersedes() as $map ) {
				$superseded = $superseded || isset( $map[ $slug ] );
			}
			update_post_meta( $post->ID, '_ce_sync_ack', $article ? ce_sync_bundle_fp( $article ) : ( $superseded ? 'superseded' : 'removed' ) );
			ce_sync_log( $slug, 'kept the owner text' );
		}
		ce_sync_review_apply();
	}
	wp_safe_redirect( ce_sync_screen_url() );
	exit;
}
add_action( 'admin_post_ce_sync_item', 'ce_sync_handle_item' );

/** Run the sync now, with the same rules as the automatic one. */
function ce_sync_handle_now(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'compelling-evidence' ), 403 );
	}
	check_admin_referer( 'ce_sync_now' );
	if ( ce_sync_disabled() ) {
		wp_safe_redirect( ce_sync_screen_url() );
		exit;
	}
	ce_sync_article_content( true, true );
	ce_sync_review_apply();
	wp_safe_redirect( ce_sync_screen_url() );
	exit;
}
add_action( 'admin_post_ce_sync_now', 'ce_sync_handle_now' );

/** One grouped notice for everything that waits for the owner. */
function ce_sync_notice(): void {
	if ( ! current_user_can( 'manage_options' ) || ce_sync_disabled() ) {
		return;
	}
	$newer = [];
	$gone  = [];
	foreach ( (array) get_option( 'ce_sync_pending', [] ) as $row ) {
		if ( 'newer' === ( $row['action'] ?? '' ) ) {
			$newer[] = (string) $row['title'];
		} elseif ( in_array( $row['action'] ?? '', [ 'removed', 'superseded' ], true ) ) {
			$gone[] = (string) $row['title'];
		}
	}
	if ( ! $newer && ! $gone ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Bundled articles have changes for articles you edited. Nothing was overwritten or trashed.', 'compelling-evidence' ) . '</p>';
	if ( $newer ) {
		echo '<p>' . esc_html__( 'Newer version available:', 'compelling-evidence' ) . ' ' . esc_html( implode( '; ', $newer ) ) . '</p>';
	}
	if ( $gone ) {
		echo '<p>' . esc_html__( 'Removed from the bundle or replaced:', 'compelling-evidence' ) . ' ' . esc_html( implode( '; ', $gone ) ) . '</p>';
	}
	printf( '<p><a href="%s">%s</a></p></div>', esc_url( ce_sync_screen_url() ), esc_html__( 'Review them under Tools', 'compelling-evidence' ) );
}
add_action( 'admin_notices', 'ce_sync_notice' );

/**
 * The text of a field set as one block, for a diff.
 *
 * @param array $fields Fields.
 * @return string
 */
function ce_sync_blob( array $fields ): string {
	$out = '';
	foreach ( $fields as $name => $value ) {
		$out .= '[' . $name . "]\n" . ( is_array( $value ) ? wp_json_encode( $value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : (string) $value ) . "\n\n";
	}
	return $out;
}

/** The Tools card: a dry run, the owner's choices, a log. It is drawn outside every other form. */
function ce_render_content_sync_card(): void {
	$labels = [
		'update'     => __( 'Would update (you have not edited it)', 'compelling-evidence' ),
		'newer'      => __( 'Newer version available (you edited it, so it stays as it is)', 'compelling-evidence' ),
		'trash'      => __( 'Would move to the Trash (dropped from the bundle, you have not edited it)', 'compelling-evidence' ),
		'removed'    => __( 'Dropped from the bundle (you edited it, so it stays)', 'compelling-evidence' ),
		'supersede'  => __( 'Would be redirected, hidden and moved to the Trash: its replacement is live', 'compelling-evidence' ),
		'superseded' => __( 'Replaced, but you edited it or the theme has no record of its text, so it stays', 'compelling-evidence' ),
		'kept'       => __( 'Kept as it is', 'compelling-evidence' ),
	];
	echo '<div class="ce-theme-opts-card" id="ce-content-sync"><h2 class="ce-theme-opts-card__title">' . esc_html__( 'Bundled articles: review', 'compelling-evidence' ) . '</h2><div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">';
	echo '<p>' . esc_html__( 'What the next sync would do. Nothing here has been changed. An article you edited is never overwritten or trashed.', 'compelling-evidence' ) . '</p>';
	if ( ce_sync_disabled() ) {
		echo '<p>' . esc_html__( 'The content sync is switched off by CE_DISABLE_CONTENT_SYNC.', 'compelling-evidence' ) . '</p>';
	}
	$blocked = ce_sync_removal_block();
	if ( '' !== $blocked && ! ce_sync_disabled() ) {
		echo '<div class="notice notice-warning inline"><p>' . esc_html( sprintf( /* translators: %s: reason. */ __( 'Removals are stopped: %s.', 'compelling-evidence' ), $blocked ) ) . '</p></div>';
	}
	$rows = array_filter( ce_sync_plan(), static fn( $r ) => 'current' !== $r['action'] );
	if ( ! $rows ) {
		echo '<p>' . esc_html__( 'Every article the theme created matches the bundle.', 'compelling-evidence' ) . '</p>';
	} else {
		[ $bundle ] = ce_sync_bundle();
		echo '<table class="widefat striped"><thead><tr><th scope="col">' . esc_html__( 'Article', 'compelling-evidence' ) . '</th><th scope="col">' . esc_html__( 'The next sync', 'compelling-evidence' ) . '</th><th scope="col">' . esc_html__( 'Your choice', 'compelling-evidence' ) . '</th></tr></thead><tbody>';
		foreach ( $rows as $row ) {
			echo '<tr><td>' . esc_html( $row['title'] ) . '<br><code>' . esc_html( $row['key'] ) . '</code>';
			if ( ! get_post_meta( $row['post_id'], '_ce_seed_key', true ) ) {
				echo '<br><em>' . esc_html__( 'The theme has no record of creating this article. Taking the bundled text is your choice; nothing happens until you click.', 'compelling-evidence' ) . '</em>';
			}
			if ( 'newer' === $row['action'] && isset( $bundle[ $row['key'] ] ) ) {
				$post = get_post( $row['post_id'] );
				if ( $post && function_exists( 'wp_text_diff' ) ) {
					$faq  = get_post_meta( $post->ID, '_ce_faq_items', true );
					$mine = [ 'title' => $post->post_title, 'excerpt' => $post->post_excerpt, 'content' => $post->post_content, 'faq' => is_array( $faq ) ? $faq : [] ];
					$a    = $bundle[ $row['key'] ];
					$diff = wp_text_diff( ce_sync_blob( $mine ), ce_sync_blob( [ 'title' => $a['title'], 'excerpt' => $a['excerpt'], 'content' => $a['content'], 'faq' => $a['faq'] ?? [] ] ), [ 'title_left' => __( 'Yours', 'compelling-evidence' ), 'title_right' => __( 'The bundled text', 'compelling-evidence' ) ] );
					if ( $diff ) {
						echo '<details><summary>' . esc_html__( 'Show the differences', 'compelling-evidence' ) . '</summary>' . $diff . '</details>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by core's wp_text_diff(), which escapes the text.
					}
				}
			}
			echo '</td><td>' . esc_html( $labels[ $row['action'] ] ?? $row['action'] ) . '</td><td>';
			if ( ! ce_sync_disabled() && in_array( $row['action'], [ 'newer', 'removed', 'superseded', 'kept' ], true ) ) {
				$take = isset( $bundle[ $row['key'] ] ) ? __( 'Take the bundled text', 'compelling-evidence' ) : __( 'Move to the Trash', 'compelling-evidence' );
				foreach ( [ 'take' => $take, 'keep' => __( 'Keep mine', 'compelling-evidence' ) ] as $choice => $text ) {
					printf(
						'<form method="post" action="%1$s" style="display:inline"><input type="hidden" name="action" value="ce_sync_item"><input type="hidden" name="item" value="%2$s"><input type="hidden" name="choice" value="%3$s">%4$s<button type="submit" class="button button-small">%5$s</button></form> ',
						esc_url( admin_url( 'admin-post.php' ) ),
						esc_attr( $row['key'] ),
						esc_attr( $choice ),
						wp_nonce_field( 'ce_sync_item', '_wpnonce', true, false ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core's nonce field.
						esc_html( $text )
					);
				}
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
	}
	if ( ! ce_sync_disabled() ) {
		printf(
			'<form method="post" action="%s" style="margin-top:12px"><input type="hidden" name="action" value="ce_sync_now">%s<button type="submit" class="button">%s</button></form>',
			esc_url( admin_url( 'admin-post.php' ) ),
			wp_nonce_field( 'ce_sync_now', '_wpnonce', true, false ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core's nonce field.
			esc_html__( 'Run the review sync now', 'compelling-evidence' )
		);
	}
	$log = array_reverse( (array) get_option( 'ce_sync_log', [] ) );
	echo '<h3>' . esc_html__( 'What changed', 'compelling-evidence' ) . '</h3>';
	if ( ! $log ) {
		echo '<p>' . esc_html__( 'Nothing yet.', 'compelling-evidence' ) . '</p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th scope="col">' . esc_html__( 'When', 'compelling-evidence' ) . '</th><th scope="col">' . esc_html__( 'Article', 'compelling-evidence' ) . '</th><th scope="col">' . esc_html__( 'What happened', 'compelling-evidence' ) . '</th></tr></thead><tbody>';
		foreach ( $log as $line ) {
			echo '<tr><td>' . esc_html( wp_date( 'Y-m-d H:i', (int) $line['t'] ) ) . '</td><td>' . esc_html( (string) $line['key'] ) . '</td><td>' . esc_html( (string) $line['action'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</div></div>';
}

/* -------------------------------------------------------------------------
 * Superseded articles: the 301 and the hiding.
 * ---------------------------------------------------------------------- */

/** The 301 from a superseded article to its replacement, at whatever address it answers. */
function ce_superseded_redirect(): void {
	if ( ce_sync_disabled() || is_admin() || is_preview() || is_feed() ) {
		return;
	}
	if ( is_singular() ) {
		$pairs = ce_superseded_pairs();
		$id    = (int) get_queried_object_id();
		if ( isset( $pairs[ $id ] ) ) {
			wp_safe_redirect( get_permalink( $pairs[ $id ] ), 301 );
			exit;
		}
		return;
	}
	if ( ! is_404() ) {
		return;
	}
	// After the old article is in the Trash its address is a 404. The last path segment is its slug, whatever
	// the prefix (/articles/, /qa/, or none), so the redirect does not depend on one permalink base.
	$path = trim( (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ), PHP_URL_PATH ), '/' );
	$slug = sanitize_title( (string) basename( $path ) );
	if ( '' === $slug ) {
		return;
	}
	if ( '' !== ce_sync_removal_block() ) {
		return; // The same guards as the removal: while one holds, no address is redirected.
	}
	// Only an article the sync created and moved to the Trash qualifies; an unrelated 404 that happens to end in
	// a mapped slug does not.
	$retired = get_posts( [ 'post_type' => 'ce_article', 'post_status' => 'trash', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_ce_seed_key', 'meta_value' => $slug ] );
	if ( ! $retired ) {
		return;
	}
	foreach ( ce_supersedes() as $pairs ) {
		if ( isset( $pairs[ $slug ] ) ) {
			$new = get_posts( [ 'name' => $pairs[ $slug ], 'post_type' => 'ce_article', 'post_status' => 'publish', 'numberposts' => 1 ] );
			if ( $new ) {
				wp_safe_redirect( get_permalink( $new[0] ), 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'ce_superseded_redirect', 1 );

/**
 * While the unlist module is in charge, superseded articles are part of its list (see ce_unlisted_ids()).
 * When it stands down (the Unlist Posts plugin is active), this hides them: out of lists, search and feeds,
 * out of the sitemaps, and noindex on their own page. An editor at work sees everything.
 */
function ce_superseded_fallback(): void {
	if ( ce_unlist_active() ) {
		return;
	}
	add_action(
		'pre_get_posts',
		static function ( $q ) {
			if ( ce_sync_disabled() || ( is_admin() && ! wp_doing_ajax() ) || $q->is_singular() || $q->get( 'p' ) || $q->get( 'name' ) ) {
				return;
			}
			$ids = ce_superseded_ids();
			if ( $ids && ! $q->get( 'post__in' ) ) {
				$q->set( 'post__not_in', array_values( array_unique( array_merge( (array) $q->get( 'post__not_in' ), $ids ) ) ) );
			}
		}
	);
	add_filter(
		'wp_sitemaps_posts_query_args',
		static function ( $args ) {
			$ids = ce_superseded_ids();
			if ( $ids ) {
				$args['post__not_in'] = array_merge( (array) ( $args['post__not_in'] ?? [] ), $ids );
			}
			return $args;
		}
	);
	add_filter(
		'wp_robots',
		static function ( $robots ) {
			return is_singular() && in_array( (int) get_queried_object_id(), ce_superseded_ids(), true ) ? wp_robots_no_robots( $robots ) : $robots;
		}
	);
}
add_action( 'init', 'ce_superseded_fallback', 20 );
