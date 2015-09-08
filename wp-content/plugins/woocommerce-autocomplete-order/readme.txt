=== WooCommerce Autocomplete Orders ===
Contributors: rashef
Tags: WooCommerce, order, complete, virtual, autocomplete
Donate link: http://cl.ly/2C2W181j1G2g
Tested up to: 4.3
Stable tag: 1.1.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Did you know that in WooCommerce virtual products still need a manual approval for the order to be completed? This plugin is the answer.

== Description ==
WooCommerce by design allows only orders for products marked as both \"Virtual\" and \"Downloadable\" to switch automatically to \"Completed\" upon instant payment. This plugin extends this feature adding three new, different scenarios:
* Virtual Paid Products Only: order for products marked as \"Virtual\" will be turned to \"Completed\" upon successful payment.
* All Paid Products: orders for any product are turned to \"Completed\" upon successful payment.
* All Products: each and every order will turn to \"Completed\" irrespective for the payment method and whether or not the payment was immediate.
      
Please be aware that the third mode allows the customer to access the product immediately regardless for whether or not he completed the payment.

== Installation ==
1. Upload the plugin\'s folder to the `/wp-content/plugins/` directory or install it through the integrated plugin installer
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Flag as \"Virtual\" all the products you want to unlock upon successful payment without any further (manual) action from you in the dashboard

== Frequently Asked Questions ==
= The plugin is not working =
Please ensure that you picked the right mode from the list.      

= Uh-uh, still not working! =
If you are testing with PayPal, you must ensure that the [ICN](https://developer.paypal.com/webapps/developer/docs/classic/products/instant-payment-notification/) - Instant Payment notification - is working properly. One of the most common problems is using in WooCommerce an e-mail which is not the primary email used to register the PayPal account.      

= No dude, not working yet! =
Please deactivate my plugin (or select mode \"Off\"), test with a product marked as \"virtual\" and \"downloadable\". If it still doesn\'t work, then there\'s something wrong in your WooCommerce configuration or in your payment gateway. If it does work, please write me through the forum!    

== Screenshots ==
1. Set the products as \"Virtual\" products
2. In your PayPal account browse \"Seller preferences\" under \"Selling Tools\"
3. Click on \"Instant Payment Notifications\"
4. Activate IPN notifications and insert the link as in the picture (using your own domain name)
5. Ensure that your main email is the same email you are using to receive payments
6. Browse WooCommerce > Settings > Woo Extra Options
7. Pick the mode that suits you best

== Changelog ==

= 1.1.1 =
	* Minor fixes
	* Documentation completely rewritten

= 1.1 =
	* Solved PHP Notices and Warnings
	* Plugin is now compatible with WooCommerce Product Bundles

= 1.0 =
	* Plugin completely rewritten to comply with WordPress 4.0 and WooCommerce 2.0.      
	* Added 3 different modes to activate the plugin:
		* Virtual Paid Products Only: order for products marked as \"Virtual\" will be turned to \"Completed\" upon successful payment.           
		* All Paid Products: orders for any product are turned to \"Completed\" upon successful payment.      
		* All Products: each and every order is turned to \"Completed\" irrespective for the payment method and whether or not the payment happened.      
    	* Added a settings page (in WooCommerce dashboard) to select the mode we want to activate (under WooCommerce > Settings > Woo Extra Options).      
	
= 0.1.2 =
	* Updated compatibility.     
	* Added localisation support.     
	* Added Italian localisation.     
	* Added Spanish localisation.     

= 0.1.1 =
	* Added links to support and the official page.     
	
= 0.1 =
	* First release.