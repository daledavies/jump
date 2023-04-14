<?php
/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2022, Dale Davies
 * @license MIT
 */

namespace Jump;

use \divineomega\array_undot;
use \Jump\Exceptions\ConfigException;
use \Jump\Exceptions\SiteNotFoundException;
use \Jump\Exceptions\TagNotFoundException;

/**
 * Loads, validates and caches the site data defined in sites.json
 * into an array of Site objects.
 */
class Sites {

    private array $default;
    private string $sitesfilelocation;
    private array $loadedsites;
    public array $tags;

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

        // Retrieve sites from cache. Load all sites from json file and docker if not
        // cached or the cache has expired.
        $this->loadedsites = $this->cache->load(cachename: 'sites', callback: function() {
            // Load json file first to set defaults.
            return array_merge($this->load_sites_from_json(), $this->load_sites_from_docker());
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
     * Try to find a list of sites from correctly labelled docker containers.
     *
     * Throws an exception if the json response from docker cannot be
     * decoded.
     *
     * @return array Array of Site objects sites identified from docker.
     * @throws ConfigException If invalid response from docker.
     */
    private function load_sites_from_docker(): array {
        // Get either dockerproxy or dockersocket config and return early if
        // neihter have been set.
        $dockerproxy = $this->config->get('dockerproxyurl');
        $dockersocket = $this->config->get('dockersocket');
        if (!$dockerproxy && !$dockersocket) {
            return [];
        }

        // Determine correct guzzle client and request options to use
        // for either a docker proxy or connecting directly to the socket,
        // prefer to use the proxy if both seem to have been given.
        $clientopts = ['timeout' => 2.0];
        $requestopts = [];
        if ($dockerproxy) {
            $clientopts['base_uri'] = 'http://'.rtrim($dockerproxy, '/');
        } else if (file_exists($dockersocket)) {
            $clientopts['base_uri'] = 'http://localhost';
            $requestopts = [
                'curl' => [CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock']
            ];
        }

        // Make a request to docker for all containers.
        try {
            $response = (new \GuzzleHttp\Client($clientopts))->request('GET', '/containers/json', $requestopts);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new ConfigException('Did not get a response from Docker API endpoint');
        }
        $containers = json_decode($response->getBody());
        if (is_null($containers)) {
            throw new ConfigException('Docker returned an invalid json response for containers');
        }

        // Build a new array of Site() objects based on labels that have been added to
        // containers returned by docker.
        $sites = [];
        foreach ($containers as $container) {
            $labels = (array) $container->Labels;
            // We can't build a Site() without at least a name and url.
            if (!isset($labels['jump.name'], $labels['jump.url'])) {
                continue;
            }
            // Convert dot-syntax labels into a proper multidimensional array
            // and just use the top-level key "jump" as our site array.
            $site = array_undot($labels)['jump'];
            // jump.tags will have been given as a comma separated string so make this
            // into an array.
            if (isset($site['tags'])) {
                // Explode the comma separated string into an array and trim any elements.
                $site['tags'] = array_map('trim', explode(',', $site['tags']));
            }
            // Convert status array to an object and also explode list of allowed status codes to array.
            if (isset($site['status'])) {
                $site['status'] = (object) $site['status'];
                if (isset($site['status']->allowed_status_codes)) {
                    $site['status']->allowed_status_codes = array_map('trim', explode(',', $site['status']->allowed_status_codes));
                }
            }
            // Finally add this to the list of sites we will return.
            $sites[] = new Site($this->config, (array) $site, $this->default);
        }
        return $sites;
    }

    /**
     * Try to load the list of sites from sites.json. Sets defaults if any
     * are found, which will then apply to any docker sites.
     *
     * Throws an exception if the file cannot be loaded, is empty, or cannot
     * be decoded to an array,
     *
     * @return array Array of Site objects sites loaded from sites.json
     * @throws ConfigException If sites.json cannot be found.
     */
    private function load_sites_from_json(): array {

        $docker = function() {
            $dockerproxy = $this->config->get('dockerproxyurl');
            $dockersocket = $this->config->get('dockersocket');
            if ($dockerproxy || $dockersocket) {
                return true;
            }
            return false;
        };

        $allsites = [];
        // If we have been instructed to only look for sites via docker then
        // don't worry about loading a local sites.json file and just
        // return an empty $allsites array.
        if ($this->config->get('dockeronlysites')) {
            if (!$docker()) {
                throw new ConfigException('DOCKERONLYSITES is specified but no Docker endpoint has been provided');
            }
            return $allsites;
        }
        // Try to load the sites.json file.
        $rawjson = @file_get_contents($this->sitesfilelocation);
        if ($rawjson === false) {
            throw new ConfigException('There was a problem loading the sites.json file');
        }
        if ($rawjson === '') {
            throw new ConfigException('The sites.json file is empty, if this is intentional please delete it');
        }
        // Do some checks to see if the JSON decodes into something
        // like what we expect to see...
        $decodedjson = json_decode($rawjson);
        // First we'll assume maybe the old format for sites.json.
        if (is_array($decodedjson)) {
            $allsites = $decodedjson;
        }
        // Handle not having any sites in sites.json or having docker integration set up.
        if (!isset($decodedjson->sites)) {
            if (!$docker()) {
                throw new ConfigException('The sites.json file is empty and docker integration is not set up either');
            }
        }
        // Now check for the newer sites format from sites.json.
        if (is_array($decodedjson->sites)) {
            $allsites = $decodedjson->sites;
        }
        // Extract default site params into an array.
        if (isset($decodedjson->default)) {
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
     * @throws SiteNotFoundException If a site with given URL does not exist.
     */
    public function get_site_by_url(string $url): Site {
        $found = array_search($url, array_column($this->get_sites(), 'url'));
        if ($found === false) {
            throw new SiteNotFoundException($url);
        }
        return $this->loadedsites[$found];
    }

    /**
     * Given a Site ID, does that site exist in our list of sites?
     *
     * @param string $id The Site ID to search for.
     * @return Site A matching Site object if found.
     * @throws SiteNotFoundException If a site with given Site ID does not exist.
     */
    public function get_site_by_id(string $id): Site {
        $found = array_search($id, array_column($this->get_sites(), 'id'));
        if ($found === false) {
            throw new SiteNotFoundException($id);
        }
        return $this->loadedsites[$found];
    }

    /**
     * Returns an array of Site objects with a given tag.
     *
     * @param string $tagname The tag to look look up sites.
     * @return array Array of Site objects with the given tag.
     * @throws TagNotFoundException If there are no sites tagged with $tagname.
     */
    public function get_sites_by_tag(string $tagname): array {
        if (!in_array($tagname, $this->tags)) {
            throw new TagNotFoundException($tagname);
        }
        $found = [];
        foreach ($this->get_sites() as $site) {
            if (in_array($tagname, $site->tags)) {
                $found[] = $site;
            }
        }
        return $found;
    }

    /**
     * Get a list of cached sites from for use in the front end via JS. Some extra details
     * added to each site in the list may not be already saved in the main sites cache.
     *
     * @return array Array of stdClass objects containing required site details.
     */
    public function get_sites_for_frontend(): array {
        $searchlist = [];
        foreach ($this->loadedsites as $loadedsite) {
            $site = new \stdClass();
            $site->id = $loadedsite->id;
            $site->name = $loadedsite->name;
            $site->url = $loadedsite->url;
            $site->tags = $loadedsite->tags;
            $site->iconurl = '/api/icon?siteid='.$loadedsite->id;
            $site->status = $this->cache->load(cachename: 'sites/status', key: $site->url) ?? null;
            $searchlist[] = $site;
        }
        return $searchlist;
    }

}
