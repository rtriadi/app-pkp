<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $minutes = 5): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Skip caching for authenticated user specific data
        if ($request->user()) {
            return $next($request);
        }

        $cacheKey = $this->generateCacheKey($request);

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            return $this->createResponseFromCache($cachedResponse);
        }

        // Execute request and cache response
        $response = $next($request);

        // Only cache successful responses
        if ($response->getStatusCode() === 200) {
            $this->cacheResponse($cacheKey, $response, $minutes);
        }

        return $response;
    }

    /**
     * Generate a unique cache key for the request.
     */
    private function generateCacheKey(Request $request): string
    {
        $key = 'response:' . $request->getPathInfo();

        // Include query parameters in cache key
        if ($request->query()) {
            ksort($request->query());
            $key .= '?' . http_build_query($request->query());
        }

        return md5($key);
    }

    /**
     * Cache the response data.
     */
    private function cacheResponse(string $key, Response $response, int $minutes): void
    {
        $cacheData = [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
        ];

        Cache::put($key, $cacheData, now()->addMinutes($minutes));
    }

    /**
     * Create a response from cached data.
     */
    private function createResponseFromCache(array $cachedData): Response
    {
        $response = new Response(
            $cachedData['content'],
            $cachedData['status']
        );

        // Restore headers
        foreach ($cachedData['headers'] as $name => $values) {
            foreach ($values as $value) {
                $response->headers->set($name, $value);
            }
        }

        // Add cache header to indicate this is a cached response
        $response->headers->set('X-Cache-Status', 'HIT');

        return $response;
    }
}
