<?php

namespace Jump;

use Exception;

class Config {

    private \PHLAK\Config\Config $config;

    /**
     * Required files and directories need that should not be configurable.
     */
    private const BASE_APPLICATION_PATHS = [
        'backgroundsdir' => '/assets/backgrounds',
        'sitesfile' => '/sites/sites.json',
        'templatedir' => '/templates',
    ];

    /**
     * Configurable params we do expect to find in config.php
     */
    private const CONFIG_PARAMS = [
        'sitename',
        'wwwroot',
        'cachebypass',
        'cachedir',
        'noindex',
    ];

    public function __construct() {
        $this->config = new \PHLAK\Config\Config(__DIR__.'/../config.php');
        $this->add_wwwroot_to_base_paths();
        if ($this->config_params_missing()) {
            throw new Exception('Config.php must always contain... '.implode(', ', self::CONFIG_PARAMS));
        }
    }

    /**
     * Prefixes the wwwroot string from config.php to the base application paths
     * so they can be located in the file system correctly.
     *
     * @return void
     */
    private function add_wwwroot_to_base_paths(): void {
        $wwwroot = $this->config->get('wwwroot');
        foreach(self::BASE_APPLICATION_PATHS as $key => $value) {
            $this->config->set($key, $wwwroot.$value);
        }
    }

    /**
     * Determine if any configuration params are missing in the list loaded
     * from the config.php.
     *
     * @return boolean
     */
    private function config_params_missing(): bool {
        return !!array_diff(
            array_keys($this->config->toArray()),
            array_merge(
                array_keys(self::BASE_APPLICATION_PATHS),
                self::CONFIG_PARAMS
            ));
    }

    /**
     * Retrieves the config parameter provided in $key, first checks for its
     * existence.
     *
     * @param string $key The config parameter required.
     * @return mixed The selected value from the configuration array.
     */
    public function get(string $key): mixed {
        if (!$this->config->has($key)) {
            throw new Exception('Config key does not exist... ('.$key.')');
        }
        return $this->config->get($key);
    }

    /**
     * Attempt to converts a string to a boolean correctly, will return the parsed boolean
     * or null on failure.
     *
     * @param mixed $input A string representing a boolean value... "true", "yes", "no", "false" etc.
     * @return mixed Returns a proper boolean or null on failure.
     */
    public function parse_bool(mixed $input): mixed {
        return filter_var($input,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
    }

}
