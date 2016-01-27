# phpBB Gallery

[![Build Status](https://travis-ci.org/satanasov/phpbbgallery.svg?branch=master)](https://travis-ci.org/satanasov/phpbbgallery) [![Coverage Status](https://coveralls.io/repos/satanasov/phpbbgallery/badge.svg?branch=master&service=github)](https://coveralls.io/github/satanasov/phpbbgallery?branch=master)

© 2012 - [nickvergessen](http://www.flying-bits.org)

© 2014 - 2016 - [Lucifer](http://www.anavaro.com)

Welcome to phpBB Gallery version 1.2

This is direct port of [nickvergessen](https://github.com/nickvergessen)'s phpBB Gallery 1.1.6 for phpBB 3.0. As he has no time to support it or to port it, I have taken this on my self. The phpBB Gallery is very good project and I use it heavily. This is why I can't leave it and just wait for someone to port it or to create alternative.

This project will include only base core code and functioning add-ons which will expand this code.

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

## Future?

As I said the 1.2 version will be primarly targeted as backword compatible with phpBB Gallery 1.1.6 MOD (DB will be the same but you will have to manualy move your images to the new location).

1.2.1 will be where the code clean up, new features and core separation will occure. I will strive to keep everything backword compatible.

## Installation

This ext installs as evry other phpBB extension:

 - Clone the repo to ext folder OR download the zip file and copy phpbbgallery to your ext folder
 - Go to ACP -> Customise -> Manage extensions
 - Activate phpBB Gallery
 - (optional) Activate needed extensions.

## Upgrading from 1.1.x

This is task that could not be automated. You will have to do some work. Just follow [this guide](lab.anavaro.com/forum/viewtopic.php?f=4&t=4).

Regards,
Stanislav Atanasov

P.S.: You can get more support here - [Forum](http://lab.anavaro.com/forum/viewforum.php?f=4)

P.P.S: If you like this extension - you could alway donate:

[![alt text](http://lab.anavaro.com/forum/images/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3JQ8HDK6Y7A2N)

![alt text](http://www.xe.com/themes/xe/images/symbols/xbt.gif) 12afvCTp1dRrQdHaavzthZ1dNWMoi8eyc8