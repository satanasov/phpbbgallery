#phpBB Gallery

© 2012 - [nickvergessen](http://www.flying-bits.org)

With phpBB 3.1 MODs will mostly be replaced with Extensions. The main difference is, that extensions are designed (by code) in a different way and won't require code edits. So I started to port the Gallery MOD to a Gallery Extension. While refactoring the code, I also split it into several Extensions.

The aim behind this splitting is, to make it easily possible to disable some features and also be able to remove their code completly from the forum.

So which Extensions will the Gallery include?

© 2014 - [lucifer4o] (http://www.anavaro.com)

This extension is forked from https://github.com/phpbbgallery/phpbb-gallery

As [nickvergessen] abandoned it I will try my best to migrate it to PhpBB 3.1 and to keep working on it.

This is one really great peace of code and I am still in the process of understanding and migrating it. Please be patiant. 

For the moment small parts of the Core is migrated.

##Extensions

###Already implemented

* ACP CleanUp
* ACP Import
* Exif Data
* Favorite
* Feed
    * todo: _Add links back to overall_header.html when the template event is commited._

###Planned

* Comment
* Rating
* Contest:
    * Rating required
    * Comment required
* Search
* Notification (Mail)
* Report
* HighslideJS/Lytebox/Shadowbox/etc.

###Ideas

The following ideas are not part of the 1.1 code

* Custom image fields
* Features image

##Support/Testing/Installing

The current code for 1.2-dev from this github branch should **NOT** be installed in a live board.

Additional **NO SUPPORT** will be given, until a final version of 1.2 is released!

