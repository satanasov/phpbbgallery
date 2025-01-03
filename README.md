# phpBB Gallery

[![Tests](https://github.com/satanasov/phpbbgallery/actions/workflows/tests.yml/badge.svg)](https://github.com/satanasov/phpbbgallery/actions/workflows/tests.yml) [![Code Coverage](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/?branch=master)

© 2012 - [nickvergessen](http://www.flying-bits.org)

© 2014 - 2022 - [Lucifer](http://www.anavaro.com)

Welcome to phpBB Gallery version 3.3.x

This is direct port of [nickvergessen](https://github.com/nickvergessen)'s phpBB Gallery 1.1.6 for phpBB 3.0. As he has no time to support it or to port it, I have taken this on my self. The phpBB Gallery is very good project and I use it heavily. This is why I can't leave it and just wait for someone to port it or to create alternative.

This project will include only base core code and functioning add-ons which will expand this code.

##Donate
If you like my code - buy me a beer/coffee/license for PhpStorm :D

- BTC: 12afvCTp1dRrQdHaavzthZ1dNWMoi8eyc8
- ETH: 0x363abc8edf41ac89906b20e90cf7fdc71fe78cd5

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XQ6USSXCSUM5W)

## Known Issues

MSSQL is not ok. I don't have a MSSQL instance to test different functions. If you want to use with MSSQL - we can debug and do stuff.

## Add ons
 - Exif
 - ACP Import
 - ACP Cleanup

### Exif

Allows saving and visualization of EXIF (Exchangeable image file format) image information. Basicly a bunch of stuff about the image - F-stop, ISO, appature, speed, usage of flash ... it could hold GPS coordinates ...

### ACP Import

Allows bulk import of images.

### ACP cleanup

Bunch of functions related to DB and file system maintenence. Allows you to purge albums, move pictures, clean orphaned images, albums and comments ...

## Adding a new feture?

If you can make me think of it as core function I will add it. Such function is going to be the contests, the plupload support and jQuery based visualization libraries.

If you can't - you can always PR a event and write the function yourself.

## Contributing

If you want to contribute to this project do it as you will any other project - clone the repo and make a PR against it. I will review your code. Your code should be documented and your PR message should describe the changes you've made.

## Development roadmap?

As I said the 1.2 version will be primarly targeted as backword compatible with phpBB Gallery 1.1.6 MOD (DB will be the same but you will have to manualy move your images to the new location).

3.2.x is providing phpBB 3.2 compatibility and some code optimization. When initial release is done I will start adding features.

Version 3.3.x will be compatible to phpBB 3.3 and will have new features.

## Installation

This ext installs as evry other phpBB extension:

 - Clone the repo to ext folder OR download the zip file and copy phpbbgallery to your ext folder
 - Go to ACP -> Customise -> Manage extensions
 - Activate phpBB Gallery
 - (optional) Activate needed extensions.

## Upgrading from 1.1.x

This is task that could not be automated. You will have to do some work. Just follow [this guide](https://www.phpbb.com/customise/db/extension/phpbb_gallery/faq/2181).

Regards,
Stanislav Atanasov

P.S.: You can get more support here - [Forum](http://lab.anavaro.com/forum/viewforum.php?f=4)

P.P.S: If you like this extension - you could alway donate:

[![alt text](http://lab.anavaro.com/forum/images/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3JQ8HDK6Y7A2N)

![alt text](http://www.xe.com/themes/xe/images/symbols/xbt.gif) 12afvCTp1dRrQdHaavzthZ1dNWMoi8eyc8
