PlugID
=========
PlugID is an [OAuth2](http://oauth.net/2/)(Draft 28 at time of writing) server written in PHP, based on [CodeIgniter](http://codeigniter.com/) 2.1.2.
Writing started on the 2nd of July, 2012, during the [iRail summer of code](http://hello.irail.be/irail-summer-of-code/)[(#iSoc12)](https://twitter.com/search/realtime/iSoc12).
The original team of coders is:

* [Hannes Van De Vreken](http://twitter.com/hannesvdvreken)(project manager)
* [Lennart Martens](http://twitter.com/lennartmart)
* [Koen De Groote](http://twitter.com/koen027)
* [Jens Segers](http://twitter.com/jenssegers)

With PlugID, users can register on your service using their accounts on various social media sites. Currently supported are Vikingspots, Foursquare, Facebook, Twitter and Google+.
Once the user gives permission, your webservice can work with the connected account(s).

PlugID can be run on any server that runs PHP 5.3 or higher.

Dependencies
============

OAuth library for php
---------------------

This is the OAuth1.0a library for PHP, which is to be installed when using OAuth1.0a service providers. More documentation can be found at: http://php.net/manual/en/book.oauth.php

How to install in Linux:
```
$ sudo apt-get install php-pear php5-dev make libpcre3-dev
$ sudo pecl install oauth
```

All there is to it.

How to install in Windows:


Copyright and license
=====================
To be added