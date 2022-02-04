
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
    
