# Fitzwilliam Museum Collection front end

[![DOI](https://zenodo.org/badge/276916733.svg)](https://zenodo.org/badge/latestdoi/276916733)


This repository contains the code base for a Laravel based front end for the production version of 
the Fitzwilliam Museum collection application. 

This is very much a minimum viable product and to run and install
your environment would need to be whitelisted for our solr, axiell, shopify and elastic api instances. 

This system is built with the latest versions of Bootstrap, Laravel 8, JQuery and uses webpack/npm/ 

# Installation

1. Install php on your development environment
2. Install composer
3. Install Node and NPM
4. Then run:

```
$ git clone https://github.com/FitzwilliamMuseum/fitz-collection-online
$ cd fitz-collection-online
$ composer install
$ npm install
$ npm run production
$ cp .env.example .env
```

You should now have all the source code installed, and you will then need to edit the .env file to hold 
all the values required. If you work for the Fitz, you will need to grab this from our Bitwarden vault. 

Once you are set up, to run locally:

```
$ php artisan serve
```

# License

GPL V3

# Contributors

Daniel Pett @portableant
