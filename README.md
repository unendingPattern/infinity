infinity
========================================================

## This is a continuation of deprecated infinity software, made fit for the 8ch.pl service. If you want to start your own imageboard and you don't need user-board creation, use vichan.

About
------------
infinity is a fork of vichan, with the difference that infinity is geared towards allowing users to create their own boards. A running instance is at [8ch.net](https://8ch.net/) (new! a user of the software wrote to me that they created a Polish version: [8ch.pl](http://8ch.pl/))

Most things (other than installation) that apply to upstream vichan also apply to infinity. See their readme for a detailed FAQ: https://github.com/vichan-devel/vichan/blob/master/README.md

If you are not interested in letting your users make their own boards, install vichan instead of infinity.

**Much like Arch Linux, infinity should be considered ``rolling release''. Unlike upstream vichan, we have no install.php. Database schema and templates are changed often and it is on you to read the Git log before updating!**

Installation
------------
Basic requirements:
A computer running a Unix or Unix-like OS(infinity has been specifically tested with and is known to work under Ubuntu 14.x and Debian 9), Nginx or Apache, MySQL, and PHP 5.6 (tested) or 7.1 (untested)

**NOTE: Extension 'mcrypt' is deprecated since PHP 7.1 and removed since PHP 7.2! This will break captcha as it requires php-mcrypt to work!**

**NOTE: PHP 5.6 AND 7.1 ARE OFFICIALLY END OF LIFE! https://www.php.net/supported-versions.php**

**The captcha system would need to be rewritten to use openssl or pecl/mcrypt to support PHP versions past 7.1!**

* Make sure your web server (apache or nginx) has read/write access to the directory infinity resides in. `chown -R www-data:www-data /path/to/infinity`
* `install.php` is not maintained. Don't use it.
* As of February 22, 2015, you need the [DirectIO module (dio.so)](http://php.net/manual/en/ref.dio.php). This is for compatibility with NFS. 

Step 1. Create infinity's database from the included install.sql file. Enter mysql and create an empty database named 'infinity':

`mysql -u root -p`
```
CREATE DATABASE infinity;
CREATE USER 'myuser'@'localhost' IDENTIFIED BY 'mypassword';
GRANT ALL PRIVILEGES ON infinity.* TO 'myuser'@'localhost';
```

Then cd into the infinity base directory and run:
```
mysql -uroot -p infinity < install.sql
echo '+ <a href="https://github.com/unendingPattern/infinity">infinity</a> '`git rev-parse HEAD|head -c 10` > .installed
```
**Local captcha provider won't work unless you run this:**
```
mysql -uroot -p infinity < 8chan-captcha/dbschema.sql
```
*This fixes `You seem to have mistyped the verification`*!

Step 2. /inc/secrets.php does not exist by default, but infinity needs it in order to function. To fix this, cd into /inc/ and run:
```
sudo cp secrets.example.php secrets.php
```

Now open secrets.php and edit the $config['db'] settings to point to the 'infinity' MySQL database you created in Step 1. 'user' and 'password' refer to your MySQL login credentials.  It should look something like this when you're finished:

```
	$config['db']['server'] = 'localhost';
	$config['db']['database'] = 'infinity';
	$config['db']['prefix'] = '';
	$config['db']['user'] = 'root';
	$config['db']['password'] = 'password';
	$config['timezone'] = 'UTC';
	$config['cache']['enabled'] = 'apc';
```

Step 3.(Optional) By default, infinity will ignore any changes you make to the template files until you log into mod.php, go to Rebuild, and select Flush Cache. You may find this inconvenient. To make infinity automatically accept your changes to the template files, set $config['twig_cache'].

Step 4. Infinity can function in a *very* barebones fashion after the first two steps, but you should probably install these additional packages if you want to seriously run it and/or contribute to it. ffmpeg may fail to install under certain versions of ubuntu. If it does, remove it from this script and install it via an alternate method. Make sure to run the below as root:

```
## Run these commands manually to make sure everything is being installed as intended!

apt-get install gnupg wget sudo ca-certificates apt-transport-https python-software-properties
# apt-get remove php* # remove your current php version, anything past 7.1 won't work with captcha

# Ubuntu: add repositories for nginx, ffmpeg and alternative php versions
add-apt-repository ppa:nginx/stable
add-apt-repository ppa:jon-severinsson/ffmpeg
add-apt-repository ppa:ondrej/php

# Debian 9 (stretch): add repositories for alternative php versions
wget -q https://packages.sury.org/php/apt.gpg -O- | sudo apt-key add -
echo "deb https://packages.sury.org/php/ stretch main" | sudo tee /etc/apt/sources.list.d/php.list

apt-get update

# install php5.6 (recommended) OR php7.1 (untested)
# apt-get install php5.6-fpm php5.6-mysql php5.6-cli php5.6-apcu php5.6-dev php5.6-mcrypt php5.6-gd php-pear
# apt-get install php7.1-fpm php7.1-mysql php7.1-cli php7.1-apcu php7.1-dev php7.1-mcrypt php7.1-gd php-pear

apt-get install graphicsmagick gifsicle imagemagick
apt-get install mariadb mariadb-client

pear install Net_DNS2
pecl install "channel://pecl.php.net/dio-0.0.7

php --version # verify that your installed version is being used

# install ffmpeg
 apt-get install ffmpeg

# install apache OR nginx (per your own preference)
# apt-get install apache2
# apt-get install nginx
```

Step 5. Configure custom captcha provider and provider URLs in `inc/config.php` (line 320 and onward)
```
$config['captcha']['enabled'] = true;
$config['captcha']['provider_get']   = 'http://my.board.address/8chan-captcha/entrypoint.php';
$config['captcha']['provider_check'] = 'http://my.board.address/8chan-captcha/entrypoint.php';
```

Step 6. If using nginx enable php for your site by editing /etc/nginx/sites-enabled/default (or whatever file your site is defined in) and inserting the following line into your server section:
```
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php5.6-fpm.sock; # or php7.1-fpm.sock if using php7.1
        }
```

Page Generation
------------
A lot of the static pages (claim.html, boards.html, index.html) need to be regenerated every so often. You can do this with a crontab.

```cron
*/10 * * * * cd /path/to/infinity; /usr/bin/php /path/to/infinity/boards.php
*/5 * * * * cd /path/to/infinity; /usr/bin/php /path/to/infinity/claim.php
*/20 * * * * cd /path/to/infinity; /usr/bin/php -r 'include "inc/functions.php"; rebuildThemes("bans");'
```

Also, main.js is empty by default. Run tools/rebuild.php to create it every time you update one of the JS files.

Good luck!
