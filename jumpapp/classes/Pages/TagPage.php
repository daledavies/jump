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

use \Jump\Exceptions\TagNotFoundException;

class TagPage extends AbstractPage {

    protected function render_header(): string {
        $template = $this->mustache->loadTemplate('header');
        $this->tagname = $this->routeparams['tag'];
        $greeting = '#'.$this->tagname;
        $title = 'Tag: '.$this->tagname;
        $csrfsection = $this->session->getSection('csrf');
        $unsplashdata = $this->cache->load('unsplash');
        $showsearch = $this->config->parse_bool($this->config->get('showsearch', false));
        $checkstatus = $this->config->parse_bool($this->config->get('checkstatus', false));
        $templatecontext = [
            'csrftoken' => $csrfsection->get('token'),
            'greeting' => $greeting,
            'noindex' => $this->config->parse_bool($this->config->get('noindex')),
            'title' => $title,
            'owmapikey' => !!$this->config->get('owmapikey', false),
            'metrictemp' => $this->config->parse_bool($this->config->get('metrictemp')),
            'ampmclock' => $this->config->parse_bool($this->config->get('ampmclock', false)),
            'unsplash' => !!$this->config->get('unsplashapikey', false),
            'unsplashcolor' => $unsplashdata?->color,
            'wwwurl' => $this->config->get_wwwurl(),
            'checkstatus' => $checkstatus,
        ];
        $stringsforjs = \Jump\Status::get_strings_for_js($this->language);
        $stringsforjs['greetings']['goodmorning'] = $this->language->get('greetings.goodmorning');
        $stringsforjs['greetings']['goodafternoon'] = $this->language->get('greetings.goodafternoon');
        $stringsforjs['greetings']['goodevening'] = $this->language->get('greetings.goodevening');
        $stringsforjs['greetings']['goodnight'] = $this->language->get('greetings.goodnight');
        if ($showsearch || $checkstatus) {
            $templatecontext['sitesjson'] = json_encode((new \Jump\Sites($this->config, $this->cache))->get_sites_for_frontend());
            if ($showsearch) {
                $searchengines = new \Jump\SearchEngines($this->config, $this->cache, $this->language);
                $templatecontext['searchengines'] = json_encode($searchengines->get_search_engines());
                $stringsforjs += $searchengines->get_strings_for_js();
            }
        }
        $templatecontext['stringsforjs'] = json_encode($stringsforjs);
        return $template->render($templatecontext);
    }

    protected function render_content(): string {
        $cachekey = isset($this->tagname) ? 'tag:'.$this->tagname : null;
        return $this->cache->load(cachename: 'templates/sites', key: $cachekey, callback: function() {
            $sites = new \Jump\Sites(config: $this->config, cache: $this->cache);
            try {
                $taggedsites = $sites->get_sites_by_tag($this->tagname);
            }
            catch (TagNotFoundException) {
                ErrorPage::display($this->config, 404, 'There are no sites with this tag.');
            }
            $template = $this->mustache->loadTemplate('sites');
            return $template->render([
                'hassites' => !empty($taggedsites),
                'sites' => $taggedsites,
                'altlayout' => $this->config->parse_bool($this->config->get('altlayout', false)),
                'wwwurl' => $this->config->get_wwwurl(),
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
                'wwwurl' => $this->config->get_wwwurl(),
                'unsplash' => !!$this->config->get('unsplashapikey', false),
            ]);
        });
    }

}
