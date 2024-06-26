# Welcome to Challe_Ps MVC repo

This repo contains a site built with symfony.
To install do the following steps:
1. Make sure you have composer, php and make installed. For the version numbers check the file: [composer.lock](symfony/app/composer.lock)
2. Download the repo
3. Run the following command in the core folder:
<pre><code>composer install</code></pre>

4. Run Asset Mapper to map the local assets into the public folder. With this command:
<pre><code>php bin/console asset-map:compile</code></pre>

5. If you change the CSS or want to add a picture, you'll need to run the Asset Mapper for the changes to take effect. 

To run the site locally, go to the app folder in a terminal and run the following command:

    php -S localhost:8888 -t public

Now you're up and running! Have fun!

## Further information
### ORM
The site has a [SQLite3](https://www.sqlite.org/) database, which it uses via an ORM called [Doctrine](https://www.doctrine-project.org/). The database can of course also be altered by use of pure SQL, if you want. There's also a backup, if something goes wrong. To create more tables, or add attributes to existing ones, there's a very good tutorial [here](https://github.com/dbwebb-se/mvc/tree/main/example/symfony-doctrine) by my teacher [Mos](https://github.com/mosbth).

### Code coverage
The sites code is 100% covered by tests, with the help of [PHP Unit](https://phpunit.de/index.html). If you want to see how and what the tests assert, look in the test files, in the folder named tests. To cover the routes I used PHPunits webtestcase and some mocking as well.
To run the tests run the command in the app directory:

    composer phpunit

### Linting
The code is heavily type hinted, and all the linters are set to the strictest level. 
A lot of if statements could be removed if it was a bit looser, but they help keep the code base
stable.

The linters used in the project are: 
* [PHPMD](https://phpmd.org/)
* [PHPStan](https://phpstan.org/)
* [PHP Coding Standars Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)

Run them all using the command:

    composer lint

### Static code analysis
The site has also undergone analysis by the static code analyser tool [PHP Metrics](https://phpmetrics.org/). To read the analysis, go to the docs/mterics-folder and open the index-file in a browser. To run a new analysis, use the command:

    composer phpmetrics

### Automatic documentation
The code is documented a bit, with some comments on the some of the classes and methods. To read the documentation, check out the docs/api-folder. The files there have been generated with the help of [phpDocumentor](https://www.phpdoc.org/).

To generate a new documentation use the command:

    composer phpdoc

### Scrutinizer
Lastly, the code is run through [Scrutinizer](https://scrutinizer-ci.com/) upon being pushed to github. Scrutinizer runs the unit tests, checks if the build works and analyses the code. Click one of the badges below to read the analysis from Scrutinizer.

Clickable badges from scrutinizer:

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/build.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/build-status/main)

My work on this project is done, so you won't need to come back here for more updates, but if you have any questions don't be afraid to get in touch!
