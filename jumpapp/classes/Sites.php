<?php

namespace Jump;

use \Exception;
use \Jump\Exceptions\TagNotFoundException;

/**
 * Loads, validates and caches the site data defined in sites.json
 * into an array of Site objects.
 *
 * TO DO: Implement search() method.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Sites {

    private array $default;
    private string $sitesfilelocation;
    private array $loadedsites;

    /**
     * Automatically load sites.json on instantiation.
     */
    public function __construct(private Config $config, private Cache $cache) {
        $this->config = $config;
        $this->loadedsites = [];
        $this->sitesfilelocation = $this->config->get('sitesfile');
        $this->cache = $cache;
        $this->default = [
            'icon' => null,
            'nofollow' => false,
            'newtab' => false,
        ];
        $this->tags = [];

        // Retrieve sites from cache. Load all sites from json file if not cached or
        // the cache has expired.
        $this->loadedsites = $this->cache->load(cachename: 'sites', callback: function() {
            return $this->load_sites_from_json();
        });

        // Enumerate a list of unique tags from loaded sites. Again will retrieve from
        // cache if available.
        $this->tags = $this->cache->load(cachename: 'tags', callback: function() {
            $uniquetags = [];
            foreach (array_column($this->get_sites(), 'tags') as $tags) {
                foreach ($tags as $tag) {
                    $uniquetags[] = $tag;
                }
            }
            return array_values(array_unique($uniquetags));
        });
    }

    /**
     * Try to load the list of sites from sites.json.
     *
     * Throws an exception if the file cannot be loaded, is empty, or cannot
     * be decoded to an array,
     *
     * @return array Array of Site objects sites loaded from sites.json
     * @throws Exception If sites.json cannot be found.
     */
    private function load_sites_from_json(): array {
        $allsites = [];
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
        // First we'll assume maybe the old format for sites.json.
        if (is_array($decodedjson)) {
            $allsites = $decodedjson;
        }
        // Now check for the newer format.
        if (isset($decodedjson->sites) && is_array($decodedjson->sites)) {
            $allsites = $decodedjson->sites;
            $this->default = (array) $decodedjson->default;
        }

        // Instantiate an actual Site() object for each element.
        foreach ($allsites as $key => $item) {
            $allsites[$key] = new Site($this->config, (array) $item, $this->default);
        }

        // Return the array of Site() objects, note we are in a callback
        // so the return is not from the outer function.
        return $allsites;
    }

    /**
     * Returns an array of all loaded Site objects.
     *
     * @return array Array of all loaded Site objects.
     */
    public function get_sites(): array {
        return $this->loadedsites;
    }

    /**
     * Return array of tags sorted alphabetically, minus the home tag.
     *
     * @return array Array of tag names.
     */
    public function get_tags_for_template(): array {
        $template_tags = [];
        foreach ($this->tags as $tag) {
            if ($tag === 'home') {
                continue;
            }
            $template_tags[] = $tag;
        }
        sort($template_tags);
        return $template_tags;
    }

    /**
     * Given a URL, does that site exist in our list of sites?
     *
     * @param string $url The URL to search for.
     * @return Site A matching Site object if found.
     * @throws Exception If a site with given URL does not exist.
     */
    public function get_site_by_url(string $url): Site {
        $found = array_search($url, array_column($this->get_sites(), 'url'));
        if ($found === false) {
            throw new Exception('The site could not be found ('.$url.')');
        }
        return $this->loadedsites[$found];
    }

    /**
     * Returns an array of Site objects with a given tag.
     *
     * @param string $tagname The tag to look look up sites.
     * @return array Array of Site objects with the given tag.
     * @throws Exception If there are no sites tagged with $tagname.
     */
    public function get_sites_by_tag(string $tagname): array {
        if (!in_array($tagname, $this->tags)) {
            throw new TagNotFoundException('No sites have been tagged with "'.$tagname.'"');
        }
        $found = [];
        foreach ($this->get_sites() as $site) {
            if (in_array($tagname, $site->tags)) {
                $found[] = $site;
            }
        }
        return $found;
    }

    public function get_sites_for_search(): array {
        $searchlist = [];
        foreach ($this->loadedsites as $loadedsite) {
            $site = new \stdClass();
            $site->name = $loadedsite->name;
            $site->url = $loadedsite->url;
            $site->tags = $loadedsite->tags;
            $site->iconurl = '/api/icon.php?siteurl='.urlencode($loadedsite->url);
            $searchlist[] = $site;
        }
        return $searchlist;
    }

}
