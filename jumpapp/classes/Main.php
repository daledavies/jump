<?php

namespace Jump;

class Main {

    private Cache $cache;
    private \Mustache_Engine $mustache;
    private array $outputarray;
    private Sites $sites;

    public function __construct() {
        $this->config = new Config();
        $this->mustache = new \Mustache_Engine([
            'loader' => new \Mustache_Loader_FilesystemLoader($this->config->get('templatedir'))
        ]);
        $this->cache = new Cache($this->config);
        $this->sites = new Sites($this->config, $this->cache);
    }

    private function render_header(): string {
        $template = $this->mustache->loadTemplate('header');
        return $template->render([
            'noindex' => $this->config->parse_bool($this->config->get('noindex')),
            'sitename' => $this->config->get('sitename'),
            'latlong' => $this->config->get('latlong', false),
            'owmapikey' => $this->config->get('owmapikey', false)
        ]);
    }

    private function render_sites(): string {
        return $this->cache->load('templates/sites', function() {
            $template = $this->mustache->loadTemplate('sites');
            return $template->render([
                'hassites' => !empty($this->sites->get_sites()),
                'sites' => $this->sites->get_sites()
            ]);
        });
    }

    private function render_footer(): string {
        $template = $this->mustache->loadTemplate('footer');
        return $template->render();
    }

    public function build_index_page(): void {
        $this->outputarray = [
            $this->render_header(),
            $this->render_sites(),
            $this->render_footer(),
        ];
    }

    public function get_output(): string {
        return implode('', $this->outputarray);
    }

}
