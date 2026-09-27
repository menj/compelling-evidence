<?php
/**
 * Secure JSON Article Loader
 * 
 * Loads article data from JSON files with integrity verification.
 * Replaces the old PHP require() system to prevent code execution attacks.
 *
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class CE_Article_Loader {
    
    private string $articles_dir;
    private array $errors = [];
    
    public function __construct() {
        $this->articles_dir = get_stylesheet_directory() . '/inc/articles';
    }
    
    /**
     * Load all articles from JSON batches with integrity verification
     * 
     * @return array Array of all articles, or empty array on failure
     */
    public function load_all_articles(): array {
        $all_articles = [];
        
        // Load and verify manifest
        $manifest = $this->load_manifest();
        if ( $manifest === null ) {
            $this->log_error( 'Manifest load failed - cannot proceed with article loading' );
            return [];
        }
        
        // CRITICAL: Verify data completeness before proceeding
        if ( ! $this->validate_data_completeness( $manifest ) ) {
            $this->log_error( 'Article data incomplete — run build-articles.php before deploying' );
            return [];
        }
        
        // Process each batch
        foreach ( $manifest['batches'] as $batch_info ) {
            $batch_articles = $this->load_batch( $batch_info );
            
            if ( $batch_articles === null ) {
                // Batch failed checksum or parsing - log and continue
                $this->log_error( "Batch {$batch_info['file']} skipped due to integrity failure" );
                continue;
            }
            
            $all_articles = array_merge( $all_articles, $batch_articles );
        }
        
        // Sort by order field
        usort( $all_articles, fn( $a, $b ) => $a['order'] <=> $b['order'] );
        
        return $all_articles;
    }
    
    /**
     * Validate that all article data is present and converted
     * Prevents sync from running with placeholder/empty JSON files
     * 
     * @param array $manifest The loaded manifest
     * @return bool True if data is complete, false otherwise
     */
    private function validate_data_completeness( array $manifest ): bool {
        // NOTE: Currently 7 batches (batch-001 through batch-007), 131 articles.
        // $expected_batches and $min_expected_articles are floors (the checks use <),
        // so adding batches never requires changing them.
        $expected_batches = 5;
        $min_expected_articles = 110; // Floor, well below the current total
        
        // Check batch count
        if ( ! isset( $manifest['batches'] ) || count( $manifest['batches'] ) < $expected_batches ) {
            $this->log_error( 
                "Incomplete data: Expected {$expected_batches} batches, found " . 
                ( isset( $manifest['batches'] ) ? count( $manifest['batches'] ) : 0 )
            );
            return false;
        }
        
        // Calculate total articles from manifest
        $total_articles = 0;
        foreach ( $manifest['batches'] as $batch ) {
            if ( isset( $batch['articles'] ) ) {
                $total_articles += (int) $batch['articles'];
            }
            
            // Check for placeholder/pending status in batch files
            $batch_file = $this->articles_dir . '/' . $batch['file'];
            if ( file_exists( $batch_file ) ) {
                $content = file_get_contents( $batch_file );
                if ( $content !== false && (
                    strpos( $content, 'PENDING_CONVERSION' ) !== false ||
                    strpos( $content, '"articles": []' ) !== false ||
                    strpos( $content, 'PLACEHOLDER' ) !== false
                ) ) {
                    $this->log_error( "Batch {$batch['file']} contains placeholder data — run converter" );
                    return false;
                }
            }
        }
        
        // Check minimum article count
        if ( $total_articles < $min_expected_articles ) {
            $this->log_error( 
                "Incomplete data: Expected at least 110 articles, found {$total_articles}. " .
                "Run build-articles.php to convert all articles."
            );
            return false;
        }
        
        return true;
    }
    
    /**
     * Load and verify manifest file
     * 
     * @return array|null Manifest data or null on failure
     */
    private function load_manifest(): ?array {
        $manifest_path = $this->articles_dir . '/manifest.json';
        
        if ( ! file_exists( $manifest_path ) ) {
            $this->log_error( 'Manifest file not found: ' . $manifest_path );
            return null;
        }
        
        $json = file_get_contents( $manifest_path );
        if ( $json === false ) {
            $this->log_error( 'Cannot read manifest file' );
            return null;
        }
        
        // Strip UTF-8 BOM and normalize line endings (common FTP/editor issue)
        $json = $this->normalize_content( $json, 'manifest.json' );
        
        // Decode JSON (never execute, just parse)
        $manifest = json_decode( $json, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $this->log_error( 'Manifest JSON parse error: ' . json_last_error_msg() );
            return null;
        }
        
        // Validate structure
        if ( ! isset( $manifest['batches'] ) || ! is_array( $manifest['batches'] ) ) {
            $this->log_error( 'Manifest missing batches array' );
            return null;
        }
        
        return $manifest;
    }
    
    /**
     * Load a single batch file with checksum verification
     * 
     * @param array $batch_info Batch metadata from manifest
     * @return array|null Articles array or null on failure
     */
    private function load_batch( array $batch_info ): ?array {
        $file_path = $this->articles_dir . '/' . $batch_info['file'];
        
        // Check file exists
        if ( ! file_exists( $file_path ) ) {
            $this->log_error( "Batch file not found: {$batch_info['file']}" );
            return null;
        }
        
        // Read file
        $json = file_get_contents( $file_path );
        if ( $json === false ) {
            $this->log_error( "Cannot read batch file: {$batch_info['file']}" );
            return null;
        }
        
        // Strip UTF-8 BOM and normalize line endings
        $json = $this->normalize_content( $json, $batch_info['file'] );
        
        // Verify checksum (security: detect tampering)
        $actual_checksum = hash( 'sha256', $json );
        if ( ! isset( $batch_info['checksum'] ) ) {
            $this->log_error( "Batch {$batch_info['file']} missing checksum in manifest" );
            return null;
        }
        
        if ( $actual_checksum !== $batch_info['checksum'] ) {
            $this->log_error( 
                "CHECKSUM MISMATCH for {$batch_info['file']}! " .
                "Expected: {$batch_info['checksum']}, Got: {$actual_checksum}. " .
                "Possible file tampering detected."
            );
            return null;
        }
        
        // Decode JSON (safe: data only, never executed)
        $data = json_decode( $json, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            $this->log_error( "Batch {$batch_info['file']} JSON error: " . json_last_error_msg() );
            return null;
        }
        
        // Validate structure
        if ( ! isset( $data['articles'] ) || ! is_array( $data['articles'] ) ) {
            $this->log_error( "Batch {$batch_info['file']} missing articles array" );
            return null;
        }
        
        // Validate and sanitize each article
        $articles = [];
        foreach ( $data['articles'] as $article ) {
            $validated = $this->validate_article( $article );
            if ( $validated !== null ) {
                $articles[] = $validated;
            }
        }
        
        return $articles;
    }
    
    /**
     * Validate and sanitize a single article
     * 
     * @param array $article Raw article data
     * @return array|null Validated article or null
     */
    private function validate_article( array $article ): ?array {
        $required = ['slug', 'title', 'topic', 'order', 'excerpt', 'content'];
        
        foreach ( $required as $field ) {
            if ( ! isset( $article[$field] ) ) {
                $this->log_error( "Article missing required field: {$field}" );
                return null;
            }
        }
        
        // Sanitize all fields
        return [
            'slug'    => sanitize_title( $article['slug'] ),
            'title'   => sanitize_text_field( $article['title'] ),
            'topic'   => sanitize_text_field( $article['topic'] ),
            'order'   => (int) $article['order'],
            'excerpt' => wp_kses_post( $article['excerpt'] ),
            'content' => wp_kses_post( $article['content'] ),
        ];
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function log_error( string $message ): void {
        $this->errors[] = $message;
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'CE Article Loader: ' . $message );
        }
    }
    
    /**
     * Get all accumulated errors
     * 
     * @return array Error messages
     */
    public function get_errors(): array {
        return $this->errors;
    }
    
    /**
     * Check if any errors occurred
     * 
     * @return bool True if errors exist
     */
    public function has_errors(): bool {
        return ! empty( $this->errors );
    }
    
    /**
     * Normalize file content: strip UTF-8 BOM and normalize line endings
     * 
     * FTP clients, Windows editors, and some hosting panels commonly introduce
     * BOM prefixes and CRLF line endings that break JSON parsing and checksums.
     * 
     * @param string $str String that may contain BOM or CRLF
     * @param string $filename Optional filename for logging
     * @return string Normalized string (no BOM, LF line endings)
     */
    private function normalize_content( string $str, string $filename = '' ): string {
        // Strip UTF-8 BOM if present
        if ( substr( $str, 0, 3 ) === "\xEF\xBB\xBF" ) {
            $str = substr( $str, 3 );
            if ( $filename ) {
                $this->log_error( "BOM detected and stripped from {$filename} (check your FTP client settings)" );
            }
        }
        
        // Normalize CRLF to LF (Windows line endings break checksums)
        $str = str_replace( "\r\n", "\n", $str );
        
        return $str;
    }
}

/**
 * Helper function to load all articles
 * 
 * @return array All articles from JSON files
 */
function ce_load_articles_from_json(): array {
    $loader = new CE_Article_Loader();
    $articles = $loader->load_all_articles();
    
    if ( $loader->has_errors() && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CE: Article loading completed with errors: ' . count( $loader->get_errors() ) );
    }
    
    return $articles;
}
