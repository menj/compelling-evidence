<?php
/**
 * Compelling Evidence — AEO Meta Boxes & Schema
 *
 * Provides two meta boxes on ce_article and post edit screens:
 *
 *   1. "FAQ Items" — repeatable question/answer pairs stored as the
 *      _ce_faq_items array meta. When populated, the article emits FAQPage
 *      JSON-LD on its public view, making the questions eligible for
 *      featured-snippet and AI-Overview citation.
 *
 *   2. "AEO Entities" — comma-separated About and Mentions strings stored
 *      as _ce_about and _ce_mentions. When populated, ce-seo.php's Article
 *      schema gains `about` and `mentions` arrays of Thing objects, helping
 *      search engines disambiguate the article's subject matter and connect
 *      it to entities in their knowledge graph.
 *
 * Both meta boxes are gated by the edit_post capability and protected by
 * standard WordPress nonces.
 *
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ═══════════════════════════════════════════════════════════════════════
   META BOX REGISTRATION
   ═══════════════════════════════════════════════════════════════════════ */

function ce_aeo_register_meta_boxes() {
    foreach ( [ 'ce_article', 'post' ] as $screen ) {
        add_meta_box(
            'ce_aeo_faq',
            __( 'FAQ Items (FAQPage schema)', 'compelling-evidence' ),
            'ce_aeo_render_faq_meta_box',
            $screen,
            'normal',
            'default'
        );
        add_meta_box(
            'ce_aeo_entities',
            __( 'AEO Entities (about & mentions)', 'compelling-evidence' ),
            'ce_aeo_render_entities_meta_box',
            $screen,
            'normal',
            'default'
        );
    }
}
add_action( 'add_meta_boxes', 'ce_aeo_register_meta_boxes' );


/* ═══════════════════════════════════════════════════════════════════════
   RENDER — FAQ ITEMS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_aeo_render_faq_meta_box( $post ) {
    wp_nonce_field( 'ce_aeo_faq_save', 'ce_aeo_faq_nonce' );

    $items = get_post_meta( $post->ID, '_ce_faq_items', true );
    if ( ! is_array( $items ) ) {
        $items = [];
    }
    // Always render at least one empty row so the editor has something to fill in.
    if ( empty( $items ) ) {
        $items = [ [ 'q' => '', 'a' => '' ] ];
    }
    ?>
    <p class="description" style="margin:0 0 1em;">
        <?php esc_html_e( 'Add 2–6 question/answer pairs that this article addresses. Pairs marked here are emitted as FAQPage JSON-LD, making them eligible for featured-snippet and AI-Overview citation. Plain text only — keep answers concise (50–250 words each).', 'compelling-evidence' ); ?>
    </p>

    <div id="ce-aeo-faq-list" class="ce-aeo-faq-list">
        <?php foreach ( $items as $i => $item ) : ?>
            <?php ce_aeo_render_faq_row( (int) $i, (array) $item ); ?>
        <?php endforeach; ?>
    </div>

    <p style="margin-top:1em;">
        <button type="button" class="button" id="ce-aeo-faq-add">
            <?php esc_html_e( '+ Add FAQ pair', 'compelling-evidence' ); ?>
        </button>
    </p>

    <script type="text/template" id="ce-aeo-faq-row-template">
        <?php ce_aeo_render_faq_row( '__INDEX__', [ 'q' => '', 'a' => '' ] ); ?>
    </script>

    <script>
    (function () {
        var list = document.getElementById('ce-aeo-faq-list');
        var addBtn = document.getElementById('ce-aeo-faq-add');
        var template = document.getElementById('ce-aeo-faq-row-template');
        if (!list || !addBtn || !template) return;

        function nextIndex() {
            var rows = list.querySelectorAll('.ce-aeo-faq-row');
            return rows.length;
        }

        addBtn.addEventListener('click', function () {
            var idx = nextIndex();
            var html = template.innerHTML.replace(/__INDEX__/g, String(idx));
            var wrap = document.createElement('div');
            wrap.innerHTML = html;
            // First child of wrap is the new row
            list.appendChild(wrap.firstElementChild);
        });

        list.addEventListener('click', function (e) {
            var btn = e.target.closest('.ce-aeo-faq-remove');
            if (!btn) return;
            var row = btn.closest('.ce-aeo-faq-row');
            if (!row) return;
            // Don't remove the last remaining row — clear it instead so the
            // user always has at least one editable slot.
            var rows = list.querySelectorAll('.ce-aeo-faq-row');
            if (rows.length <= 1) {
                row.querySelectorAll('input,textarea').forEach(function (el) { el.value = ''; });
            } else {
                row.remove();
            }
        });
    })();
    </script>
    <?php
}

/**
 * Render a single FAQ row — extracted so the JS template and the PHP loop
 * produce identical markup.
 */
function ce_aeo_render_faq_row( $index, array $item ) {
    $q = isset( $item['q'] ) ? (string) $item['q'] : '';
    $a = isset( $item['a'] ) ? (string) $item['a'] : '';
    ?>
    <div class="ce-aeo-faq-row" style="border:1px solid #ccd0d4; padding:0.8em 1em; margin-bottom:0.8em; background:#fbfbfb; border-radius:3px;">
        <p style="margin:0 0 0.5em;">
            <label style="display:block; font-weight:600; margin-bottom:0.3em;">
                <?php esc_html_e( 'Question', 'compelling-evidence' ); ?>
            </label>
            <input
                type="text"
                name="ce_aeo_faq[<?php echo esc_attr( $index ); ?>][q]"
                value="<?php echo esc_attr( $q ); ?>"
                style="width:100%;"
                placeholder="<?php esc_attr_e( 'e.g. Did Islam spread by the sword?', 'compelling-evidence' ); ?>"
            >
        </p>
        <p style="margin:0 0 0.3em;">
            <label style="display:block; font-weight:600; margin-bottom:0.3em;">
                <?php esc_html_e( 'Answer', 'compelling-evidence' ); ?>
            </label>
            <textarea
                name="ce_aeo_faq[<?php echo esc_attr( $index ); ?>][a]"
                rows="3"
                style="width:100%;"
                placeholder="<?php esc_attr_e( '50–250 words. Plain text. The first sentence should be the direct answer.', 'compelling-evidence' ); ?>"
            ><?php echo esc_textarea( $a ); ?></textarea>
        </p>
        <p style="margin:0; text-align:right;">
            <button type="button" class="button-link ce-aeo-faq-remove" style="color:#a00;">
                <?php esc_html_e( '× Remove', 'compelling-evidence' ); ?>
            </button>
        </p>
    </div>
    <?php
}


/* ═══════════════════════════════════════════════════════════════════════
   RENDER — AEO ENTITIES (about & mentions)
   ═══════════════════════════════════════════════════════════════════════ */

function ce_aeo_render_entities_meta_box( $post ) {
    wp_nonce_field( 'ce_aeo_entities_save', 'ce_aeo_entities_nonce' );

    $about    = (string) get_post_meta( $post->ID, '_ce_about',    true );
    $mentions = (string) get_post_meta( $post->ID, '_ce_mentions', true );
    ?>
    <p class="description" style="margin:0 0 1em;">
        <?php esc_html_e( 'Comma-separated entity names. "About" should list the 1–3 primary subjects of this article (e.g. "Muhammad, Prophethood"). "Mentions" should list secondary entities discussed (e.g. "Mecca, Quran, Banu Qurayza"). These are emitted as schema.org Thing objects in the Article schema, helping search engines disambiguate the article\'s subject matter.', 'compelling-evidence' ); ?>
    </p>

    <p>
        <label for="ce_about" style="display:block; font-weight:600; margin-bottom:0.3em;">
            <?php esc_html_e( 'About (primary entities)', 'compelling-evidence' ); ?>
        </label>
        <input
            type="text"
            id="ce_about"
            name="ce_about"
            value="<?php echo esc_attr( $about ); ?>"
            style="width:100%;"
            placeholder="<?php esc_attr_e( 'Muhammad, Prophethood, Islamic apologetics', 'compelling-evidence' ); ?>"
        >
    </p>

    <p>
        <label for="ce_mentions" style="display:block; font-weight:600; margin-bottom:0.3em;">
            <?php esc_html_e( 'Mentions (secondary entities)', 'compelling-evidence' ); ?>
        </label>
        <input
            type="text"
            id="ce_mentions"
            name="ce_mentions"
            value="<?php echo esc_attr( $mentions ); ?>"
            style="width:100%;"
            placeholder="<?php esc_attr_e( 'Mecca, Quran, Banu Qurayza, Treaty of Hudaybiyyah', 'compelling-evidence' ); ?>"
        >
    </p>
    <?php
}


/* ═══════════════════════════════════════════════════════════════════════
   SAVE — FAQ ITEMS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_aeo_save_faq_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['ce_aeo_faq_nonce'] ) ) {
        return;
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['ce_aeo_faq_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'ce_aeo_faq_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $raw = isset( $_POST['ce_aeo_faq'] ) && is_array( $_POST['ce_aeo_faq'] )
        ? wp_unslash( $_POST['ce_aeo_faq'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each item is sanitised in the loop below.
        : [];

    $clean = [];
    foreach ( $raw as $row ) {
        if ( ! is_array( $row ) ) {
            continue;
        }
        $q = isset( $row['q'] ) ? sanitize_text_field( (string) $row['q'] ) : '';
        $a = isset( $row['a'] ) ? sanitize_textarea_field( (string) $row['a'] ) : '';
        // Skip pairs where either side is empty — incomplete rows would emit
        // malformed FAQPage schema and trip Search Console validation.
        if ( '' === trim( $q ) || '' === trim( $a ) ) {
            continue;
        }
        $clean[] = [ 'q' => $q, 'a' => $a ];
    }

    if ( empty( $clean ) ) {
        delete_post_meta( $post_id, '_ce_faq_items' );
    } else {
        update_post_meta( $post_id, '_ce_faq_items', $clean );
    }
}
add_action( 'save_post_ce_article', 'ce_aeo_save_faq_meta' );
add_action( 'save_post_post',       'ce_aeo_save_faq_meta' );


/* ═══════════════════════════════════════════════════════════════════════
   SAVE — AEO ENTITIES
   ═══════════════════════════════════════════════════════════════════════ */

function ce_aeo_save_entities_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['ce_aeo_entities_nonce'] ) ) {
        return;
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['ce_aeo_entities_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'ce_aeo_entities_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    foreach ( [ 'ce_about', 'ce_mentions' ] as $field ) {
        $value = isset( $_POST[ $field ] )
            ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) )
            : '';
        if ( '' === trim( $value ) ) {
            delete_post_meta( $post_id, '_' . $field );
        } else {
            update_post_meta( $post_id, '_' . $field, $value );
        }
    }
}
add_action( 'save_post_ce_article', 'ce_aeo_save_entities_meta' );
add_action( 'save_post_post',       'ce_aeo_save_entities_meta' );


/* ═══════════════════════════════════════════════════════════════════════
   HELPERS — Read meta back as schema-ready arrays
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Return the FAQPage mainEntity array for the given post, or null if no FAQ
 * items have been configured. Used by ce-seo.php to merge FAQ schema into
 * the article's @graph.
 *
 * @param int|WP_Post|null $post Defaults to current post.
 * @return array|null FAQPage schema array, or null if no items.
 */
function ce_aeo_get_faq_schema( $post = null ): ?array {
    $post = get_post( $post );
    if ( ! $post ) {
        return null;
    }
    $items = get_post_meta( $post->ID, '_ce_faq_items', true );
    if ( ! is_array( $items ) || empty( $items ) ) {
        return null;
    }

    $main_entity = [];
    foreach ( $items as $item ) {
        if ( empty( $item['q'] ) || empty( $item['a'] ) ) {
            continue;
        }
        $main_entity[] = [
            '@type'          => 'Question',
            'name'           => (string) $item['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => (string) $item['a'],
            ],
        ];
    }

    if ( empty( $main_entity ) ) {
        return null;
    }

    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        '@id'        => trailingslashit( get_permalink( $post ) ) . '#faq',
        'mainEntity' => $main_entity,
    ];
}

/**
 * Parse a comma-separated entity string into an array of schema.org Thing
 * objects. Used by ce-seo.php for `about` and `mentions` fields.
 *
 * @param string $csv Comma-separated entity names.
 * @return array<array> List of Thing schema objects, possibly empty.
 */
function ce_aeo_parse_entities( string $csv ): array {
    if ( '' === trim( $csv ) ) {
        return [];
    }
    $parts = array_map( 'trim', explode( ',', $csv ) );
    $parts = array_filter( $parts, function ( $p ) {
        return '' !== $p;
    } );
    $parts = array_values( array_unique( $parts ) );

    return array_map( function ( $name ) {
        return [
            '@type' => 'Thing',
            'name'  => $name,
        ];
    }, $parts );
}
