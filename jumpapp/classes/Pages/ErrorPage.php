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
    public static function display(\Jump\Config $config, int $httpcode, string $message) {
        $mustache = new \Mustache_Engine([
            'loader' => new \Mustache_Loader_FilesystemLoader($config->get('templatedir'))
        ]);
        $template = $mustache->loadTemplate('errorpage');
        $content = $template->render([
            'code' => $httpcode,
            'message' => $message,
            'wwwurl' => $config->get_wwwurl(),
        ]);
        die($content);
    }
}
