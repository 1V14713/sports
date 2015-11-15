Readme phpMyGPX
===============

Homepage: http://phpmygpx.tuxfamily.org/
(C) 2009-2012 Sebastian Klemm (osm@erlkoenigkabale.eu)


What's this?
------------
phpMyGPX is an application to manage your GPX files and photos locally or online.
GPX is a xml file format containing track points and routes collected by GPS
receivers and beeing very popular with navigation, geo caching and OpenStreetMap,
of course.

Icons are taken from the open source applications phpMyAdmin (phpmyadmin.net)
and Joomla (joomla.org), which both are GNU/GPL licensed, but if you
want to create better ones you can just contribute them.


Requirements
------------
- LAMP environment or equivalent, it could be your local (offline) computer with:
- a web server, e.g. Apache2 from httpd.apache.org
- PHP 5 (with DOM, GD2 and MySQL extensions enabled) from www.php.net
  * optional: PHP Exif (and mbstring on windows hosts) extensions for photo features
  * optional: PHP cURL extension for the tile cache proxy
- MySQL 4.1 or 5 from www.mysql.com (it should work with 3.2x, too, but not tested)
- and a web browser, of course, e.g. Mozilla Firefox


Configuration
-------------
As of version 0.5 there's no need to manually edit the config file because of 
an installation/upgrade wizard. Nevertheless you might want to tweak some 
features of the software, this can be easily done:
All configuration is held in "config.inc.php" file. Just change the variables
to suit your needs.

If you want to use phpMyGPX on a public web server, you should set
$cfg['public_host'] = TRUE and $cfg['admin_password'] = 'yoursecret'
This prevents anonymous visitors from changing anything in data base e.g.
adding and deleting trackpoints but allows them to view your data. To do so
just login with your admin password.


Installation
------------
Extract the downloaded archive and copy it to a directory on your web server.

Then point your prefered web browser to the newly created directory, for example
http://your-server/phpmygpx/
You will be guided thru the installation process. After the data base is created,
you can use the application and start with uploading gpx files.


Upgrading
---------
In general, you should backup your config.inc.php file and your GPX files
(in 'files' folder). After that, just extract the archive containing the upgrade
to your current phpMyGPX folder.
Then, point your browser to http://your-server/phpmygpx/installation/
and check the "upgrade" installation mode. This makes sure that possible changes to
the data base layout can be done, although your existing data tables will be kept.
Even the config file will be updated by the upgrade wizard, if neccessary.


Languages
---------
At the moment, these languages are available:
- English
- German
- Dutch (thanks to Leon Vrancken)
- French (thanks to Arno Renevier)
- Spanish (thanks to Andrés Gómez Casanova)

You can choose your prefered frontend language by setting the 
"$cfg['config_language']" variable in the "config.inc.php" file.
Just have a look at the "/languages" directory to find out which languages are
provided and take the name of the file without the ".php" extension.

Adding more languages is quite easy:
Copy a file with a language you know in the "/languages" directory rename it to
your new language and translate all defined constants. Then you can change the
"$cfg['config_language']" variable to your newly created language. That's it!

Would be great to receive your improved or new language files by email, thanks!


License
-------
Please see the LICENSE file for details.
