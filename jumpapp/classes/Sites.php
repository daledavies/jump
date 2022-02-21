<?php

namespace Jump;

use Exception;

/**
 * Loads, validates and caches the site data defined in sites.json
 * into an array of Site objects.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Sites {

    private Cache $cache;
    private Config $config;
    private array $default;
    private string $sitesfilelocation;
    private array $loadedsites;

    /**
     * Automatically load sites.json on instantiation.
     */
    public function __construct(Config $config, Cache $cache) {
        $this->config = $config;
        $this->loadedsites = [];
        $this->sitesfilelocation = $this->config->get('sitesfile');
        $this->cache = $cache;
        $this->default = [
            'icon' => null,
            'nofollow' => false
        ];
        $this->load_sites_from_json();
    }

    /**
     * Try to load the list of sites from site.json.
     *
     * Throws an exception if the file cannot be loaded, is empty, or cannot
     * be decoded to an array,
     *
     * @return void
     * @throws Exception if sites.json cannot be found
     */
    private function load_sites_from_json(): void {
        $this->loadedsites = $this->cache->load('sites', function() {
            $sites = [];
            $rawjson = file_get_contents($this->sitesfilelocation);
            if ($rawjson === false) {
                throw new Exception('There was a problem loading the sites.json file');
            }
            if ($rawjson === '') {
                throw new Exception('The sites.json file is empty');
            }
            // Do some checks to see if the JSON decodes into something
            // like what we expect to see...
            $decodedjson = json_decode($rawjson);
            if (is_array($decodedjson)) {
                $sites = $decodedjson;
            }
            if (isset($decodedjson->sites) && is_array($decodedjson->sites)) {
                $sites = $decodedjson->sites;
                $this->default = (array) $decodedjson->default;
            }
            // Walk over the sites array and instantiate an actual Site() object
            // for each element.
            array_walk($sites, function(&$item, $key, $default) {
                $item = new Site($this->config, (array) $item, $default);
            }, $this->default);
            // Return the array of Site() objects, note we are in a callback
            // so the return is not from the outer function.
            return $sites;
        });
    }

    /**
     * Return the loaded sites.
     *
     * @return array of sites loaded from sites.json
     */
    public function get_sites(): array {
        return $this->loadedsites;
    }

    /**
     * Given a URL, does that site exist in our list of sites?
     *
     * @param string $url The URL to search for.
     * @return Site
     */
    public function get_site_by_url(string $url): Site {
        $found = array_search($url, array_column($this->get_sites(), 'url'));
        if (!$found) {
            throw new Exception('The site could not be found ('.$url.')');
        }
        return $this->loadedsites[$found];
    }
}