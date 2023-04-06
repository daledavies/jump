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

abstract class AbstractAPI {

    public function __construct(
        protected \Jump\Config $config,
        protected \Jump\Cache $cache,
        protected \Nette\Http\Session $session,
        protected \Jump\Language $language,
        protected ?array $routeparams
    ){}

    protected function send_json_header(): void {
        header('Content-Type: application/json; charset=utf-8');
    }

    protected function validate_token(): void {
        $this->send_json_header();

        // Get a Nette session section for CSRF data.
        $csrfsection = $this->session->getSection('csrf');

        // Has a CSRF token been set up for the session yet?
        if (!$csrfsection->offsetExists('token')){
            http_response_code(401);
            die(json_encode(['error' => 'Session not fully set up']));
        }

        // Check CSRF token saved in session against token provided via request.
        if (!isset($this->routeparams['token']) || !hash_equals($csrfsection->get('token'), $this->routeparams['token'])) {
            http_response_code(401);
            die(json_encode(['error' => 'API token is incorrect or missing']));
        }
        // Close the session as soon as possible to avoid session lock blocking other scripts.
        $this->session->close();
    }

    abstract protected function get_output(): string;

}
