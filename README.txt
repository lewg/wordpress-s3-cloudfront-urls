=== S3/CloudFront Upload Tool ===
Contributors: ljg3
Donate link: http://example.com/
Tags: s3, cloudfront, aws
Requires at least: 3.5.1
Tested up to: 3.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tool specifically for uploading to S3 for the purpose of generating CloudFront Links. 

== Description ==

This tools was created specifically for uploading files to S3 for the purpose of generating CloudFront links. Additionally, because it uses the AWS Javascript SDK, all interations with AWS are done client-side, with no server storage necessary. Optionally, if you use the Qloudstat metrics service, you can connect it to their API to get your download numbers. 


== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 's3-cloudfront-urls'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `s3-cloudfront-urls.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `s3-cloudfront-urls.zip`
2. Extract the `s3-cloudfront-urls` directory to your computer
3. Upload the `s3-cloudfront-urls` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==

= Are the client-side AWS interactions secure? =

Only so much as you trust your users. Because the access key and secret are in the source of the uploader game, any of your users could extract it. That's why it's HIGHLY recommended that you create an IAM account with permissions specifically for the bucket you're using. You trust them with your media library, so you're also going to have to trust them with write access to your bucket!

= How do I create said policies? =

The plugin creates suggested policys once you have the setting in place. 

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* First Release


<!-- == Upgrade Notice == -->

<!-- = 1.0 = -->
<!-- Upgrade notices describe the reason a user should upgrade.  No more than 300 characters. -->
