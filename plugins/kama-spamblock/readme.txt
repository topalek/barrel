=== Plugin Name ===
Contributors: Tkama
Tags: spam, spammer, autospam, spamblock, antispam, anti-spam, protect, comments, ping, trackback, bot, robot, human, captcha, invisible
Requires at least: 2.7
Tested up to: 4.0
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Very light invisible  method to block auto-spam when spam comment is posted. Pings and trackbacks cheks for real backlink.

== Description ==

Block auto-spam when spam comment is posted. Absolutely invisible for users. There is no any captcha code. Pings and trackbacks cheks for real backlink.

It's very simple and effective plugin. It's option to install Kama Spamblock even if you have external comment system like "Disqus", because autospam could posted directly to WordPress "wp-comments-post.php" file. The plugin will protect this.

Plugin just block spam comment, not mark it as "spam". So you don't even know that spam was posted to your site, you don't here about spam at all.

Localisation: Russian


== Installation ==
Add and activate the plugin through the 'Plugins' menu in WordPress



== Screenshots ==

1. Plugin settings on standart WordPress <strong>Settings > Discussion</strong> page.

2. Spam alert, when spam comment detected or if user have javascript disabled in his browser. This alert allows send comment once again, when it was blocked in any nonstandard cases.



== Frequently Asked Questions ==

= On comment post i see message "Antispam block your comment!" is it normal plugin work? =

NO! Plugin invisible for users. You need to go to WordPress "Discussion" setting page. At the bottom you will see "Kama Spamblock settings". Set there correct ID attribute of comment form submit button. This attribute you can get from "souse code" of you site page where comment form is. Look for: type="submit" id="-----"



== Changelog ==

= 1.5.2 =
added: delete is_singular check for themes where this check work wrong. Now plugin JS showen in all pages

= 1.5.1 =
added: js include from numbers of hooks. If there is no "wp_footer" hook in theme

= 1.5.0 =
added: Russian localization