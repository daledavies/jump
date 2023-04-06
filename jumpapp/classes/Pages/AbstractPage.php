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

abstract class AbstractPage {

    protected \Mustache_Engine $mustache;
    private array $outputarray;

    /**
     * Construct an instance of a page.
     *
     * @param \Jump\Config $config
     * @param \Jump\Cache $cache
     * @param string|null $generic param, passed from router.
     */
    public function __construct(
        protected \Jump\Config $config,
        protected \Jump\Cache $cache,
        protected \Nette\Http\Session $session,
        protected \Jump\Language $language,
        protected ?array $routeparams
    ){
        $this->hastags = false;
        $this->mustache = new \Mustache_Engine([
            'loader' => new \Mustache_Loader_FilesystemLoader($this->config->get('templatedir')),
            // Create a urlencodde helper for use in template. E.g. using siteurl in icon.php query param.
            'helpers' => [
                'urlencode' => function($text, $renderer) {
                    return urlencode($renderer($text));
                },
                'language' => function($text, $renderer) {
                    return $this->language->get($text);
                },
            ],
        ]);
        // Get a Nette session section for CSRF data.
        $csrfsection = $this->session->getSection('csrf');
        // Create a new CSRF token within the section if one doesn't exist already.
        if (!$csrfsection->offsetExists('token')){
            $csrfsection->set('token', bin2hex(random_bytes(32)));
        }
        // Close the session as soon as possible to avoid session lock blocking other scripts.
        $this->session->close();
    }

    abstract protected function render_content(): string;

    abstract protected function render_header(): string;

    abstract protected function render_footer(): string;

    protected function build_page(): void {
        $this->outputarray = [
            $this->render_header(),
            $this->render_content(),
            $this->render_footer(),
        ];
    }

    public function get_output(): string {
        $this->build_page();
        return implode('', $this->outputarray);
    }

}
