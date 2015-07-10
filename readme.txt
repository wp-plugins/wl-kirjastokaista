=== Plugin Name ===
Contributors: j0nnii
Tags: Kirjastokaista, library, libraries, video, webtv
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides a very easy and efficient way to embed videos from Kirjastokaista (Library Channel) service in your posts, pages and widgets.

== Description ==

WL Kirjastokaista plugin provides a very easy and efficient way to embed videos from Kirjastokaista (Library Channel) service in your posts, pages and widgets. You can create listings that connect in real-time with Kirjastokaista's API or get a single media embed-code to post editor.

----------

**Usage**

-----

**SHORTCODES**

In WordPress admin, navigate to Tools > WL Kirjastokaista. Choose UI, categories, filters etc. and test shortcode results with pressing button View Shortcode Results. If you're satisfied with the results, press Generate Shortcode -button and copy paste the generated shortcode to WordPress content area of your choice.

If you want to use shortcodes in widget areas, turn on Allow Kirjastokaista and other shortcodes to run in Text Widget setting from Tools > WL Kirjastokaista>Settings.

**SAVING A SHORTCODE & CACHING**

You can save the generated shortcoded to database and reference it via [kirjastokaista id=xx] where xx is the id of the saved shortcode. One benefit of saving is that it’s possible to mark saved shortcodes for caching on first load. That means, shortcode gets processed on first load of the page and saved as html in database, so the page loads very fast after the first initial load.

**SHORTCODE LAYOUTS**

To edit shortcode layout templates, copy templates from plugin's templates -folder and include them in your /wp-content/active theme's folder/kirjastokaista/plugin-layout-templates/ . That way, if you update plugin, templates won't get overwritten. For reference what variables you can use in templates, see plugin file kirjastokaista-api-info.php

**GET VIDEO EMBED CODE TO POST CONTENT**

In post content WYSIWYG editor, press button Kirjastokaista. Paste URL of your Kirjastokaista video and embed code of the video will automatically appear in your content.

If you don't see Kirjastokaista button on WYSIWYG content editor, insure that you have Show Kirjastokaista embed button on content editor setting turned on in Tools > WL Kirjastokaista>Settings.

**LOCALIZATION**

To localize this plugin, translate language/kirjastokaista.po file and save it as .mo. Save .mo file as /wp-content/languages/plugins/kirjastokaista-xx.mo where xx is your language code. 


----------


**OTHER**

Read more info on Kirjastokaista (Library Channel) service [here](http://www.kirjastokaista.fi).

This plugin is developed as open source by [Buskerud fylkesbibliotek/Webløft](http://www.webloft.no) and Jonni Tammisto in spring 2015.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload plugin to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Adjust plugin's default settings to your preference.

== Frequently Asked Questions ==

= Why is it not possible to choose media languages + some ordering options at the same time =

This is not possible due to API limitation.

== Screenshots ==

1. Shortcode generator

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release