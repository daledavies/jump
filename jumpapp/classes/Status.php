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

class Status {

    private const STATUS_UNKNOWN = 'unknown';
    private const STATUS_ONLINE = 'online';
    private const STATUS_OFFLINE = 'offline';
    private const STATUS_ERROR = 'error';

    private $connectionTimeout = 10;
    private $requestTimeout = 30;

    /**
     * Allows for checking if a site is online/offline or returns an error code.
     *
     * @param Cache $cache
     * @param Site $site
     */
    public function __construct(private Cache $cache, public Site $site) {
        $this->status = $this->cache->load(cachename: 'sites/status', key: $this->site->id);
    }

    /**
     * Get the site's status.
     *
     * @return string The site status.
     */
    public function get_status(): string {
        // If we haven't got a status already cachhed then try connecting to the site
        // and save the status to the cache.
        if (!$this->status) {
            // Create a new client with client config.
            $client = new \GuzzleHttp\Client([
                'connect_timeout' => $this->connectionTimeout,
                'timeout' => $this->requestTimeout,
                'allow_redirects' => true
            ]);
            // Try to connect to site and determine status.
            try {
                if ($client->request('HEAD', $this->site->url)) {
                    $status = self::STATUS_ONLINE;
                }
            } catch (\GuzzleHttp\Exception\ConnectException) {
                // Catch instances where we cant connect.
                $status =  self::STATUS_OFFLINE;
            } catch (\GuzzleHttp\Exception\BadResponseException) {
                // Catch 4xx and 5xx errors.
                $status =  self::STATUS_ERROR;
            } catch (\Exception) {
                // If anything went wrong or we had some other status code.
                $status =  self::STATUS_UNKNOWN;
            }
            // Save the status to the cache.
            $this->status = $this->cache->save(cachename: 'sites/status', key: $this->site->id, data: $status);
        }
        // Finally return the status.
        return $this->status;
    }
}
