<?php

namespace App\Services;

/**
 * Service for handling URLs and preventing CORS issues between localhost and 127.0.0.1
 */
class UrlService
{
    /**
     * Get the normalized host that should be used for all URLs
     *
     * @return string
     */
    public function getNormalizedHost(): string
    {
        // Always prefer 127.0.0.1 over localhost for consistency
        return '127.0.0.1';
    }

    /**
     * Generate a URL for a storage file path
     *
     * @param string $path Path relative to the storage directory
     * @return string
     */
    public function storageUrl(string $path): string
    {
        // Strip any leading slashes
        $path = ltrim($path, '/');
        
        // Use the image proxy to prevent CORS issues
        return url('/image-proxy.php?path=' . urlencode($path));
    }

    /**
     * Normalize a URL to prevent CORS issues
     *
     * @param string $url Original URL
     * @return string Normalized URL
     */
    public function normalizeUrl(string $url): string
    {
        // Replace localhost with 127.0.0.1 in the URL
        return str_replace('localhost', $this->getNormalizedHost(), $url);
    }
} 