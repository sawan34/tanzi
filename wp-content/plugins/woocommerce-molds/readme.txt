=== Plugin Name ===
Contributors: melinadonati
Donate link: http://www.melinadonati.fr
Tags: woo, woocommerce, e-commerce, shop, woothemes, automatically, product, products, variation, variations
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 0.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tired of manually configuring numerous product variations ? The Molds plugin will ease your work ! It can even do a little calculation on the prices.

== Description ==

For all users of the great Woocommerce plugin, this is a little tool to easily configure many variations (no limit in number). 
You will retrieve every option existing in the product variations panel, and the configuration is the same.

French version of the plugin already included.

A mold is composed of :

*   a name
*   a product
*   an attribute
*   0 to X terms

A mold can change variations values on :

*   image
*   "main image" option will replace a variation image if present, and fill those which don't have one ( don't check it for filling variations without image only )
*   SKU
*   stock
*   active (activate/deactivate)
*   downloadable (activate/deactivate)
*   virtual (activate/deactivate)
*   regular price
*   "use as regular price" will replace current regular price
*   "add to price" will add the number entered to current regular price
*   "substract" will substract the number entered to current regular price
*   "calculate in %" will do the chosen calculation using the number you entered as a % of the regular price

( e.g. "add" checked and "10" entered = "add 10% of variation regular price to variation regular price" )

*   sale price ( exactly the same options as regular price => "use as sale price", "add", "substract", "calculate in %" )
*   schedule
*   weight
*   dimensions
*   tax class
*   file paths (for downloadable only)
*   download limit (for downloadable only)
*   download expiry (for downloadable only)


= Using it =

1. Create all the variations wanted for a product, in the edit panel fo the product.
It's an easy task, and there's even "linking all the variations" to automatically create every possible variation.

1. Click on "Molds" which is under the Product menu.

1. Create a new mold by clicking on the button "New Mold".

1. First thing to do is to select the concerned product, then the attribute, and finally the terms.
If you don't select a term the mold will still be applied but only to the variations set on "any".
You can also select "all" in the mold creation panel to apply the mold to every term *and* "any".

1. Configure only the values that every variation of your "Product -> Attribute -> Terms" selection must have in common.
e.g. You have "T-Shirt -> Colour -> Red" the red variations must have the photo of the red version, but won't have the same price which depends on the size.
You create a mold, let's name it "Kid's Shirt : Red photo", you select "Kid's Shirt" product -> "Colour" attribute -> "Red" term.
You select a photo for the red shirts, and save it.

1. Now that your configuration is done, you can apply the mold by clicking on the empty "cupcake mold" icon on the right.
The mold is applied and feeds every "red" variation with the photo.
You're done ! You can click on the product name appearing in the mold's line, in the admin list, to go see your product variations in the edit panel.

1. To see your changes, edit your product and just save it. Go to your shop: shazam !

*Recommandations:*

Molds can be applied one after another on the same variations without any problem, just be careful to add/substract something to the price in the logical order, because it can be tricky.
When you edit a mold, it's automatically unapplied, so be careful if you have many molds in use for the same product, I recommend unapplying them all, changing what needs to be changed, and applying them again in the right order.
Options or pricing crushing each other is never pleasant, and with percentages you can have surprising results !
It's never a big problem though, as unapplying the molds will most certainly reset your variations. 
If you need to put all the prices to 0 for a fresh start you can still create a mold for that :-)

== Installation ==

Don't use this plugin if you don't have the Woocommerce plugin first, it won't work.

1. Be sure you have the Woocommerce plugin active
1. Upload the woocommerce-molds folder with all its content to the /wp-content/plugins/ directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= I'd like other options/functionalities =

I'll be glad to improve the plugin if I can, let me know about your needs by posting in the "Support" section.

== Screenshots ==

1. Edit/New mold panel (FR version)
2. Molds admin panel (FR version)

== Changelog ==

= 0.3 =

Takes account of databases with prefixes other than "wp_"
Works with Wordpress v4.2.2 (july 2015)

= 0.2 =

Corrects plugin files encoding to UTF8 - no BOM

= 0.1 =

* Handles product variations only
* Proposes price calculation options

== Upgrade Notice ==

= 0.2 =

Upgrade if you're getting a "Warning: Cannot modify header..." on your website

= 0.3 =

Upgrade if you're getting a query or database error when editing / creating a mold