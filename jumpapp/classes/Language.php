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

namespace Jump;

use \Jump\Exceptions\ConfigException;
use \Jump\Pages\ErrorPage;

/**
 * Defines a class for loading language strings form available translations files, caching
 * and fetching of language strings etc. Will fetch the appropriate strings based on the
 * language code defined in config.php.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */
class Language {

    private \Utopia\Locale\Locale $locale;

    /**
     * Automatically loads available language strings on instantiation, either from the
     * cache or from available files in the translations dir.
     *
     * @param Config $config
     * @param Cache $cache
     */
    public function __construct(private Config $config, private Cache $cache) {
        // Try to load the translations from cache.
        $languages = $this->cache->load(cachename: 'languages');
        // If they are not there or the cache has expired, then find all language files, load them up
        // again and cache them.
        if ($languages == null) {
            $languages = [];
            // Enumerate translation files and load their content.
            $languagefiles = glob($this->config->get('translationsdir').'/*.json');
            foreach ($languagefiles as $file) {
                $rawjson = file_get_contents($file);
                if ($rawjson === false) {
                    throw new ConfigException('There was a problem loading a translation file... ' . $file);
                }
                if ($rawjson === '') {
                    throw new ConfigException('The following translation file is empty... ' . $file);
                }
                $languages[pathinfo($file, PATHINFO_FILENAME)] = json_decode($rawjson, true);
            }
            // Save the content of translation files into the cache.
            $this->cache->save(cachename: 'languages', data: $languages);
        }
        // For each translation file that has been loaded, set them as available locales.
        foreach ($languages as $name => $strings) {
            \Utopia\Locale\Locale::setLanguageFromArray($name, $strings);
        }
        // Initialise the locale defined in the config.php language setting.
        try {
            $locale = new \Utopia\Locale\Locale($this->config->get('language'));
        } catch (\Exception) {
            ErrorPage::display($this->config, 500, 'Provided language code has no corresponding translation file.');
        }

        $this->locale = $locale;
    }

    /**
     * Retrieve a language string for the given key, substituting and placeholders
     * that are provided.
     *
     * @param string $string
     * @param array $placeholders
     * @return mixed
     */
    public function get(string $string, array $placeholders = []): mixed {
        return $this->locale->getText($string, $placeholders);
    }
}
