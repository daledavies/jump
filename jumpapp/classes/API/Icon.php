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

namespace Jump\API;

use \Jump\Exceptions\APIException;

class Icon extends AbstractAPI {

    public function get_output(): string {
        if (!isset($this->routeparams['siteid']) || empty($this->routeparams['siteid'])) {
            throw new APIException('The siteid query parameter is not provided or empty');
        }

        $sites = new \Jump\Sites($this->config, $this->cache);

        # A site ID can contain lowercase a-z, 0-9 and the "-" (dash) character only.
        $siteid = preg_replace("/[^a-z0-9-]/", "", $this->routeparams['siteid']);
        $site = $sites->get_site_by_id($siteid);

        $imagedata =  $site->get_favicon_image_data();

        // We made it here so output the API response as json.
        header('Content-Type: '.$imagedata->mimetype);
        return $imagedata->data;
    }

}
