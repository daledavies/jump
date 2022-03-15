<?php

namespace Jump\Pages;

class HomePage extends AbstractPage {

    protected function render_header(): string {
        $template = $this->mustache->loadTemplate('header');
        $greeting = null;
        if (!$this->config->parse_bool($this->config->get('showgreeting'))) {
            $greeting = 'home';
        }
        return $template->render([
            'greeting' => $greeting,
            'noindex' => $this->config->parse_bool($this->config->get('noindex')),
            'title' => $this->config->get('sitename'),
            'owmapikey' => !!$this->config->get('owmapikey', false),
        ]);
    }

    protected function render_content(): string {
        return $this->cache->load(cachename: 'templates/sites', callback: function() {
            $sites = new \Jump\Sites($this->config, $this->cache);
            $template = $this->mustache->loadTemplate('sites');
            return $template->render([
                'hassites' => !empty($sites->get_sites()),
                'sites' => $sites->get_sites_by_tag('home'),
            ]);
        });
    }

    protected function render_footer(): string {
        return $this->cache->load(cachename: 'templates/sites', key: 'footer', callback: function() {
            $sites = new \Jump\Sites(config: $this->config, cache: $this->cache);
            $tags = $sites->get_tags_for_template();
            $template = $this->mustache->loadTemplate('footer');
            return $template->render([
                'hastags' => !empty($tags),
                'tags' => $tags,
                'showclock' => $this->config->parse_bool($this->config->get('showclock'))
            ]);
        });
    }

}
