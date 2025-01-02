<?php

/**
 * Get the current route path
 * @return string The current route path
 */
function get_current_route()
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    
    // Remove query string if present
    if (($pos = strpos($uri, '?')) !== false) {
        $uri = substr($uri, 0, $pos);
    }
    
    // Remove trailing slash except for root path
    $uri = $uri !== '/' ? rtrim($uri, '/') : $uri;
    
    // Remove base path if exists
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
    
    return $uri;
}
