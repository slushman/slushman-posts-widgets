=== Slushman Post Widgets ===
Contributors: slushman
Donate link: http://slushman.com/
Tags: widget, post, featured, upcoming
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 0.1.0
License: GPLv2

Slushman Post Widgets creates two widgets for displaying posts. The Featured Posts widget allows you to choose posts you want to feature, like favorite posts. The Upcoming Posts widget displays scheduled posts.

== Description ==

Slushman Post Widgets creates two widgets for displaying posts. The Featured Posts widget allows you to choose posts you want to feature, like favorite posts. On the widget form, enter the post ID(s) of the widgets you'd like to feature and they will appear in the widget. The Upcoming Posts widget displays scheduled posts. Each widget allows for customizing the output template for your site. The default templates included were developed for the The Craft podcast site.

Features

* The Featured Posts widget can display any already published post.
* The Upcoming Post widget automatically displays scheduled, but not published posts.
* Each widget allows for choosing the amount of posts to display.
* The public-facing output can be completely customized through templates.
* Uses WordPress transients to speed up loading players
* The Upcoming Posts widget can be automatically hidden if there aren't any scheduled posts.

== Installation ==

1. Upload the Slushman Posts Widgets folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag one of the widgets to a sidebar on the 'Widgets' page under the 'Appearance' menu

== Frequently Asked Questions ==

= Where do I put custom templates? =

The template function looks for templates the following places (in this order):
1. Child theme directory, in a folder called "spw-templates"
2. Parent theme directory, in a folder called "spw-templates"
3. WP Content directory, in a folder called "spw-templates"
4. Inside the plugin's directory

I recommend putting custom widget templates in your theme inside a folder called "spw-templates". That will be the first place the plugin will look for a template and anything you put into that folder will override the default templates included in the plugin.



= What is required for the template? =

The output template should be the output for each posting. For example, the default template for the Upcoming Posts widget displays the post thumbnail across the full width of the widget, with the post title underneath. All the data from the post is available in a $post object, so to display the title: $post->post_title. 

You also have access to all the post metadata through a $custom variable, which is a multi-dimensional array. To get the 'testdata' meta from the post's metadata would look like this: $custom['testdata'].



= How do I find the post IDs? =

This plugins adds a custom column to the Posts and Pages listings that displays the ID.



== Screenshots ==

1.



== Changelog ==

= 0.1 =
* Plugin created.



== Upgrade Notice ==

= 0.1 =
Plugin released.