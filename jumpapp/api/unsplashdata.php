<?php
/**
 * Proxy requests to Unsplash API and cache response.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

// Provided by composer for psr-4 style autoloading.
require __DIR__ .'/../vendor/autoload.php';

$config = new Jump\Config();
$cache = new Jump\Cache($config);

// If this script is run via CLI then clear the cache and repopulate it,
// otherwise if run via web then get image data from cache and run this
// script asynchronously to refresh the cahce for next time.
if (http_response_code() === false) {
    $cache->clear('unsplash');
    load_cache_unsplash_data();
    die('Cached data from Unsplash');
}

// Output header here so we can return early with a json response if there is a curl error.
header('Content-Type: application/json; charset=utf-8');

// Initialise a new session using the request object.
$session = new Nette\Http\Session((new Nette\Http\RequestFactory)->fromGlobals(), new Nette\Http\Response);
$session->setName($config->get('sessionname'));
$session->setExpiration($config->get('sessiontimeout'));

// Get a Nette session section for CSRF data.
$csrfsection = $session->getSection('csrf');

// Has a CSRF token been set up for the session yet?
if (!$csrfsection->offsetExists('token')){
   http_response_code(401);
   die(json_encode(['error' => 'Session not fully set up']));
}

// Check CSRF token saved in session against token provided via request.
$token = isset($_GET['token']) ? $_GET['token'] : false;
if (!$token || !hash_equals($csrfsection->get('token'), $token)) {
   http_response_code(401);
   die(json_encode(['error' => 'API token is incorrect or missing']));
}

$unsplashdata = load_cache_unsplash_data();
echo json_encode($unsplashdata);

shell_exec('/usr/bin/nohup /usr/bin/php -f unsplashdata.php >/dev/null 2>&1 &');

function load_cache_unsplash_data() {
    global $cache, $config;
    return $cache->load(cachename: 'unsplash', callback: function() use ($config) {
        Crew\Unsplash\HttpClient::init([
            'utmSource' => 'jump_startpage',
            'applicationId'	=> $config->get('unsplashapikey'),
        ]);
        // Try to get a random image via the API.
        try {
            $photo = Crew\Unsplash\Photo::random([
                'collections' => $config->get('unsplashcollections', false),
            ]);
        } catch (Exception $e) {
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
        $unsplashdata = new stdClass();
        $unsplashdata->color = $photo->color;
        $unsplashdata->attribution = '<a target="_blank" rel="noopener" href="'.$photo->links['html'].'">'.$description.' by '.$photo->user['name'].'</a>';
        $unsplashdata->imagedatauri = 'data: '.(new finfo(FILEINFO_MIME_TYPE))->buffer($response).';base64,'.base64_encode($response);
        return $unsplashdata;
    });
}
