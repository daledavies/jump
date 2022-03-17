<?php

namespace Jump\Pages;

use \Jump\Exceptions\TagNotFoundException;

class TagPage extends AbstractPage {

    protected function render_header(): string {
        $template = $this->mustache->loadTemplate('header');
        $greeting = $this->param;
        $title = 'Tag: '.$this->param;
        return $template->render([
            'greeting' => $greeting,
            'noindex' => $this->config->parse_bool($this->config->get('noindex')),
            'title' => $title,
            'owmapikey' => !!$this->config->get('owmapikey', false),
            'metrictemp' => $this->config->parse_bool($this->config->get('metrictemp')),
        ]);
    }

    protected function render_content(): string {
        $cachekey = isset($this->param) ? 'tag:'.$this->param : null;
        return $this->cache->load(cachename: 'templates/sites', key: $cachekey, callback: function() {
            $sites = new \Jump\Sites(config: $this->config, cache: $this->cache);
            try {
                $taggedsites = $sites->get_sites_by_tag($this->param);
            }
            catch (TagNotFoundException) {
                (new ErrorPage($this->cache, $this->config, 404, 'There are no sites with this tag.'))->init();
            }
            $template = $this->mustache->loadTemplate('sites');
            return $template->render([
                'hassites' => !empty($taggedsites),
                'sites' => $taggedsites,
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
