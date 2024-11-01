=== Plugin Name ===
Contributors: Jeremias Francisco
Donate link: http://ihayag.com/wpplugin
Tags: wp ecommerce csv importer, wp-ecommerce, wp-E-commerce, WP E-commerce, csv importer, ecommerce csv, e-commerce csv
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 3.2

WP E-commerce CSV importer, add numbers of products in WP E-commerce plugin by GetShopped in one go.

== Description ==

The WP E-commerce CSV importer, helps you add products, change product information, publish and unpublish product items, activate or deactivate products, assign multiple categories and/or brands, add or change variations into product items and set quantity to a particular product and/or variation per product to your WP E-commerce plugin in easy step.

Visit the Plugin Forum to find out how you can manage your products and add multiple variations and thumbnails.
Learn more about the CSV importer plugin for WP e-Commerce version 3.8
http://ihayag.com/wpplugin/
http://ihayag.com/wpplugin/blog/

In no doubt that WP Ecommerce plugin by GetShopped is one of the best plugin for wordpress, even though the plugin comes with their own csv importer, still some of the key feaures are missing like thumbnail image, sale price or special price, local and International shipping per item, donation item and especially Categories. Although the included csv importer will be able you to select category, but you have to create csv file for every category. With this plugin you just create one csv file and populate them with category id and import them in one go.

This was inspired by the CSV importer included WP E-commerce.

Do you want your WP e-Commerce site to be more responsive.
Learn more about the WP e-Commerce AJAX plugin at: 

Forum : http://ihayag.com/wpplugin/home/?mingleforumaction=viewforum&f=3.0

Demo Site: http://ihayag.com/wpplugin/store-1/


Join the happy growing community that tried and used the plugin. If you are one of them that wants an easy to manage WP E-commerce website solution. Download and test it yourself and be one of the happy growing community.



== Installation ==

This section describes how to install the plugin and get it working.

1. You must have a WP E-commerce plugin by getshopped installed.
2. Upload `wp-ecommerce-csv-importer` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. In the Settings you will find the link for the wp-ecommerce-csv-importer.


== Frequently Asked Questions ==

= How to use the plugin? =


In the Settings of your Wordpress dashboard, click the WP E-commerce CSV Importer then the main page will prompt.

1. Click the "Choose File" button and select your csv file. Use the included CSV(Comma Delimited) Sample file.
2. Click the "Validate Header" button to check if your csv file complies with header naming convention. Please see other topic on how to create a csv File or visit us at http://ihayag.com/wpplugin/
3. If no errors found in the csv file, another set of buttons and brief instructions on how to import the csv file will prompt into the main page.
4. If all goes well according to your liking, at any time you can click the "Import Now" button and boom! you're done.

= How to Create CSV file? =
Header is very important when using WP E-commerce CSV Importer plugin. This is a case senstive and you must follow this rule. For instance you want to import sku, product description, additional description, price, sale price, local shipping. Here's how you would write at the very first row of your csv file.

SKU, Product Description, Additional Description, Price, Sale Price, Local Shipping

As mentioned earlier this header fields are case sensitive.

**Note: 
Product Id or SKU is a required field. You can use both, but I recommend to use SKU and must be included in your all csv file as the primary key. 

This plugin can also be used to change related informations in your product details, and again you must include either Product Id or SKU or both.

Example: 
You have all your product in the database and you don't have SKU and want to change price or product description or add sale price using Product Id as your primary key. In your csv file include the "Product Id" "Price" "Sale Price" "Product Description" "SKU" if you'd like to add sku in your product. This will create or add the sku and update whatever product details you'd like to change.


= For Mac Users =
If you are using MS Excel spreadsheet for Mac, make sure you save your CSV file as "Comma Delimited" and not "Comma Separated Values". Otherwise it won't work.
If you don't have "Comma Delimited" in your Excel spreadsheet, go to openoffice.org download and install the openoffice. Use the openoffice to create your CSV file(Comma Delimited).

= WP E-commerce CSV Importer Needs Server Session =
The plugin needs server sessions to store some variables. If your sessions was turned-off or not sure, please contact and web host and they will set this up for you.

= How to import image? =
You must include the "Thumbnail Image" header field in you csv file and at the right side of the page you will see the the "upload" button, click onto that and select all the image files that you would like to upload. A message indicator will show up to let you know the progress of the upload.

= Have more questions? =
Please visit us at http://ihayag.com/wpplugin/ for more infomation.


= Updating =

Simply copy the new files across and replace the old files saving saving ones you've modified. If you have product images uploaded then do not overwrite the product_images folder or images folder. If you have downloadable products for sale do not overwrite the files folder.

When updating it is important that you do not overwrite the entire images folder. Instead you should copy over the contents of the new images folder into the existing images folder on your server - saving all the exiting product images you may have already uploaded.

If you experience database errors try de-activating and re-activating your plugin. 


== Changelog == 

Version 1.0 and 1.0.1 and 1.0.2

Sale Price is not updating or changing.

Version 1.0.3

1. Sale Price is now updating correctly
2. No particular order for header field is now fully supported.

Version 1.0.4

1. Some web servers are set-up differently and in this version taken care of the issue.
2. Adjustment on the "Thumbnail Image" header.


== Upgrade Notice ==

Some web servers are set-up differently and in this version taken care of the said issue.


== Screenshots ==

Check out the Screenshots

http://ihayag.com/wpplugin/home/?mingleforumaction=viewforum&f=2.0

For WP e-Commerce version 3.8.x
variation.gif
http://ihayag.com/wpplugin/wp-content/uploads/wpec38/main_1.gif
http://ihayag.com/wpplugin/wp-content/uploads/wpec38/meta.gif
http://ihayag.com/wpplugin/wp-content/uploads/wpec38/thumbnail.gif
http://ihayag.com/wpplugin/wp-content/uploads/wpec38/variation.gif

For ealier version of WP e-Commerce
http://ihayag.com/wpplugin/wp-content/uploads/2010/10/admin-page.gif
http://ihayag.com/wpplugin/wp-content/uploads/2010/10/header-checker.gif
http://ihayag.com/wpplugin/wp-content/uploads/2010/10/cvs-file.gif
http://ihayag.com/wpplugin/wp-content/uploads/2010/10/upload-image.gif



