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

Also possible is to clone this base application, write your own api in a subfolder of application/controllers and write a route towards it in the application/config/routes.php config file. From there you can load your drivers and authenticate them to do some api calls to the connected services.

Dependencies
============
PHP 5.3+
--------
Minumum required php version installed is 5.3 . The .htaccess file is for apache2 webserver. The sql dump file is for mySQL but can be adapted for other RDBMS's.

Copyright and license
=====================

© 2012 - iRail vzw/asbl

AGPLv3