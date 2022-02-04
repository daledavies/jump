
# Jump

Jump is yet another self-hosted startpage for your server, designed to be simple to set up and deploy.


## Installation

### Docker

You can pull the daledavies/jump image direct from Docker Hub and use docker cli to run but my personal preference is docker-compose.

The following will start Jump servinge on http://localhost:8123 with a custom site name, mapping the backgrounds and sites directory locally...

```yaml
version: '3'
services:
    web:
        image: daledavies/jump
        ports:
            - 8123:8080
        volumes:
            - ./backgrounds:/var/www/html/assets/backgrounds
            - ./sites:/var/www/html/sites
        environment:
            SITENAME: 'Custom site name'

```

You can use the following environment variables to customise configure Jump...

- `SITENAME` - Custom site name.
- `NOINDEX` - Include a robots noindex meta tag in site header.
- `CACHEBYPASS` - Bypass all caches, useful for testing changes.

### Without Docker

Clone the repo and copy everything within the `jumpapp` directory to your server, edit `config.php` accordingly.

Then from within the web root directory on your server, install dependencies via composer...

```bash
composer install --no-dev
```

Make sure you have created a cache directory and given the web user permission to read and write, the cache directory should match your `config.php` entry for `cachedir`.

## Configuration

### Sites

Edit the `/sites/sites.json` file to include your own services on the startpage...

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

Although `name` and `url` are mandatory, you do not need to provide `nofollow` and `icon`...

#### Icons

You can provide custom icons for your sites by placing them in the `/sites/icons/` directory and referencing the filename in `sites.json` using the `icon` option.

#### nofollow

Use the `nofollow` option to specify if site links should include `rel="nofollow"`.

### Background Images

To use your own background images just copy them to the `/assets/backgrounds/` directory.