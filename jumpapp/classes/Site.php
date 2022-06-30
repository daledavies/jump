<?php

namespace Jump;

use stdClass;

/**
 * Parse the data required to represent a site and provide method for generating
 * and/or retrieving the site's icon.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Site {

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
    public function __construct(private Config $config, array $sitearray, private array $defaults) {
        if (!isset($sitearray['name'], $sitearray['url'])) {
            throw new \Exception('The array passed to Site() must contain the keys "name" and "url"!');
        }
        $this->name = $sitearray['name'];
        $this->url = $sitearray['url'];
        $this->nofollow = isset($sitearray['nofollow']) ? $sitearray['nofollow'] : (isset($this->defaults['nofollow']) ? $this->defaults['nofollow'] : false);
        $this->newtab = isset($sitearray['newtab']) ? $sitearray['newtab'] : (isset($this->defaults['newtab']) ? $this->defaults['newtab'] : false);
        $this->iconname = $sitearray['icon'] ?? null;
        $this->tags = $sitearray['tags'] ?? $this->tags;
        $this->description = isset($sitearray['description']) ? $sitearray['description'] : $sitearray['name'];
    }

    /**
     * Return an object containing mimetype and raw image data, or a site's
     * favicon if an icon is not provided in sites.json.
     *
     * @return object Containing mimetype and raw image data.
     */
    public function get_favicon_image_data(): object {
        // Use the applications own default icon unless one is supplied via the sites.json file.
        $defaulticon = $this->config->get('defaulticonpath');
        if (isset($this->defaults['icon'])) {
            $defaulticon = $this->config->get('sitesdir').'/icons/'.$this->defaults['icon'];
        }
        // Did we have a supplied icon or are we going to try retrieving the favicon?
        if ($this->iconname === null) {
            // Go get the favicon, if there isnt one then use the default icon.
            $favicon = new \Favicon\Favicon();
            $favicon->cache([
                'dir' => $this->config->get('cachedir').'/icons',
                'timeout' => 86400
            ]);
            $rawimage = $favicon->get($this->url, \Favicon\FaviconDLType::RAW_IMAGE);
            if (!$rawimage) {
                $rawimage = file_get_contents($defaulticon);
            }
        } else {
            $rawimage = file_get_contents($this->config->get('sitesdir').'/icons/'.$this->iconname);
        }
        $imagedata = new stdClass();
        $imagedata->mimetype = (new \finfo(FILEINFO_MIME_TYPE))->buffer($rawimage);
        $imagedata->data = $rawimage;
        return $imagedata;
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

}
