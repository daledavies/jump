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

class Status extends AbstractAPI {

    public function get_output(): string {
        $this->validate_token();
        $statusarray = [];
        $sites = (new \Jump\Sites($this->config, $this->cache))->get_sites();
        foreach ($sites as $site) {
            $status = $site->get_status();
            $statusarray[$site->id] = $status;
        }
        return json_encode($statusarray);
    }

}
