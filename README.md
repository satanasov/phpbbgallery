# phpBB Gallery

[![Tests](https://github.com/satanasov/phpbbgallery/actions/workflows/tests.yml/badge.svg)](https://github.com/satanasov/phpbbgallery/actions/workflows/tests.yml) [![Code Coverage](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/satanasov/phpbbgallery/?branch=master)

© 2008 - 2012 - [nickvergessen](https://web.archive.org/web/20131104154014/http://www.flying-bits.org/index.php)

© 2014 - 2025 - [Lucifer](https://www.anavaro.com)

© 2019 - 2025 - [Leinad4Mind](https://leinad4mind.top/forum)

Welcome to phpBB Gallery version 3.3.x

This is direct port of [nickvergessen](https://github.com/nickvergessen)'s phpBB Gallery 1.1.6 for phpBB 3.0. As he has no time to support it or to port it, I have taken this on my self. The phpBB Gallery is very good project and I use it heavily. This is why I can't leave it and just wait for someone to port it or to create alternative.

This project will include only base core code and functioning add-ons which will expand this code.

## Donate
If you like this extension or just like my code - buy me a beer/coffee/license for PhpStorm :D

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3JQ8HDK6Y7A2N)

- BTC: 12afvCTp1dRrQdHaavzthZ1dNWMoi8eyc8
- ETH: 0x363abc8edf41ac89906b20e90cf7fdc71fe78cd5

## Known Issues

MSSQL is not ok. I don't have a MSSQL instance to test different functions. If you want to use with MSSQL - we can debug and do stuff.

## Add ons
 - Exif
 - ACP Import
 - ACP Cleanup

### Exif

Allows saving and visualization of EXIF (Exchangeable image file format) image information. Basically a bunch of stuff about the image - F-stop, ISO, aperture, speed, usage of flash... it could hold GPS coordinates...

### ACP Import

Allows bulk import of images.

### ACP cleanup

Bunch of functions related to DB and file system maintenance. Allows you to purge albums, move pictures, clean orphaned images, albums and comments...


## Current features (grabbed from 1.1.6 - To be reviewed)

### Configurations/Misc:
 - Filetypes: gif, jpg, jpeg, png & webp (new)
 - Imagesize: filesize, image-height, image-width
 - Resize images on upload
 - Global control about comment & rating system (dis-/enable)
 - Thumbnails: GD-version, quality and size
 - Images available in 3 sizes (thumbnail, medium, full)
 - Several options to thumbnail-displaying album.php: number of rows & columns, options to display (username, rating, comments, upload-time...)
 - RRC (recent-random-comment): display the recent images, random images and/or recent comments on the gallery/index.php with full ACP-Control
 - Sub-folder name adjustable (default: gallery/)
 - Search function (also available in the album) (page for Recent/Random/Top-Rated/Last comments/Own images)
 - Available in more languages: Bulgarian, Dutch, French, German and Russian
 - Available Addons: ACP Cleanup, ACP Import and EXIF

### Integration in phpBB:
 - Link to personal gallery on viewtopic (ACP-Option)
 - Number of user-images on viewtopic (ACP-Option)
 - Number of images on index (ACP-Option)
 - Recent/Random images in user-profile (with full ACP-Control)
 - BBcode to use gallery images in postings
 - Available for prosilver styles

### Album / Permission-management:
 - Unlimited sub... subalbums depth
 - Copy permissions on create/edit album
 - Album-types: Category, album, contest (see later for more information)
 - "Album locked"-Option
 - Inherit function on setting permissions
 - Group- & User-based permissions
 - Image-permissions: view, view without watermark, upload, upload with approval, edit, delete, report, rate
 - Comment-permissions: view, post, edit, delete
 - Moderate-permissions: moderate comments, image: delete, edit, move, handle reports, approve/lock
 - Misc-permissions: View album, Number of allowed images (also unlimited), albums (for personal galleries)
 - Personal galleries (upload by owner only), with subalbums and management through UCP

### Images / Comments:
 - Hotlink prevention: also with whitelist
 - Display exif-data
 - Watermark: with min-width and min-height, to avoid little images from being fully covered
 - Report function
 - Upload more images at once (Number settable in the ACP)
 - Full user-profile on comments
 - BBCodes, Custom-BBCodes and Smilies on comments & image-description
 - Notification (via notification only) on new images/comments/reports/approvals
 - Option to favorite images
 - Next/Previous link (with imagename and thumbnail)
 - Unread-markup of albums with new images

### ACP:
 - Statistical overview
 - Resync-Options for several dynamic values
 - Reset rating for album
 - Mass-Import (With Addon)
 - CleanUp (With Addon): Ability to delete images/comments of deleted users (or set to anonymous/guest), and personal albums

### Contests:
 - First timeperiod: Upload only, ratings and comments are not allowed, upload-username is hidden
 - Second timeperiod: No more upload, ratings are allowed, comments are not allowed, upload-username is hidden
 - End of contest: No more upload and ratings, comments are allowed, upload-username is displayed

### TODO

Make plugins work again [Highslide JS](https://highslide.com/download.php), [Shadowbox](https://www.shadowbox-js.com/download.html) and [Lytebox](https://web.archive.org/web/20130430051404/http://lytebox.com) support (These scripts must be downloaded separately because of the license) or maybe remove and add new ones.
Points Ext. Compatibility (Addon)

## Adding a new feature?

If you can make me think of it as core function I will add it. Such function is going to be the contests, the plupload support and jQuery based visualization libraries.

If you can't - you can always PR a event and write the function yourself.

## Contributing

If you want to contribute to this project do it as you will any other project - clone the repo and make a PR against it. I will review your code. Your code should be documented and your PR message should describe the changes you've made.

## Development roadmap?

As I said the 1.2 version will be primarily targeted as backwards compatible with phpBB Gallery 1.1.6 MOD (DB will be the same but you will have to manually move your images to the new location).

3.2.x is providing phpBB 3.2 compatibility and some code optimization. When initial release is done I will start adding features.

Version 3.3.x will be compatible to phpBB 3.3 and will have new features.

## Installation

This ext installs as every other phpBB extension:

 - Clone the repo to ext folder OR download the zip file and copy phpbbgallery to your ext folder
 - Go to ACP -> Customise -> Manage extensions
 - Activate phpBB Gallery
 - (optional) Activate needed extensions.

## Upgrading from 1.1.x

This is task that could not be automated. You will have to do some work. Just follow [this guide](https://www.phpbb.com/customise/db/extension/phpbb_gallery/faq/2181).

Regards,
Stanislav Atanasov

P.S.: You can get more support here - [Forum](https://www.phpbb.com/customise/db/extension/phpbb_gallery/support)
