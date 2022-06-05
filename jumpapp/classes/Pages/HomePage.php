<?php

namespace Jump\Pages;

class HomePage extends AbstractPage {

    protected function render_header(): string {
        $template = $this->mustache->loadTemplate('header');
        $greeting = null;
        if (!$this->config->parse_bool($this->config->get('showgreeting'))) {
            $greeting = 'home';
        }
        $csrfsection = $this->session->getSection('csrf');
        $unsplashdata = $this->cache->load('unsplash');
        $templatecontext = [
            'csrftoken' => $csrfsection->get('token'),
            'greeting' => $greeting,
            'noindex' => $this->config->parse_bool($this->config->get('noindex')),
            'title' => $this->config->get('sitename'),
            'owmapikey' => !!$this->config->get('owmapikey', false),
            'metrictemp' => $this->config->parse_bool($this->config->get('metrictemp')),
            'ampmclock' => $this->config->parse_bool($this->config->get('ampmclock', false)),
            'unsplash' => !!$this->config->get('unsplashapikey', false),
            'unsplashcolor' => $unsplashdata?->color,
        ];
        if ($this->config->parse_bool($this->config->get('showsearch', false))) {
            $templatecontext = array_merge($templatecontext, [
                'searchengines' => json_encode((new \Jump\SearchEngines($this->config, $this->cache))->get_search_engines()),
                'searchjson' => json_encode((new \Jump\Sites($this->config, $this->cache))->get_sites_for_search()),
            ]);
        }
        return $template->render($templatecontext);
    }

    protected function render_content(): string {
        return $this->cache->load(cachename: 'templates/sites', callback: function() {
            $sites = new \Jump\Sites($this->config, $this->cache);
            $template = $this->mustache->loadTemplate('sites');
            return $template->render([
                'hassites' => !empty($sites->get_sites()),
                'sites' => $sites->get_sites_by_tag('home'),
                'altlayout' => $this->config->parse_bool($this->config->get('altlayout', false)),
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
                'showclock' => $this->config->parse_bool($this->config->get('showclock')),
                'showsearch' => $this->config->parse_bool($this->config->get('showsearch', false)),
            ]);
        });
    }

}
