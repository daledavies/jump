<?php

namespace Jump\API;

class Icon extends AbstractAPI {

    public function get_output(): string {
        if (!isset($this->routeparams['siteurl']) || empty($this->routeparams['siteurl'])) {
            throw new \Exception('The siteurl query parameter is not provided or empty');
        }

        $sites = new \Jump\Sites($this->config, $this->cache);

        $siteurl = filter_var($this->routeparams['siteurl'], FILTER_SANITIZE_URL);
        $site = $sites->get_site_by_url($siteurl);

        $imagedata =  $site->get_favicon_image_data();

        // We made it here so output the API response as json.
        header('Content-Type: '.$imagedata->mimetype);
        return $imagedata->data;
    }

}
