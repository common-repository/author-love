=== author love ===
Contributors: Peter Scheepens
Donate link: http://digidownload.nl/
Tags: rating, author, authorrating, ajax, authorlove
Requires at least: 3.4
Tested up to: 3.4
Stable tag: 1.7
License: GPLv2 or later

a rating system for AUTHORS ! Show post authors your love

== Description ==

This rating system will rate AUTHORS instead of the usual posts/pages/comments. Let post authors know how you feel about them. Let visitors rate your authors and show which author (publisher) is trustworhty, a good read, an expert, or a fluke.

Built with ajax the author love rating system is unobtrusive and comes in many flavors. Use stars, hearts, arrows or anything else.

Display rating blocks automatically, or adapt theme yourself.
To modify your themes yourself you can set automatic rendering at 'DISABLED' and insert the php function
<?PHP render_author_love('author_id'); ?>
You can leave the author-id empty, but only if used inside the loop.
If used inside the loop the function will automatically pull the post-author of the post displayed at that time.

Another option is the function :
<?PHP return_author_love( $author_id="FALSE",$pos="left",$author_info=""); ?>
$author_id = empty || (or) Any valid author ID
$pos = left || right
$author_info = empty || author_nicename

[youtube http://www.youtube.com/watch?v=kOzj7Qzlavc]

== Installation ==

install and activate the plugin. No settings necessary.

you can find the author love admin menu in your admin panel -> Settings ...

== Frequently Asked Questions ==

Why are the ratings not showing up ?
A. They probably are, but only on the single posts. To show ratings on the main pages too check the checkbox in the admin settings.

Why are the ratings not showing up ?
A. you may have selected the 'disabled' option in the menu setings (so you can use function calls in PHP yourself)

Where are the translation fields ?
A. in the PREMIUM version

== Changelog ==

= 1.7 (30/7/2012) =
* major code clean-up
* new ajax routines

= 1.05 (30/7/2012) =
* changes paths to reflect SVN naming convention (fixing 'missing' images)

= 1.0 (30/7/2012) =
* Initial release

== Upgrade Notice ==

Upgrades to the free version are available through wordpress.org or can be downloaded freely at : http://digidownload.nl/author-love-voor-wordpress/

== Credits ==

author love was written and developed further by Pete Scheepens.
Core features were based on jRating by ALPIXEL and jQuery

* Upgrades
* inspiration

== Screenshots ==

1. screenshot
2. admin panel