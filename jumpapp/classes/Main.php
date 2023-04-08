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

use \Jump\Pages\ErrorPage;
use \Tracy\Debugger;

class Main {

    private Cache $cache;
    private Config $config;
    private Language $language;
    private \Nette\Http\Request $request;
    private \Nette\Routing\RouteList $router;
    private \Nette\Http\Session $session;

    public function __construct() {
        // We can't do anything without the config object.
        $this->config = new Config();

        // Set something to either display detailed debugging info or handle exceptions
        // as early as possible during initialisation.
        if ($this->config->get('debug')) {
            Debugger::enable(Debugger::Development);
        } else {
            set_exception_handler([$this, 'exception_handler']);
        }

        // Carry on setting things up.
        $this->cache = new Cache($this->config);
        $this->router = new \Nette\Routing\RouteList;
        $this->language = new Language($this->config, $this->cache);

        // Set up the routes that Jump expects.
        $this->router->addRoute('/', [
			'class' => 'Jump\Pages\HomePage'
		]);
        $this->router->addRoute('/tag/<tag>', [
			'class' => 'Jump\Pages\TagPage'
		]);
        $this->router->addRoute('/api/icon?siteid=<siteid>', [
			'class' => 'Jump\API\Icon'
		]);
        $this->router->addRoute('/api/status[/<token>]', [
			'class' => 'Jump\API\Status'
		]);
        $this->router->addRoute('/api/unsplash[/<token>]', [
			'class' => 'Jump\API\Unsplash'
		]);
        $this->router->addRoute('/api/weather[/<token>[/<lat>[/<lon>]]]', [
			'class' => 'Jump\API\Weather'
		]);
    }

    public function init() {
        // Create a request object based on globals so we can utilise url rewriting etc.
        $this->request = (new \Nette\Http\RequestFactory)->fromGlobals();

        // Initialise a new session using the request object.
        $this->session = new \Nette\Http\Session($this->request, new \Nette\Http\Response);
        $this->session->setName($this->config->get('sessionname'));
        $this->session->setExpiration($this->config->get('sessiontimeout'));

        // Try to match the correct route based on the HTTP request.
        $matchedroute = $this->router->match($this->request);

        // If we do not have a matched route then just serve up the home page.
        $outputclass = $matchedroute['class'] ?? 'Jump\Pages\HomePage';

        // Instantiate the correct class to build the requested page, get the
        // content and return it.
        $page = new $outputclass($this->config, $this->cache, $this->session, $this->language, $matchedroute ?? null);
        return $page->get_output();
    }

    /**
     * Global exception handler, display friendly message if something goes wrong.
     *
     * @param $exception
     * @return void
     */
    public function exception_handler($exception): void {
        error_log($exception->getMessage());
        ErrorPage::display($this->config, 500, 'Something went wrong, please use debug option to see details.');
    }

}
