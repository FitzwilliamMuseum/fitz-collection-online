# Fitzwilliam Museum Collection front end

[![DOI](https://zenodo.org/badge/276916733.svg)](https://zenodo.org/badge/latestdoi/276916733)  
[![ORCiD](https://img.shields.io/badge/ORCiD-0000--0002--0246--2335-green.svg)](http://orcid.org/0000-0002-0246-2335)

![Social card for this repo](https://repository-images.githubusercontent.com/276916733/496041fd-bd33-4261-a390-a628689ed5b6)

This repository contains the code base for a Laravel based front end for the production version of 
the Fitzwilliam Museum collection application. 

This is very much a minimum viable product and to run and install
your environment would need to be whitelisted for our solr, axiell, directus, shopify 
and elastic api instances. 

This system is built with the latest versions of Bootstrap and Laravel and uses webpack/npm.

# Requirements

PHP 8.1
Node 18.17.0

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

## License

This project's code is licensed under GPL V3

## Contributors

Daniel Pett @portableant

## Cite this repository 

### APA style

Pett, D. The Fitzwilliam Museum Collections and API Website Framework [Computer software]. https://github.com/FitzwilliamMuseum/fitz-collection-online

### Bibtex style 

@software{Pett_The_Fitzwilliam_Museum,
author = {Pett, Daniel},
license = {AGPL-3.0},
title = {{The Fitzwilliam Museum Collections and API Website Framework}},
url = {https://github.com/FitzwilliamMuseum/fitz-collection-online}
}
