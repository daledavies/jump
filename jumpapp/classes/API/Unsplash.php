<?php

namespace Jump\API;

class Unsplash extends AbstractAPI {

    public function get_output(): string {

        $this->validate_token();

        $unsplashdata = $this->cache->load(cachename: 'unsplash');

        if ($unsplashdata == null) {
            $unsplashdata = \Jump\Unsplash::load_cache_unsplash_data($this->config);
            $this->cache->save(cachename: 'unsplash', data: $unsplashdata);
        }

        $toexec = '/usr/bin/nohup /usr/bin/php -f ' . $this->config->get('wwwroot') . '/cli/cacheunsplash.php >/dev/null 2>&1 &';
        shell_exec($toexec);

        return json_encode($unsplashdata);
    }
}
