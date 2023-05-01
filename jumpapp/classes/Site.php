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

use stdClass;

/**
 * Parse the data required to represent a site and provide method for generating
 * and/or retrieving the site's icon.
 */
class Site {

    public string $id;
    public string $name;
    public bool $nofollow;
    public ?string $iconname;
    public string $url;
    public array $tags = ['home'];

    /**
     * Parse the data required to represent a site and provide method for generating
     * and/or retrieving the site's icon.
     *
     * @param Config $config A Jump Config() object.
     * @param array $sitearray Array of options for this site from sites.json.
     * @param array $defaults Array of default values for this site to use, defined in sites.json.
     */
    public function __construct(private Config $config, private Cache $cache, array $sitearray, private array $defaults) {
        if (!isset($sitearray['name'], $sitearray['url'])) {
            throw new \Exception('The array passed to Site() must contain the keys "name" and "url"!');
        }
        $this->id = 'site-'.md5($sitearray['url']);
        $this->name = $sitearray['name'];
        $this->url = $sitearray['url'];
        $this->nofollow = isset($sitearray['nofollow']) ? $sitearray['nofollow'] : (isset($this->defaults['nofollow']) ? $this->defaults['nofollow'] : false);
        $this->newtab = isset($sitearray['newtab']) ? $sitearray['newtab'] : (isset($this->defaults['newtab']) ? $this->defaults['newtab'] : false);
        $this->iconname = $sitearray['icon'] ?? null;
        $this->tags = $sitearray['tags'] ?? $this->tags;
        $this->description = isset($sitearray['description']) ? $sitearray['description'] : $sitearray['name'];
        $this->status = $sitearray['status'] ?? null;
    }

    /**
     * Return an object containing mimetype and raw image data, or a site's
     * favicon if an icon is not provided in sites.json.
     *
     * @return object Containing mimetype and raw image data.
     */
    public function get_favicon_image_data(): object {
        return $this->cache->load(cachename: 'sites/favicons', key: $this->id, callback: function() {
            // Use the applications own default icon unless one is supplied via the sites.json file.
            $defaulticon = $this->config->get('defaulticonpath');
            if (isset($this->defaults['icon'])) {
                $defaulticon = $this->config->get('sitesdir').'/icons/'.$this->defaults['icon'];
            }
            // Did we have a supplied icon or are we going to try retrieving the favicon?
            if ($this->iconname === null) {
                // Go get the favicon, if there isnt one then use the default icon.
                $favicon = new \Favicon\Favicon();
                if (!$this->config->parse_bool($this->config->get('cachebypass'))) {
                    $favicon->cache(['dir' => $this->config->get('cachedir').'/favicon']);
                }
                $rawimage = $favicon->get($this->url, \Favicon\FaviconDLType::RAW_IMAGE);
            } else {
                // If the icon name has a file extension the n try to retrieve it locally, otherwise
                // see if we can get it from Dashboard Icons.
                if (pathinfo($this->iconname, PATHINFO_EXTENSION)) {
                    $file = $this->config->get('sitesdir').'/icons/'.$this->iconname;
                    $errormessage = 'Icon file not found... '.$file;
                } else {
                    $file = 'https://cdn.jsdelivr.net/gh/walkxcode/dashboard-icons@master/svg/'.$this->iconname.'.svg';
                    $errormessage = 'Dashboard icon does not exist... '.$this->iconname;
                }
                $rawimage = @file_get_contents($file);
                if (!$rawimage) {
                    error_log($errormessage);
                }
            }
            // If we didnt manage to get any icon data from any of the above methods then return
            // the default icon.
            if (!$rawimage) {
                $rawimage = file_get_contents($defaulticon);
            }
            $imagedata = new stdClass();
            $imagedata->mimetype = (new \finfo(FILEINFO_MIME_TYPE))->buffer($rawimage);
            $imagedata->data = $rawimage;
            return $imagedata;
        });
    }

    /**
     * Return a data uri or a site's favicon if an icon is not provided.
     *
     * @return string Base 64 encoded datauri for the icon image.
     */
    public function get_favicon_datauri(): string {
        $imagedata = $this->get_favicon_image_data();
        return 'data:'.$imagedata->mimetype.';base64,'.base64_encode($imagedata->data);
    }

    /**
     * Get the online status of this site.
     *
     * @return string The site status.
     */
    public function get_status(): string {
        $cache = new Cache($this->config);
        return (new Status($cache, $this))->get_status();
    }
}
