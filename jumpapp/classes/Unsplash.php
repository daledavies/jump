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

class Unsplash {

    public static function load_cache_unsplash_data($config) {
        \Crew\Unsplash\HttpClient::init([
            'utmSource' => 'jump_startpage',
            'applicationId'	=> $config->get('unsplashapikey'),
        ]);
        // Try to get a random image via the API.
        try {
            $photo = \Crew\Unsplash\Photo::random([
                'collections' => $config->get('unsplashcollections', false),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            die(json_encode(['error' => json_decode($e->getMessage())]));
        }
        // Download the image data from Unsplash.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $photo->urls['raw'].'&auto=compress&w=1920');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $response = curl_exec($ch);
        // Create the response and return it.
        $description = 'Photo';
        if ($photo->description !== null &&
            strlen($photo->description) <= 45) {
            $description = $photo->description;
        }
        $unsplashdata = new \stdClass();
        $unsplashdata->color = $photo->color;
        $unsplashdata->attribution = '<a target="_blank" rel="noopener" href="'.$photo->links['html'].'">'.$description.' by '.$photo->user['name'].'</a>';
        $unsplashdata->imagedatauri = 'data: '.(new \finfo(FILEINFO_MIME_TYPE))->buffer($response).';base64,'.base64_encode($response);
        return $unsplashdata;
    }
}
