<?php

/**
 * TO DO:
 * - use CSRF token in weatherdata and icon api
 *
 */

namespace Jump;

use Nette\Routing\RouteList;

class Main {

    private Cache $cache;
    private Config $config;

    public function __construct() {
        $this->config = new Config();
        $this->cache = new Cache($this->config);
        $this->router = new RouteList;

        // Set up the routes that Jump expects.
        $this->router->addRoute('/tag/<param>', [
			'class' => 'Jump\Pages\TagPage'
		]);
    }

    function init() {
        // Try to match the correct route based on the HTTP request.
        $matchedroute = $this->router->match(
            (new \Nette\Http\RequestFactory)->fromGlobals()
        );

        // If we do not have a matched route then just serve up the home page.
        $pageclass = $matchedroute['class'] ?? 'Jump\Pages\HomePage';
        $param = $matchedroute['param'] ?? null;

        // Instantiate the correct class to build the requested page, get the
        // content and return it.
        $page = new $pageclass($this->config, $this->cache, $param ?? null);
        return $page->get_output();
    }

}
