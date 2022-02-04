<?php

namespace Jump;

class Background {

    private string $backgroundsdirectory;
    private array $backgroundfiles;

    public function __construct(Config $config) {
        $this->config = $config;
        $this->backgroundsdirectory = $config->get('backgroundsdir');
        $this->webaccessibledir = str_replace($config->get('wwwroot'), '', $config->get('backgroundsdir'));
        $this->enumerate_files();
    }

    private function enumerate_files(): void {
        $this->backgroundfiles = array_diff(scandir($this->backgroundsdirectory), array('..', '.'));
    }

    public function get_random_background_file(bool $includepath = true): string {
        return ($includepath ? $this->webaccessibledir : '')
            . '/'. $this->backgroundfiles[array_rand($this->backgroundfiles)];
    }

}
