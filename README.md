
# Jump

Jump is yet another self-hosted startpage for your server designed to be simple, stylish, fast and secure.

![screenshot](screenshot.png)


## Installation

### Docker

You can pull the [daledavies/jump](https://hub.docker.com/r/daledavies/jump) image direct from Docker Hub and use the Docker CLI command, but my personal preference is Docker Compose.

The following will start Jump and serve the page at http://localhost:8123 with a custom site name, mapping the backgrounds and sites directory locally...

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

You can use the following environment variables to configure/customise Jump...

- `SITENAME` - Custom site name.
- `NOINDEX` - Include a robots noindex meta tag in site header.
- `CACHEBYPASS` - Bypass all caches, useful for testing changes.
- `OWMAPIKEY` - An API key for Open Weather Map, LATLONG (below) must also be defined.
- `LATLONG` - A latitude and longitude for the default location (e.g. "51.509865,-0.118092").

#### Volume Mapping

You can map the "backgrounds" and "sites" directories as shown in the Docker Compose example above. After configuring volumes, the directories mapped on your host will be populated with Jump's default files when the container is next started. If the local directories contain files then they will persist going forward.

### Without Docker

Clone this git repo and copy everything within the `jumpapp` directory to your server, edit `config.php` accordingly.

Then from within the web root directory on your server, install dependencies via composer...

```bash
composer install --no-dev
```

Make sure you have created a cache directory and given the web user permission to read and write, the cache directory should match your `config.php` entry for `cachedir`.

## Configuration

### Open Weather Map

You can configure Jump to get local time and weather updates by adding an Open Weather Map API key to `config.php` or passing the `OWPAPIKEY ` environment variable to the docker container (as described above).

You will also need to provide a default `LATLONG` string, Jump will use this (e.g. "51.509865,-0.118092") until you press the location button and allow permission to get your location via the web browser. 

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

Although `name` and `url` are mandatory, you do not need to provide `nofollow` and `icon`.

#### Icons

You can provide custom icons for your sites by placing them in the `/sites/icons/` directory and referencing the filename in `sites.json` using the `icon` option.

#### nofollow

Use the `nofollow` option to include `rel="nofollow"` on specific site links.

### Background Images

To use your own background images just copy them to the `/backgrounds/` directory, Jump will pick up on them automatically.
