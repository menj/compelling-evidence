<?php
/**
 * Automatic lead images for new articles.
 *
 * Hourly (WP-Cron), and on demand from Theme Options → Media:
 *
 *  1. Find published articles with no featured image and no [ce_figure].
 *  2. Search Pexels with keywords from the title (and, failing that, the
 *     topic), filter the results against the site's image rules, and take
 *     the first acceptable photograph not already used on the site.
 *  3. Register it (option ce_media_auto_registry), import it into the Media
 *     Library, set it as the featured image, and store the figure on the
 *     post (_ce_auto_figure, _ce_auto_caption). The figure is shown after
 *     the first paragraph at render time, so article content sync never
 *     overwrites it.
 *
 * Images publish at once. Theme Options → Media lists every automatic
 * choice with Replace (next acceptable candidate) and Remove.
 *
 * Image rules applied to each candidate's own description:
 *  - never: religious symbols of other faiths, alcohol, pork, weapons in
 *    use, blood, protests, revealing clothing, gambling;
 *  - on sensitive topics (leaving Islam, trauma, abuse, sexuality, mental
 *    illness, prison, cult and violence accusations): no people at all.
 *
 * Registered images from inc/articles/media.json are imported by the same
 * cron run, so no clicks are needed after a deploy.
 *
 * @package CE_Theme
 * @since   2.6.35
 */

defined( 'ABSPATH' ) || exit;

const CE_MEDIA_AUTO_PER_RUN = 5;

/* ═══════════════════════════════════════════════════════════════════════
   RULES
   ═══════════════════════════════════════════════════════════════════════ */

function ce_media_auto_banned_words(): array {
	return (array) apply_filters( 'ce_media_auto_banned_words', [
		'cross', 'crucifix', 'church', 'cathedral', 'chapel', 'christian', 'christmas', 'easter', 'jesus', 'bible', 'rosary',
		'biblia', 'biblical', 'gospel', 'psalm', 'priest', 'nun', 'monk', 'pastor', 'saint', 'angel statue', 'nativity', 'altar',
		'buddha', 'buddhist', 'hindu', 'ganesha', 'shiva', 'temple', 'synagogue', 'menorah', 'star of david', 'idol', 'statue',
		'beer', 'wine', 'whisky', 'whiskey', 'vodka', 'cocktail', 'alcohol', 'champagne', 'bar counter', 'pub',
		'pork', 'bacon', 'ham', 'pig',
		'gun', 'rifle', 'pistol', 'blood', 'bloody', 'corpse', 'dead body', 'protest', 'rally', 'demonstration', 'riot',
		'bikini', 'lingerie', 'underwear', 'swimsuit', 'shirtless', 'topless', 'nude', 'sensual', 'sexy',
		'casino', 'gambling', 'poker', 'tattoo', 'halloween', 'skull',
	] );
}

function ce_media_auto_people_words(): array {
	return [ 'man', 'men', 'woman', 'women', 'person', 'people', 'girl', 'boy', 'child', 'children', 'kid', 'face', 'portrait', 'couple', 'crowd', 'he ', 'she ', 'his ', 'her ', 'male', 'female', 'worshipper', 'student' ];
}

function ce_media_auto_sensitive_words(): array {
	return (array) apply_filters( 'ce_media_auto_sensitive_words', [
		'leave', 'leaving', 'left', 'apostas', 'doubt', 'deconver', 'ex-muslim', 'trauma', 'abuse', 'beat', 'violence', 'honour', 'honor',
		'same-sex', 'gay', 'homosexual', 'sexual', 'mental', 'jinn', 'prison', 'cult', 'control', 'fear', 'anger', 'terror', 'kill', 'hijab', 'women', 'slavery', 'marriage',
	] );
}

function ce_media_auto_is_sensitive( WP_Post $post ): bool {
	$hay   = strtolower( $post->post_title . ' ' . implode( ' ', wp_get_post_terms( $post->ID, 'ce_topic', [ 'fields' => 'names' ] ) ) );
	foreach ( ce_media_auto_sensitive_words() as $w ) {
		if ( false !== strpos( $hay, $w ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Whether a candidate's description passes the image rules.
 */
function ce_media_auto_acceptable( string $alt, bool $sensitive ): bool {
	$alt = ' ' . strtolower( $alt ) . ' ';
	foreach ( ce_media_auto_banned_words() as $w ) {
		if ( preg_match( '/\b' . preg_quote( $w, '/' ) . 's?\b/', $alt ) ) {
			return false;
		}
	}
	if ( $sensitive ) {
		foreach ( ce_media_auto_people_words() as $w ) {
			if ( preg_match( '/\b' . preg_quote( trim( $w ), '/' ) . '\b/', $alt ) ) {
				return false;
			}
		}
	}
	return '' !== trim( $alt );
}

/**
 * Search keywords from a title: drop question words and short fillers.
 */
function ce_media_auto_query( WP_Post $post, bool $sensitive ): string {
	$stop  = [ 'the', 'a', 'an', 'and', 'or', 'of', 'to', 'in', 'on', 'for', 'is', 'are', 'was', 'were', 'be', 'it', 'its', 'if', 'why', 'what', 'how', 'who', 'when', 'does', 'do', 'did', 'can', 'could', 'would', 'should', 'just', 'that', 'this', 'there', 'than', 'with', 'about', 'your', 'you', 'my', 'we', 'our', 'not', 'no', 'islam', 'islamic', 'muslim', 'muslims', 'god', 'quran', 'really', 'actually', 'all', 'any', 'into', 'from', 'by', 'as', 'at', 'so', 'but' ];
	$words = preg_split( '/[^a-z]+/', strtolower( wp_strip_all_tags( $post->post_title ) ), -1, PREG_SPLIT_NO_EMPTY );
	// Words that describe the question rather than its subject.
	$generic = [ 'early', 'first', 'last', 'build', 'built', 'make', 'made', 'makes', 'happen', 'happens', 'happened', 'quietly', 'alone', 'lead', 'leads', 'mean', 'means', 'still', 'ever', 'every', 'many', 'much', 'more', 'most', 'without', 'need', 'needs', 'want', 'wants', 'really', 'true', 'whether', 'they', 'them', 'their', 'have', 'been', 'being', 'will', 'shall', 'than', 'then', 'only', 'even', 'such', 'some', 'faith', 'religion', 'answer', 'question', 'claim', 'claims' ];
	$words   = array_values( array_filter( $words, static function ( $w ) use ( $stop, $generic ) {
		return strlen( $w ) > 3 && ! in_array( $w, $stop, true ) && ! in_array( $w, $generic, true );
	} ) );
	// Keep the most specific (longest) words, in title order.
	$keep = $words;
	usort( $keep, static function ( $a, $b ) {
		return strlen( $b ) <=> strlen( $a );
	} );
	$keep  = array_slice( $keep, 0, 2 );
	$words = array_values( array_filter( $words, static function ( $w ) use ( $keep ) {
		return in_array( $w, $keep, true );
	} ) );
	$q = implode( ' ', array_unique( $words ) );
	if ( $sensitive ) {
		$q .= ' landscape';
	}
	return trim( $q );
}

/**
 * Caption from the photograph's own description: first sentence only, which
 * drops stock-site marketing ("Perfect for…", "Ideal for…").
 */
function ce_media_auto_caption( string $alt ): string {
	$alt = trim( preg_replace( '/\s+/', ' ', $alt ) );
	if ( preg_match( '/^(.+?[.!?])(\s|$)/u', $alt, $m ) ) {
		$alt = $m[1];
	}
	$alt = preg_replace( '/^(a|an)\s+(stunning|beautiful|breathtaking|captivating|mesmerizing|serene|vibrant|striking)\s+/i', '$1 ', $alt );
	$alt = rtrim( $alt, ' .' ) . '.';
	return sanitize_text_field( ucfirst( $alt ) );
}

/* ═══════════════════════════════════════════════════════════════════════
   PEXELS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * @return array<int,array>|WP_Error Photos from the Pexels search API.
 */
function ce_pexels_search( string $query, int $per_page = 15 ) {
	$key = trim( (string) get_option( 'ce_pexels_key', '' ) );
	if ( '' === $key ) {
		return new WP_Error( 'ce_pexels_no_key', 'No Pexels API key is set.' );
	}
	$res = wp_remote_get( add_query_arg( [
		'query'       => rawurlencode( $query ),
		'per_page'    => $per_page,
		'orientation' => 'landscape',
		'size'        => 'large',
	], 'https://api.pexels.com/v1/search' ), [
		'timeout' => 20,
		'headers' => [ 'Authorization' => $key ],
	] );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'ce_pexels_http', 'Pexels returned HTTP ' . (int) wp_remote_retrieve_response_code( $res ) . '.' );
	}
	$data = json_decode( (string) wp_remote_retrieve_body( $res ), true );
	return isset( $data['photos'] ) && is_array( $data['photos'] ) ? $data['photos'] : [];
}

/**
 * Pexels photo ids already used anywhere on the site.
 */
function ce_media_used_pexels_ids(): array {
	$ids = [];
	foreach ( ce_media_registry() as $m ) {
		if ( 'pexels' === ( $m['source'] ?? '' ) && ! empty( $m['file'] ) ) {
			$ids[] = (string) $m['file'];
		}
	}
	$rejected = get_option( 'ce_media_auto_rejected', [] );
	return array_unique( array_merge( $ids, is_array( $rejected ) ? array_map( 'strval', $rejected ) : [] ) );
}

/**
 * Choose, register, import and attach a lead image for one article.
 *
 * @return string|WP_Error Registry id.
 */
function ce_media_auto_pick( WP_Post $post ) {
	$sensitive = ce_media_auto_is_sensitive( $post );
	$used      = ce_media_used_pexels_ids();
	$queries   = array_filter( [
		ce_media_auto_query( $post, $sensitive ),
		implode( ' ', wp_get_post_terms( $post->ID, 'ce_topic', [ 'fields' => 'names' ] ) ) . ( $sensitive ? ' landscape' : '' ),
		$sensitive ? 'calm landscape' : 'mosque architecture',
	] );

	foreach ( array_values( $queries ) as $qi => $q ) {
		$stems  = array_map( static function ( $w ) {
			return substr( $w, 0, 5 );
		}, array_filter( explode( ' ', strtolower( $q ) ), static function ( $w ) {
			return strlen( $w ) > 3 && 'landscape' !== $w;
		} ) );
		$photos = ce_pexels_search( $q );
		if ( is_wp_error( $photos ) ) {
			return $photos;
		}
		foreach ( $photos as $p ) {
			$alt = (string) ( $p['alt'] ?? '' );
			if ( in_array( (string) $p['id'], $used, true ) || (int) $p['width'] < 1600 || ! ce_media_auto_acceptable( $alt, $sensitive ) ) {
				continue;
			}
			// From the title query, the picture's own description must mention the subject.
			if ( 0 === $qi && $stems ) {
				$hit = false;
				foreach ( $stems as $st ) {
					if ( false !== strpos( strtolower( $alt ), $st ) ) {
						$hit = true;
						break;
					}
				}
				if ( ! $hit ) {
					continue;
				}
			}
			$id    = 'pexels-' . (int) $p['id'];
			$entry = [
				'source'       => 'pexels',
				'file'         => (string) $p['id'],
				'url'          => (string) $p['src']['large2x'],
				'thumb'        => (string) $p['src']['large2x'],
				'page'         => (string) $p['url'],
				'author'       => (string) $p['photographer'],
				'author_url'   => (string) $p['photographer_url'],
				'license'      => 'Pexels License',
				'license_url'  => 'https://www.pexels.com/license/',
				'alt'          => wp_html_excerpt( $alt, 160, '…' ),
				'width'        => (int) $p['width'],
				'height'       => (int) $p['height'],
				'variant'      => 'photo',
				'crop'         => null,
				'verified'     => gmdate( 'Y-m-d' ),
				'featured_for' => $post->post_name,
				'auto'         => true,
			];
			$auto        = get_option( 'ce_media_auto_registry', [] );
			$auto        = is_array( $auto ) ? $auto : [];
			$auto[ $id ] = $entry;
			update_option( 'ce_media_auto_registry', $auto, false );
			ce_media_registry( true );

			$att = ce_media_import_one( $id, $entry );
			if ( is_wp_error( $att ) ) {
				unset( $auto[ $id ] );
				update_option( 'ce_media_auto_registry', $auto, false );
				ce_media_registry( true );
				continue;
			}
			$map        = ce_media_attachments();
			$map[ $id ] = $att;
			update_option( 'ce_media_attachments', $map, false );
			set_post_thumbnail( $post, $att );
			update_post_meta( $post->ID, '_ce_auto_figure', $id );
			update_post_meta( $post->ID, '_ce_auto_caption', ce_media_auto_caption( $alt ) );
			return $id;
		}
	}
	return new WP_Error( 'ce_media_auto_none', 'No acceptable photograph found.' );
}

/**
 * Articles that still need a lead image.
 *
 * @return WP_Post[]
 */
function ce_media_auto_candidates( int $limit ): array {
	$posts = get_posts( [
		'post_type'      => 'ce_article',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- cron only, 50 rows.
			[ 'key' => '_thumbnail_id', 'compare' => 'NOT EXISTS' ],
		],
	] );
	$skip = get_option( 'ce_media_auto_skip', [] );
	$skip = is_array( $skip ) ? $skip : [];
	$out  = [];
	foreach ( $posts as $p ) {
		if ( false !== strpos( $p->post_content, '[ce_figure' ) || in_array( $p->ID, $skip, true ) ) {
			continue;
		}
		$out[] = $p;
		if ( count( $out ) >= $limit ) {
			break;
		}
	}
	return $out;
}

/**
 * One automatic run.
 *
 * @return array{placed:int,failed:array,imported:int}
 */
function ce_media_auto_run( int $limit = CE_MEDIA_AUTO_PER_RUN ): array {
	$result = [ 'placed' => 0, 'failed' => [], 'imported' => 0 ];
	if ( get_transient( 'ce_media_auto_lock' ) ) {
		return $result;
	}
	set_transient( 'ce_media_auto_lock', 1, 10 * MINUTE_IN_SECONDS );

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Registered images from media.json first, so deploys need no clicks.
	if ( get_option( 'ce_media_import', '1' ) === '1' ) {
		$imp                = ce_media_import_pending( 10 );
		$result['imported'] = $imp['imported'];
	}

	if ( get_option( 'ce_media_auto', '0' ) === '1' ) {
		foreach ( ce_media_auto_candidates( $limit ) as $post ) {
			$r = ce_media_auto_pick( $post );
			if ( is_wp_error( $r ) ) {
				$result['failed'][ $post->post_name ] = $r->get_error_message();
				if ( 'ce_media_auto_none' === $r->get_error_code() ) {
					$skip   = get_option( 'ce_media_auto_skip', [] );
					$skip[] = $post->ID;
					update_option( 'ce_media_auto_skip', array_values( array_unique( $skip ) ), false );
				}
				if ( 'ce_pexels_no_key' === $r->get_error_code() ) {
					break;
				}
				continue;
			}
			$result['placed']++;
		}
		update_option( 'ce_media_auto_last', [ 'time' => time(), 'result' => $result ], false );
	}
	delete_transient( 'ce_media_auto_lock' );
	return $result;
}

/* ═══════════════════════════════════════════════════════════════════════
   SCHEDULE
   ═══════════════════════════════════════════════════════════════════════ */

function ce_media_auto_schedule(): void {
	if ( ! wp_next_scheduled( 'ce_media_auto_cron' ) ) {
		wp_schedule_event( time() + 300, 'hourly', 'ce_media_auto_cron' );
	}
}
add_action( 'init', 'ce_media_auto_schedule' );
add_action( 'ce_media_auto_cron', 'ce_media_auto_run' );

function ce_media_auto_unschedule(): void {
	wp_clear_scheduled_hook( 'ce_media_auto_cron' );
}
add_action( 'switch_theme', 'ce_media_auto_unschedule' );

/* ═══════════════════════════════════════════════════════════════════════
   RENDER
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Show an automatic lead figure after the first paragraph. Runs before
 * shortcodes (priority 9) so the figure renders like any other.
 */
function ce_media_auto_inject( string $content ): string {
	if ( ! is_singular( 'ce_article' ) || false !== strpos( $content, '[ce_figure' ) ) {
		return $content;
	}
	$id = (string) get_post_meta( get_the_ID(), '_ce_auto_figure', true );
	if ( '' === $id ) {
		return $content;
	}
	$fig = '[ce_figure id="' . esc_attr( $id ) . '"]' . esc_html( (string) get_post_meta( get_the_ID(), '_ce_auto_caption', true ) ) . '[/ce_figure]';
	$pos = stripos( $content, '</p>' );
	return false === $pos ? $fig . $content : substr_replace( $content, '</p>' . "\n" . $fig, $pos, 4 );
}
add_filter( 'the_content', 'ce_media_auto_inject', 9 );

/* ═══════════════════════════════════════════════════════════════════════
   CORRECTIONS (Theme Options → Media)
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Remove an automatic image from an article; optionally pick the next one.
 */
function ce_media_auto_undo( int $post_id, bool $replace ) {
	$post = get_post( $post_id );
	$id   = (string) get_post_meta( $post_id, '_ce_auto_figure', true );
	if ( ! $post || '' === $id ) {
		return new WP_Error( 'ce_media_auto_missing', 'No automatic image on this article.' );
	}
	$auto     = get_option( 'ce_media_auto_registry', [] );
	$rejected = get_option( 'ce_media_auto_rejected', [] );
	$rejected = is_array( $rejected ) ? $rejected : [];
	if ( isset( $auto[ $id ]['file'] ) ) {
		$rejected[] = (string) $auto[ $id ]['file'];
	}
	update_option( 'ce_media_auto_rejected', array_values( array_unique( $rejected ) ), false );

	$map = ce_media_attachments();
	if ( ! empty( $map[ $id ] ) ) {
		if ( (int) get_post_thumbnail_id( $post ) === (int) $map[ $id ] ) {
			delete_post_thumbnail( $post );
		}
		wp_delete_attachment( (int) $map[ $id ], true );
		unset( $map[ $id ] );
		update_option( 'ce_media_attachments', $map, false );
	}
	unset( $auto[ $id ] );
	update_option( 'ce_media_auto_registry', $auto, false );
	ce_media_registry( true );
	delete_post_meta( $post_id, '_ce_auto_figure' );
	delete_post_meta( $post_id, '_ce_auto_caption' );

	if ( ! $replace ) {
		$skip   = get_option( 'ce_media_auto_skip', [] );
		$skip[] = $post_id;
		update_option( 'ce_media_auto_skip', array_values( array_unique( $skip ) ), false );
		return true;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	return ce_media_auto_pick( $post );
}

/**
 * Handle the Media tab's Find, Replace and Remove buttons.
 */
function ce_media_auto_handle_post(): string {
	if ( ! current_user_can( 'manage_options' ) || empty( $_POST['ce_media_auto_action'] ) ) {
		return '';
	}
	check_admin_referer( 'ce_media_auto', 'ce_media_auto_nonce' );
	$action = sanitize_key( wp_unslash( $_POST['ce_media_auto_action'] ) );
	if ( 'run' === $action ) {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may be disabled on the host.
		}
		delete_transient( 'ce_media_auto_lock' );
		$r = ce_media_auto_run( 10 );
		return sprintf( 'Placed %d image(s); imported %d registered image(s).', $r['placed'], $r['imported'] ) . ( $r['failed'] ? ' Not placed: ' . implode( ', ', array_keys( $r['failed'] ) ) . '.' : '' );
	}
	$post_id = isset( $_POST['ce_media_auto_post'] ) ? absint( $_POST['ce_media_auto_post'] ) : 0;
	if ( $post_id && in_array( $action, [ 'replace', 'remove' ], true ) ) {
		$r = ce_media_auto_undo( $post_id, 'replace' === $action );
		return is_wp_error( $r ) ? $r->get_error_message() : ( 'replace' === $action ? 'Image replaced.' : 'Image removed; the article will be left without one.' );
	}
	return '';
}

/**
 * The list of automatic choices, newest first.
 */
function ce_media_auto_panel_html(): string {
	$msg   = ce_media_auto_handle_post();
	$posts = get_posts( [
		'post_type'      => 'ce_article',
		'posts_per_page' => 30,
		'meta_key'       => '_ce_auto_figure', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- admin screen, 30 rows.
		'orderby'        => 'modified',
	] );
	$last  = get_option( 'ce_media_auto_last', [] );
	$nonce = wp_nonce_field( 'ce_media_auto', 'ce_media_auto_nonce', true, false );

	$h  = '<div class="ce-field" style="background:#f8f9fa;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-top:1rem;">';
	$h .= '<h3 style="margin:0 0 .6rem;font-size:1rem;">Automatic images</h3>';
	if ( $msg ) {
		$h .= '<div class="notice notice-info inline" style="margin:0 0 1rem;"><p>' . esc_html( $msg ) . '</p></div>';
	}
	$next = wp_next_scheduled( 'ce_media_auto_cron' );
	$h   .= '<p>Next run: ' . ( $next ? esc_html( human_time_diff( time(), $next ) ) . ' from now' : 'not scheduled' ) . '.';
	if ( ! empty( $last['time'] ) ) {
		$h .= ' Last run ' . esc_html( human_time_diff( (int) $last['time'] ) ) . ' ago.';
	}
	$h .= '</p>' . $nonce . '<button type="submit" name="ce_media_auto_action" value="run" class="button">Run now</button>';

	if ( $posts ) {
		$h .= '<table class="widefat striped" style="margin-top:1rem;"><thead><tr><th>Image</th><th>Article</th><th>Caption</th><th></th></tr></thead><tbody>';
		foreach ( $posts as $p ) {
			$h .= '<tr><td style="width:120px;">' . get_the_post_thumbnail( $p, [ 110, 62 ] ) . '</td>'
				. '<td><a href="' . esc_url( get_permalink( $p ) ) . '" target="_blank">' . esc_html( get_the_title( $p ) ) . '</a></td>'
				. '<td>' . esc_html( (string) get_post_meta( $p->ID, '_ce_auto_caption', true ) ) . '</td>'
				. '<td style="white-space:nowrap;">'
				. '<button type="submit" class="button button-small" name="ce_media_auto_action" value="replace" onclick="this.form.ce_media_auto_post.value=' . (int) $p->ID . '">Replace</button> '
				. '<button type="submit" class="button button-small button-link-delete" name="ce_media_auto_action" value="remove" onclick="this.form.ce_media_auto_post.value=' . (int) $p->ID . '">Remove</button>'
				. '</td></tr>';
		}
		$h .= '</tbody></table><input type="hidden" name="ce_media_auto_post" value="0">';
	} else {
		$h .= '<p style="color:#555;margin-top:1rem;">No automatic images yet.</p>';
	}
	return $h . '</div>';
}
