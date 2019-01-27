# phpBB 3.1 Extension - Apache static pages for anonymous users

The goal of this extension is to fasten the loading of pages for anonymous users (or lurkers) by avoiding to go through php when unecessary, and hence avoid the creation of anonymous sessions.

## Drawbacks

 - The viewer counter will be wrong: use a proper analytics tool instead and disable it from your theme.
 - Previous anonymous users will still use the php version has long as their cookie is valid
 - Writing/Answering to a post is slightly slower (needs to write to disk)

## Installation

Clone into phpBB/ext/wardormeur/anoncache:

    $ git clone https://github.com/wardormeur/anoncache.git phpBB/ext/wardormeur/anoncache

Go to "ACP" > "Customise" > "Extensions" and enable the "Apache static page" extension.

## Setup

The extension cannot (and shouldn't have the right to) modify your .htaccess or your virtal host, yet it needs some configuration as the point of this plugin is to avoid the round-trip to phpbb when unecessary.
Namely, you must apply before the handling of the app.php the content of `.htaccess.patch` to get the effect of the plugin.

## Comparison

Tested locally with Apache's ab, running
`ab -n 1000 -c 100 -l http://localhost/` [with](ab-w-plugin.output) and [without](ab-wo-plugin.output) the plugin

Gain is roughly 100% faster on not-logged in users

## To be done

  - Ensure that reusing a sessionId doesn't introduce a security issue
  - Reuse the Anonymous user' session_id when generating the static file(s)
  - Make it work for viewtopic as well
