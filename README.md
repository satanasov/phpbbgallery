
#phpBB Gallery

© 2012 - [nickvergessen](http://www.flying-bits.org)

© 2014 - [Lucifer](http://www.anavaro.com)

This is direct port of [nickvergessen](https://github.com/nickvergessen)'s phpBB Gallery 1.1.6 for phpBB 3.0. As he has no time to support it or to port it, I have taken this on my self. The phpBB Gallery is very good project and I use it heavily. This is why I can't leave it and just wait for someone to port it or ti create alternative.

In the [original](https://github.com/phpbbgallery/phpbb-gallery) initial port, the main developer have stated that he wants to split the gallery to smaller peaces - core and few non essentials. I can argue what should be in the core and what should be left as a plug-in. My main goal for now is to migrate the gallery as is. Not chopping "non essetials".

The incoming 1.2 version of phpBB Gallery is going to be just a port of the original MOD. With as little changes as possible.

I still think that taking the non-essentials out of the core code is a good idea. Just won't do it so extensevly for the initial version.

Please bear in mind that this is WORK IN PROGRESS. It is as BETA as they comme.

#What Works (for the moment)

 - ACP
    - Overview
        - Create file structure and check if it is writable
        - Resynchronize image count and personal albums
        - Refresh "Last image"
        - Puge cache
        - Reset ratings for album (but the ratings are still not implemented)
        - Create personal gallery for user
    - Configure gallery (you can choose and set every option here)
    - Manage albums (you can create, delete and manage every public album (it allows you to create contests, but the contests will be the one of the few things split as plug-in)
    - Permissions (allow you to set all the permissions for all and every album)
 - UCP
    - Manage albums (create, delete and edit albums (zebra ACL is still not implemented))
    - Manage subscritions (here you can manage images and and album you are subscribed to)
    - Personal settings
 - Controller (url ./gallery)
    - You can see albums and images
    - ACL seted in ACP -> Gallery -> Permissions are recongized for view, upload and edit
    - Watermark
    - You see image description
    - You can see user albums
    - You can upload pictures
    - You can edit pictures
    - You can delete pictures
    - You can comment/quote
    - You can edit comment
    - You can delete comment
    - You can rate images
    - You can report images
    - Controller (url ./gallery/moderate)
      - There is basic overview of queues
      - Moderators can edit images
      - Moderators can delete images
      - Unaproved images are marked in album
	  - moderator can approve image
	  - Moderator can disaprove image
	  - Moderator can move image

#What Does not work (yet)
 - Moderation
   - moderator can review reports
   - etc.
 - BBCode
 - Comments:
   - moderate comment
   - can report comment
 - Serch (ego, latest, random, user, comment, rating)
 - Notifications (on new image, new comment, new rating, image submited for moderation, image approved, image reported)
 - Image subscription
 - ACP clean up (this I think must stay in the core until atleast 1.2.1)
 - ACP mass upload (still think it's a core function)
 - Zebra ACL for albums/images

#Add ons
 - Exif
 
#Future?

As I said the 1.2 version will be primarly targeted as backword compatible with phpBB Gallery 1.1.6 MOD (DB will be the same but you will have to manualy move your images to the new location). 1.2 will incorporate TRAVIS-CI tests (all my projects do).

1.2.1 will be where the code clean up, new features and core separation will occure. I will strive to keep everything bakword compatible.

Thank you for reading this long README. 

[here a travic-ci badge will be placed]

Regards,
Stanislav Atanasov
