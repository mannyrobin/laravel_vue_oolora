# SaaSCore
This package contains Laravel boilerplate code that allows for rapid development of SaaS Applications

*Laravel Version: 5.7*


- - - -


## Installation New Project

Clone the skeleton project from the repo 
``` bash
git clone git@bitbucket.org:helpcommerce/projectdev.git
```


Add SaaSCore under the *require* section of the root application composer.json file and also the repository url
 
``` bash
"DanTheCoder/SaaSCore": "^1.0",
```

``` bash
    "repositories": [
        {
            "type": "git",
            "url": "git@bitbucket.org:helpcommerce/saascore.git"
        }
    ],
```



Install all dependencies
``` bash
composer install
npm install
```

Create a copy of your .env file
``` bash
cp .env.example .env
```


Generate an app encryption key
``` bash
php artisan key:generate
```


- - - -


### Updating and Publishing

Configuration files
``` bash
php artisan vendor:publish --tag=saascore.config --force
```

**NB:** See ProjectDev repo for all other files that were changed that cannot be published due to override issues.


- - - -


### Database Migration
Run all outstanding migrations by executing the migrate Artisan command.

``` bash
php artisan migrate
```


- - - -

### Forked Code

Subscription Module
https://github.com/gerardojbaez/laraplans
v2.2.0



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.