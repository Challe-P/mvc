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

The project will be updated regularly, come back to see more changes!

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/Challe-P/mvc/badges/build.png?b=main)](https://scrutinizer-ci.com/g/Challe-P/mvc/build-status/main)
