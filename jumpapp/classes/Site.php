<?php

namespace Jump;

/**
 * Parse the data required to represent a site and provide method for generating
 * and/or retrieving the site's icon.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Site {

    private Config $config;
    public string $name;
    public bool $nofollow;
    public string $icon;
    public string $url;

    public function __construct(Config $config, array $sitearray, array $default) {
        $this->config = $config;
        $this->defaults = $default;
        if (!isset($sitearray['name'], $sitearray['url'])) {
            throw new \Exception('The array passed to Site() must contain the keys "name" and "url"!');
        }
        $this->name = $sitearray['name'];
        $this->url = $sitearray['url'];
        $this->nofollow = isset($sitearray['nofollow']) ? $sitearray['nofollow'] : (isset($this->defaults['nofollow']) ? $this->defaults['nofollow'] : false);
        $this->icon = isset($sitearray['icon']) ? $this->get_favicon_datauri($sitearray['icon']) : $this->get_favicon_datauri();
    }

    /**
     * Return a data uri for a given icon, or a site's favicon if an icon
     * is not provided.
     *
     * @param string|null $icon File name of a given icon to retrieve.
     * @return string Base 64 encoded datauri for the icon image.
     */
    public function get_favicon_datauri(?string $icon = null): string {
        // Use the applications own default icon unless one is supplied via the sites.json file.
        $defaulticon = $this->config->get('defaulticonpath');
        if (isset($this->defaults['icon'])) {
            $defaulticon = $this->config->get('sitesdir').'/icons/'.$this->defaults['icon'];
        }
        // Did we have a supplied icon or are we going to try retrieving the favicon?
        if ($icon === null) {
            // Go get the favicon, if there isnt one then use the default icon.
            $favicon = new \Favicon\Favicon();
            $favicon->cache([
                'dir' => $this->config->get('cachedir').'/icons/',
                'timeout' => 86400
            ]);
            $rawimage = $favicon->get($this->url, \Favicon\FaviconDLType::RAW_IMAGE);
            if (!$rawimage) {
                $rawimage = file_get_contents($defaulticon);
            }
        } else {
            $rawimage = file_get_contents($this->config->get('sitesdir').'/icons/'.$icon);
        }
        $mimetype = (new \finfo(FILEINFO_MIME_TYPE))->buffer($rawimage);
        return 'data:'.$mimetype.';base64,'.base64_encode($rawimage);
    }

}
