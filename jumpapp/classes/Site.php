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

    public string $name;
    public bool $nofollow;
    public string $icon;
    public string $url;

    public function __construct(array $sitearray) {
        if (!isset($sitearray['name'], $sitearray['url'])) {
            throw new \Exception('The array passed to Site() must contain the keys "name" and "url"!');
        }
        $this->name = $sitearray['name'];
        $this->url = $sitearray['url'];
        $this->nofollow = isset($sitearray['nofollow']) ? $sitearray['nofollow'] : false;
        $this->icon = isset($sitearray['icon']) ? $this->get_favicon_datauri($sitearray['icon']) : $this->get_favicon_datauri();
    }

    public function get_favicon_datauri(?string $icon = null): string {
        if ($icon === null) {
            $favicon = new \Favicon\Favicon();
            $favicon->cache([
                'dir' => '/var/www/cache/icons/',
                'timeout' => 86400
            ]);
            $rawimage = $favicon->get($this->url, \Favicon\FaviconDLType::RAW_IMAGE);
            if (!$rawimage) {
                $rawimage = file_get_contents(__DIR__ . '/../assets/images/default-icon.png');
            }
        } else {
            $rawimage = file_get_contents(__DIR__ . '/../sites/icons/'.$icon);
        }
        $mimetype = (new \finfo(FILEINFO_MIME_TYPE))->buffer($rawimage);
        return 'data:'.$mimetype.';base64,'.base64_encode($rawimage);
    }

}