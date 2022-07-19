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

namespace Jump\Pages;

class ErrorPage {

    private string $content;

    public function __construct(private \Jump\Cache $cache, private \Jump\Config $config, private int $httpcode, public string $message) {
        $this->mustache = new \Mustache_Engine([
            'loader' => new \Mustache_Loader_FilesystemLoader($this->config->get('templatedir'))
        ]);
        $this->content = $cache->load(cachename: 'templates/errorpage', key: $httpcode.md5($message), callback: function() use ($httpcode, $message) {
            $template = $this->mustache->loadTemplate('errorpage');
            return $template->render([
                'code' => $httpcode,
                'message' => $message,
                'wwwurl' => $this->config->get_wwwurl(),
            ]);
        });
    }

    public function init() {
        http_response_code($this->httpcode);
        die($this->content);
    }

}
