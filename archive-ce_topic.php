<?php
/**
 * Topic Archive — SUPERSEDED
 *
 * This file is kept as a fallback but is no longer used.
 * WordPress uses taxonomy-ce_topic.php for /topic/[slug]/ URLs.
 * See taxonomy-ce_topic.php for the active template.
 */

// Redirect to the correct template
// Note: get_header()/get_footer() intentionally omitted — this is a redirect-only shim.
get_template_part('taxonomy-ce_topic');
