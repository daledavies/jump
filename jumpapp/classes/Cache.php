<?php

namespace Jump;

use Nette\Caching;

/**
 * Defines caches to be used throughout the site and provides a wrapper around
 * the Nette\Caching library.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Cache {

    private Caching\Storages\FileStorage $storage;

    /**
     * The definition of various caches used throughout the application.
     *
     * @var array Multidimensional array
     */
    private array $caches;

    /**
     * Creates file storage for cache and initialises cache objects for each
     * name/type specified in $caches definition.
     */
    public function __construct(private Config $config) {
        // Define the various caches used throughout the app.
        $this->caches = [
            'searchengines' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::FILES,
                'expirationparams' => $config->get('searchenginesfile')
            ],
            'sites' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::FILES,
                'expirationparams' => $config->get('sitesfile')
            ],
            'tags' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::FILES,
                'expirationparams' => $config->get('sitesfile')
            ],
            'templates/sites' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::FILES,
                'expirationparams' => [
                    __DIR__.'/../config.php',
                    $config->get('sitesfile'),
                    $config->get('templatedir').'/sites.mustache'
                ]
            ],
            'templates/errorpage' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::FILES,
                'expirationparams' => [
                    $config->get('templatedir').'/errorpage.mustache'
                ]
            ],
            'weatherdata' => [
                'cache' => null,
                'expirationtype' => Caching\Cache::EXPIRE,
                'expirationparams' => '5 minutes'
            ],
        ];
        // Inititalise file storage for cache using cachedir path from config.
        $this->storage = new Caching\Storages\FileStorage($this->config->get('cachedir').'/application');
    }

    /**
     * Read the specified item from the cache or generate it, mostly a wrapper
     * around Nette\Caching\Cache::load().
     *
     * @param string $cachename The name of a cache, must match a key in $caches definition.
     * @param string $key A key used to represent an object within a cache,
     * @param callable $callback The code from which the result should be stored in cache.
     * @return mixed The result of callback function retreieved from cache.
     */
    public function load(string $cachename, ?string $key = 'default', callable $callback): mixed {
        // If cachebypass has been set in config.php then just execute the callback.
        if ($this->config->parse_bool($this->config->get('cachebypass'))) {
            return $callback();
        }
        // We can only work with caches that have already been defined.
        if (!array_key_exists($cachename, $this->caches)) {
            throw new \Exception('Cache name not found ('.$cachename.')');
        }
        // If a cache key has not been used then intialise a cache object for it.
        if (!isset($this->caches[$cachename]['cache']) || !array_key_exists($key, $this->caches[$cachename]['cache'])) {
            $this->caches[$cachename]['cache'][$key] = new Caching\Cache($this->storage, $cachename.'/'.$key);
        }
        // Retrieve the initialised cache object from $caches, defines the caches expiry
        // and executes the callback.
        return $this->caches[$cachename]['cache'][$key]->load($cachename.'/'.$key,
            function (&$dependencies) use ($callback, $cachename) {
                $dependencies[$this->caches[$cachename]['expirationtype']] = $this->caches[$cachename]['expirationparams'];
                return $callback();
            }
        );
    }

}
