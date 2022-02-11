
# Jump
![GitHub release (latest by date)](https://img.shields.io/github/v/release/daledavies/jump)
![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue?style=flat)
![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/daledavies/jump?sort=date)

Jump is yet another self-hosted startpage for your server designed to be simple, stylish, fast and secure.

![screenshot](screenshot.png)

### Features
- Fast, easy to deploy, secure
- Custom sites and icons
- Fetch favicon for sites without supplied icon
- Custom bacground images
- Open Weather Map integration


## Installation

### Docker

Get the container image from Docker Hub (https://hub.docker.com/r/daledavies/jump).

The following will start Jump and serve the page at http://localhost:8123 with a custom site name, Open Weather Map support, and volumes to map Jump's "backgrounds" and "sites" directories to local directories on your machine...

```yaml
version: '3'
services:
    web:
        image: daledavies/jump
        ports:
            - 8123:8080
        volumes:
            - ./backgrounds:/backgrounds
            - ./sites:/sites
        environment:
            SITENAME: 'Custom site name'
            OWMAPIKEY: '0a1b2c3d4e5f6a7b8c9d0a1b'
            LATLONG: '51.509865,-0.118092'

```

You can use the following optional environment variables to configure/customise Jump...

- `SITENAME` - Custom site name.
- `NOINDEX: 'true'` - Include a robots noindex meta tag in site header
- `CACHEBYPASS: 'true'` - Bypass all caches, useful for testing changes.
- `OWMAPIKEY` - An API key for Open Weather Map, LATLONG (below) must also be defined.
- `LATLONG` - A latitude and longitude for the default location (e.g. "51.509865,-0.118092").

#### Volume Mapping

You can map the "backgrounds" and "sites" directories as shown in the Docker Compose example above. Your host directories will be populated with Jump's default files when the container is next started unless the local directories already contain files, in which case the local files will be used by Jump instead.

### Without Docker

Clone this repository and copy everything within the `jumpapp` directory to your server, edit `config.php` accordingly.

Install dependencies via composer by running the following command within the web root...

```bash
composer install --no-dev
```

Make sure you have created a cache directory and given the web user permission to write to it, the cache directory should match your `config.php` entry for `cachedir`.

## Configuration

### Open Weather Map

You can configure Jump to get local time and weather updates by adding an Open Weather Map API key to `config.php` or passing the `OWPAPIKEY ` environment variable to the docker container (as described above).

You will also need to provide a default `LATLONG` string (e.g. "51.509865,-0.118092"), Jump will use this  until you press the location button and allow permission to get your location from the web browser.

### Sites

Edit the `/sites/sites.json` file to include your own sites on the startpage...

```json
[
    {
        "name": "Bitwarden",
        "url" : "https://bitwarden.example.com",
        "nofollow": true,
        "icon": "bitwarden.png"
    },
    {
        "name": "Gitea",
        "url" : "https://git.example.com"
    },
    {
        "name": "Nextcloud",
        "url" : "https://cloud.example.com",
        "nofollow": true
    },
    {
        "name": "Paperless",
        "url" : "https://paperless.example.com",
        "nofollow": true,
        "icon": "paperless.jpg"
    }
]
```

* `name` and `url` are mandatory.
* `nofollow` and `icon` are optional.

#### Icons

You can provide custom icons for your sites by placing them in the `/sites/icons/` directory and referencing the filename in `sites.json` using the `icon` option.

#### nofollow

Use the `nofollow` option to include `rel="nofollow"` on specific site links.

### Background Images

To use your own background images just copy them to the `/backgrounds/` directory, Jump will pick up on them automatically.

## Development

Patches, improvements and feature requests are welcomed although I want to avoid anything that requires a database, admin interface or user accounts.

For development you will need to install composer dependencies by running `composer install` from within the `jumpapp` directory.

Javascript is bundled using Webpack, so you will need to have installed Node.js. Then within the root project directory (the same level as webpack.config.js) you should run `npm install`.

Before starting development you can run `npm run dev`, this will watch for changes to files within the `/assets/js/src/`directory and bundle them on the fly. The javascript bundle (`index.bundle.js`) created in development mode will not be minified and will contain source maps for debugging.

You can test a production build using `npm run build`, this will bundle and minify the javascript source files without source maps.

Please do not commit javascript bundles, only commit the patched source files.
