=== Plugin Name ===
Stable tag: 2.5.4
Tested up to: 4.6.1
Requires at least: 2.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors: Tkama
Tags: thumbnail, image

Create any thumbnails on fly and cache the result. Auto-create of post thumbs based on: WP post thumbnail or first img in post content/attachment.


== Description ==
Super convenient way to create post thumbnails on the fly without server overload.

The best alternative to scripts like "thumbnail.php".

### Usage ###

The plugin for developers firstly, because it don't do anything after install. In order to the plugin begin to work, you need use one of plugin function in your theme or plugin. Example:

`
<?php echo kama_thumb_img('w=150 &h=150'); ?>
`

Using the code in the loop you will get ready thumbnail IMG tag. Plugin takes post thumbnail image or find first image in post content, resize it and create cache. Also creates custom field for the post with URL to original image. In simple words it cache all routine and in next page loads just take cache result.

You can make thumbs from custom URL, like this:
`<?php echo kama_thumb_img('w=150 &h=150', 'URL_TO_IMG'); ?>`

The `URL_TO_IMG` must be from local server: by default, plugin don't work with external images, because of security. But you can set allowed hosts on settings page: `Settings > Media`.

**All plugin functions:**

`
// return thumb url URL
echo kama_thumb_src( $args, $src );

// return thumb IMG tag
echo kama_thumb_img( $args, $src );

// return thumb IMG tag wraped with <a>. A link of A will leads to original image.
echo kama_thumb_a_img( $args, $src );
`

Parameters:

**$args** (array/string) - Arguments to create thumb.
Accepts:

* `w | width`              – (int) desired width (required)
* `h | height`             – (int) desired height (required)
* `notcrop`                – (isset) set to resize image by one of the parameter: width | height.
* `q | quality`            – (int) jpg compression quality (Default 85. max.100)
* `alt`                    – (str) alt attr of img tag
* `title`                  - (str) title attr of img tag
* `class`                  – (str) class attr of img tag.
* `no_stub`                – (isset) don't show picture stub if there is no picture. Return empty string.

* `post_id | post`         - (int|WP_Post) post ID. It needs when use function not from the loop. If pass the parameter plugin will exactly knows which post to process. Parametr 'post' added in ver 2.1.

* `attr`                   - (str) Allow to pass any attributes in IMG tag. String passes in IMG tag as it is, without escaping.

* `allow`                  - (str) Which hosts are allowed. This option sets globally in plugin setting, but if you need allow hosts only for the function call, specify allow hosts here. Set 'any' to allow to make thumbs from any site (host).


If parameters passes as array second argument `$src` can be passed in this array, with key: `src` или `url` или `link` или `img`:
`
echo kama_thumb_img( array(
	'src' => 'http://yousite.com/IMAGE_URL.jpg',
	'w' => 150,
	'h' => 100,
) );
`

if parameters 'w' and 'h' not set, both of them become 100 - square thumb 100х100 px.

**$src** (string) - УРЛ то any image. In this case plugin will not parse URL from post content.


### Notes ###
1. You can pass `$args` as string or array:
`
// string
kama_thumb_img('w=200 &h=100 &alt=IMG NAME &class=aligncenter', 'IMG_URL');

// array
kama_thumb_img( array(
	'width'  => 200,
	'height' => 150,
	'class'  => 'alignleft'
	'src'    => ''
) );
`

2. You can set only one side: `width` | `height`, then other side became proportional.
3. `src` parameter or second function argument is for cases when you need create thumb from any image not image of WordPress post.
4. For test is there image for post, use this code:
`
if( ! kama_thumb_img('w=150&h=150&no_stub') )
	echo 'NO img';
`

### Examples ###
#### #1 Get Thumb ####

In the loop where you need the thumb 150х100:

`
<?php echo kama_thumb_img('w=150 &h=100 &class=alignleft myimg'); ?>
`
Result:
`
<img src='thumbnail_URL' alt='' class='alignleft myimg' width='150' height='100'>
`

#### #2 Not show stub image ####
`
<?php echo kama_thumb_img('w=150 &h=100 &no_stub'); ?>
`

#### #3 Get just thumb URL ####
`
<?php echo kama_thumb_src('w=100&h=80'); ?>
`
Result: `/wp-content/cache/thumb/ec799941f_100x80.png`

This url you can use like:
`
<img src='<?php echo kama_thumb_src('w=100 &h=80 &q=75'); ?>' alt=''>
`

#### #4 `kama_thumb_a_img()` function ####
`
<?php echo kama_thumb_a_img('w=150 &h=100 &class=alignleft myimg &q=75'); ?>
`
Result:
`
<a href='ORIGINAL_URL'><img src='thumbnail_URL' alt='' class='alignleft myimg' width='150' height='100'></a>
`

#### #5 Thumb of any image URL ####
Pass arguments as array:
`
<?php
	echo kama_thumb_img( array(
		'src' => 'http://yousite.com/IMAGE_URL.jpg',
		'w' => 150,
		'h' => 100,
	) );
?>
`

Pass arguments as string:
`
<?php
	echo kama_thumb_img('w=150 &h=200 ', 'http://yousite.com/IMAGE_URL.jpg');
?>
`
When parameters passes as string and "src" parameter has additional query args ("src=$src &w=200" where $src = http://site.com/img.jpg?foo&foo2=foo3) it might be confuse. That's why "src" parameter must passes as second function argument, when parameters passes as string (not array).


#### #6 Parameter post_id ####
Get thumb of post ID=50:

`
<?php echo kama_thumb_img("w=150 &h=100 &post_id=50"); ?>
`

### I don't need plugin ###
This plugin can be easily used not as a plugin, but as a simple php file.

If you are themes developer, and need all it functionality, but you need to install the plugin as the part of your theme, this short instruction for you:

1. Create folder in your theme, let it be 'thumbmaker' - it is for convenience.
2. Download the plugin and copy the files: `class.Kama_Make_Thumb.php` and `no_photo.png` to the folder you just create.
3. Include `class.Kama_Make_Thumb.php` file into theme `functions.php`, like this:
`require 'thumbmaker/class.Kama_Make_Thumb.php';`
4. Bingo! Use functions: `kama_thumb_*()` in your theme code.
5. If necessary, open `class.Kama_Make_Thumb.php` and edit options (at the top of the file): cache folder URL/PATH, custom field name etc.

* Conditions of Use - mention of this plugin in describing of your theme.


== Screenshots ==

1. Setting block on standart "Media" admin page.


== Installation ==

### Instalation via Admin Panel ###
1. Go to `Plugins > Add New > Search Plugins` enter "Kama Thumbnail"
2. Find the plugin in search results and install it.


### Instalation via FTP ###
1. Download the `.zip` archive
2. Open `/wp-content/plugins/` directory
3. Put `kama-thumbnail` folder from archive into opened `plugins` folder
4. Activate the `Kama Thumbnail` in Admin plugins page
5. Go to `Settings > Media` page to customize plugin


== Changelog ==

= 2.5.4 =
* ADD: thumb img in post content: now consider 'srcset' attribute if it's set

= 2.5.3 =
* FIX: regular about 'mini' class in IMG tag and now you can change 'mini' class

= 2.5.2 =
* FIX: some minor fixes for plugin activation and uninstall

= 2.5.1 =
* ADD: Cyrilic domain support - such URL will wokr 'http://сайт.рф/img.jpg'.
* ADD: 'allow' parameter from single function call fix - not work correctly.

= 2.5 =
* ADD: New filters for Kama_Make_Thumb class: 'kmt_set_args', 'kmt_is_allow_host', 'kmt_img', 'kmt_a_img'
* ADD: Is allow host now checks for only main domain (not subdomain). Ex: now plugin works if you try create thumb of 'http://site.com/img.png' from 'foo.site.com' host
* ADD: New parameter 'allow' - set allowed hosts for only current function call. Ex: kama_thumb_img("w=200 &h=200 &allow=any", 'http://external-domain.com/img.jpg' );

= 2.4.4 =
* IMPROVE: Get file from remote domain not work properly if there were redirects...

= 2.4.3 =
* FIX: mini class for IMG in content. Was output error if IMG inside and not inside A tag.

= 2.4.2 =
* CHANGE: Kama_Make_Thumb class::get_src_and_set_postmeta() become publik. In order to just original get img url of post.
* FIX: search img url in post content not worked with relative url like: "/foo.jpg", and not worked if img extension "jpeg";
* FIX: many times faster `&lt;img class="mini"&gt;` treatment in post content (regular expression fix);

= 2.4.1 =
* FIX: parsing parametrs if it given as string. ex: "h=250 &notcrop &class=aligncenter" notcrop becomes  "notcrop_"

= 2.4 =
* FIX: If place second function parameter $src (img url) - it didn't work correctly, because stupid mistake.
* FIX: when use class 'mini' in post content and IMG already wrapped with A tag, plugin made double A wrap.
* IMPROVE: Now self hosted images firstly parses as absolute path, and if there is error, it parses as URL. This method is much stable in some cases.
* ADD: Place 'any' in alowed hostes string on settings page, and plugin will make thumbs from any domain.

= 2.3 =
* Great Bug: Now if parameters passes as string 'src' parameter better pass as second argument of functions kama_thumb_*("w=200 &h=300", 'http://site.com/image.jpg').

= 2.2 =
* ADD: 'attr' parameter. Allow to pass any attributes in IMG tag. String passes as it is, without escaping.

= 2.1 =
* ADD: aliases for passed parameters: src = url|link|img, post_id = post (can be passed as post object), q = quality, w = width, h = height
* FIX: when parameters passes as string and "src" parameter has aditional query args ("src=$src &w=200" where $src = http://site.com/img.jpg?foo&foo2=foo3) it might be confuse, that's why "src" parameter must passes in the end of string, when parameters passes as string (not array).
* CHG: some code refactoring in class.Kama_Make_Thumb.php file.
* FIX: no_stub worked only for images from posts. When 'src' is setted parameter 'no_stub' had no effect;

= 2.0 =
* ADD: notice message when no image library instaled on server (GD or Imagic)
* ADD: diferent names for real thumb and nophoto thumb. And possibility to clear only nophoto thumbs from cache. All it needed to correctly create IMGs from external URLs (not selfhosted img) - sometimes it can't be loaded external imges properly.

= 1.9.4 =
* FIX: ext detection if img URL have querya rgs like <code>*.jpg?foo</code>

= 1.9.3 =
* CHG: DOCUMENT ROOT detection if allow_url_fopen and CURL disabled on server

= 1.9.2 =
* FIX: trys to get image by abs server path, if none of: CURL || allow_url_fopen=on is set on server

= 1.9.1 =
* FIX: getimagesizefromstring() only work in php 5.4+

= 1.9.0 =
* ADD: Images parses from URL with curl first

= 1.8.0 =
* ADD: Images parses from URL. It FIX some bugs, where plugin couldn't create abs path to img.
* ADD: Allowed hosts settings. Now you can set sites from which tumbs will be created too.

= 1.7.2 =
* CHG: Back to PHP 5.2 support :(

= 1.7.1 =
* CHG: PHP lower then 5.3 now not supported, because it's bad practice...

= 1.7 =
* FIX: refactor - separate one class to two: "WP Plugin" & "Thumb Maker". Now code have better logic!

= 1.6.5 =
* ADD: EN localisation

= 1.6.4 =
* ADD: now cache_folder & no_photo_url detected automatically
* ADD: notcrop parametr

