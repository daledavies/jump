<?php

namespace Jump;

/**
 * Return a random background image path selected from the list of files
 * found in the /assets/backgrounds directory.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Background {

    private string $backgroundsdirectory;
    private array $backgroundfiles;

    public function __construct(private Config $config) {
        $this->backgroundsdirectory = $config->get('backgroundsdir');
        $this->webaccessibledir = str_replace($config->get('wwwroot'), '', $config->get('backgroundsdir'));
        $this->enumerate_files();
    }

    /**
     * Enumerate a list of background filenames from backgrounds directory.
     *
     * @return void
     */
    private function enumerate_files(): void {
        $this->backgroundfiles = array_diff(scandir($this->backgroundsdirectory), array('..', '.'));
    }

    /**
     * Select a random file from the enumerated list in $this->backgroundfiles
     * and optionally prefix with a web accessible path for the backgrounds
     * directory.
     *
     * @param boolean $includepath Should the backgrounds directory path be prefixed?
     * @return string The selected background image filename/, optionally including path.
     */
    public function get_random_background_file(bool $includepath = true): string {
        return ($includepath ? $this->webaccessibledir : '')
            . '/'. $this->backgroundfiles[array_rand($this->backgroundfiles)];
    }

}
