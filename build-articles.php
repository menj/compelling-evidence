<?php
/**
 * Article Data Converter: PHP → JSON
 * 
 * Converts legacy PHP article data files to secure JSON format with checksums.
 * Parses PHP source files directly without execution (safe extraction).
 * 
 * Usage: php build-articles.php
 */

if ( php_sapi_name() !== 'cli' ) {
    die( "ERROR: This script must be run from command line\n" );
}

echo "=== Compelling Evidence Article Converter ===\n\n";

$source_files = [
    'inc/articles/backup/articles-data.php'   => ['batch' => '001', 'func' => 'ce_get_articles_data'],
    'inc/articles/backup/articles-data-2.php' => ['batch' => '002', 'func' => 'ce_get_articles_data_2'],
    'inc/articles/backup/articles-data-3.php' => ['batch' => '003', 'func' => 'ce_get_articles_data_3'],
    'inc/articles/backup/articles-data-4.php' => ['batch' => '004', 'func' => 'ce_get_articles_data_4'],
    'inc/articles/backup/articles-data-5.php' => ['batch' => '005', 'func' => 'ce_get_articles_data_5'],
    'inc/articles/backup/articles-data-6.php' => ['batch' => '006', 'func' => 'ce_get_articles_data_6'],
];

$output_dir = 'inc/articles';
$manifest = [
    'version' => '2.3.6',
    'generated' => date('c'),
    'batches' => [],
];

// Create output directory
if ( ! is_dir( $output_dir ) ) {
    mkdir( $output_dir, 0755, true );
    echo "Created directory: {$output_dir}\n";
}

$total_articles = 0;

foreach ( $source_files as $source => $config ) {
    echo "Processing {$source}...\n";
    
    if ( ! file_exists( $source ) ) {
        echo "  ERROR: Source file not found: {$source}\n";
        continue;
    }
    
    // Read PHP source
    $php_source = file_get_contents( $source );
    if ( $php_source === false ) {
        echo "  ERROR: Cannot read {$source}\n";
        continue;
    }
    
    // Extract articles using regex pattern matching
    $articles = extract_articles_from_php( $php_source );
    
    if ( empty( $articles ) ) {
        echo "  WARNING: No articles found in {$source}\n";
        continue;
    }
    
    $output_file = 'batch-' . $config['batch'] . '.json';
    
    // Build JSON structure
    $json_data = [
        'meta' => [
            'batch' => $config['batch'],
            'generated' => date('c'),
            'source_file' => $source,
            'article_count' => count( $articles ),
        ],
        'articles' => $articles,
    ];
    
    // Generate JSON with pretty printing
    $json = json_encode( $json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    
    if ( $json === false ) {
        echo "  ERROR: JSON encoding failed: " . json_last_error_msg() . "\n";
        continue;
    }
    
    // Calculate checksum on the FINAL json that will be written to disk.
    // IMPORTANT: Do NOT modify $json after this point — the loader verifies
    // the file content against this exact hash. Adding meta.checksum after
    // this line was the previous bug that caused all checksums to mismatch.
    $checksum = hash( 'sha256', $json );
    
    // Write file
    $output_path = $output_dir . '/' . $output_file;
    $result = file_put_contents( $output_path, $json );
    
    if ( $result === false ) {
        echo "  ERROR: Failed to write {$output_path}\n";
        continue;
    }
    
    echo "  Created: {$output_path}\n";
    echo "  Articles: " . count( $articles ) . "\n";
    echo "  Checksum: {$checksum}\n\n";
    
    // Add to manifest
    $manifest['batches'][] = [
        'file' => $output_file,
        'source' => $source,
        'checksum' => $checksum,
        'articles' => count( $articles ),
    ];
    
    $total_articles += count( $articles );
}

// Generate manifest checksum
$manifest_json = json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
$manifest['checksum'] = hash( 'sha256', $manifest_json );

// Re-encode with checksum
$manifest_json = json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

// Write manifest
$manifest_path = $output_dir . '/manifest.json';
file_put_contents( $manifest_path, $manifest_json );

echo "Created manifest: {$manifest_path}\n";
echo "Manifest checksum: {$manifest['checksum']}\n";
echo "\n=== Conversion Complete ===\n";
echo "Total articles converted: {$total_articles}\n";
echo "Output directory: {$output_dir}/\n";
echo "\nFiles generated:\n";
foreach ( $manifest['batches'] as $batch ) {
    echo "  - {$batch['file']} ({$batch['articles']} articles)\n";
}
echo "  - manifest.json\n";
echo "\nNEXT STEPS:\n";
echo "1. Verify the JSON files in {$output_dir}/\n";
echo "2. Deploy theme with new JSON-based system\n";
echo "3. Visit /wp-admin/ to trigger content sync\n";
echo "4. Remove legacy PHP files after successful migration:\n";
foreach ( $source_files as $source => $config ) {
    echo "   - {$source}\n";
}

/**
 * Extract articles from PHP source using regex
 * 
 * @param string $php_source The PHP file source code
 * @return array Array of article data
 */
function extract_articles_from_php( string $php_source ): array {
    $articles = [];
    
    // Pattern to match article array entries
    // Matches: ['slug' => '...', 'title' => '...', ...],
    $article_pattern = '/\[\s*\'slug\'\s*=>\s*\'([^\']+)\'\s*,\s*\'title\'\s*=>\s*\'([^\']*)\'\s*,\s*\'topic\'\s*=>\s*\'([^\']*)\'\s*,\s*\'order\'\s*=>\s*(\d+)\s*,\s*\'excerpt\'\s*=>\s*\'([^\']*)\'\s*,\s*\'content\'\s*=>\s*<<<\'([^\']+)\'\s*(.+?)\s*\3\s*,\s*\]/s';
    
    if ( preg_match_all( $article_pattern, $php_source, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            $articles[] = [
                'slug'    => $match[1],
                'title'   => unescape_php_string( $match[2] ),
                'topic'   => $match[3],
                'order'   => (int) $match[4],
                'excerpt' => unescape_php_string( $match[5] ),
                'content' => trim( $match[6] ),
            ];
        }
    }
    
    // If regex fails, try alternative parsing for mixed quote syntax
    if ( empty( $articles ) ) {
        $articles = extract_articles_fallback( $php_source );
    }
    
    return $articles;
}

/**
 * Fallback extraction for mixed quote PHP arrays
 */
function extract_articles_fallback( string $php_source ): array {
    $articles = [];
    
    // Find all array blocks that start with 'slug'
    $pattern = '/\[\s*[\'"]slug[\'"]\s*=>\s*([\'"])([^\1]*)\1/';
    
    if ( preg_match_all( '/\[\s*[\'"]slug[\'"]\s*=>.*?\],/s', $php_source, $blocks ) ) {
        foreach ( $blocks[0] as $block ) {
            $article = [];
            
            // Extract each field with flexible quote handling
            $fields = ['slug', 'title', 'topic', 'order', 'excerpt'];
            foreach ( $fields as $field ) {
                if ( preg_match( '/[\'"]' . $field . '[\'"]\s*=>\s*([\'"])([^\1]*?)\1/', $block, $m ) ) {
                    $article[$field] = unescape_php_string( $m[2] );
                } elseif ( preg_match( '/[\'"]' . $field . '[\'"]\s*=>\s*(\d+)/', $block, $m ) ) {
                    $article[$field] = (int) $m[1];
                }
            }
            
            // Extract nowdoc/heredoc content
            if ( preg_match( '/[\'"]content[\'"]\s*=>\s*<<<\'([^\']+)\'\s*(.+?)\s*\1\s*,/s', $block, $m ) ) {
                $article['content'] = trim( $m[2] );
            }
            
            if ( ! empty( $article['slug'] ) ) {
                $articles[] = $article;
            }
        }
    }
    
    return $articles;
}

/**
 * Unescape PHP string (\' → ', \" → ", etc)
 */
function unescape_php_string( string $str ): string {
    return str_replace( ["\\'", '\\"', "\\\\", "\\n", "\\r", "\\t"], ["'", '"', "\\", "\n", "\r", "\t"], $str );
}
