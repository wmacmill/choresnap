=== WooCommerce Autocomplete Order ===
Contributors: rashef
Donate link: http://cl.ly/1j3n450I323q
Tags: WooCommerce, order, complete, virtual, autocomplete
Requires at least: 3.3
Tested up to: 4.0.1
Stable tag: 1.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Do you hate WooCommerce for obliging you to manually approve every order placed for non-downloadable goods? This plugin is the answer.

== Description ==

WooCommerce by design allows to automatically mark as "Completed" only orders for products marked as both "Virtual" and "Downloadable". This plugin actually extend this feature through 3 different modes:    
* Virtual Paid Products Only: order for products marked as "Virtual" will be turned to "Completed" upon successful payment.      
* All Paid Products: orders for any product are turned to "Completed" upon successful payment.      
* All Products: each and every order is turned to "Completed" irrespective for the payment method and whether or not the payment happened.      
Please be aware that the third mode allows the customer to immediately access the product (if downloadable, for example) whether or not the payment was performed.

**WooCommerce Product Bundles**    
Please consider that if you selected "Virtual Paid Product Only" mode, you must activate "Non-bundled shipping" for the product bundle if you want the autocomplete to work.    

== Installation ==

1. Upload the plugin's folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Flag as "Virtual" all the products you want to be unlocked for users upon successful payment without any further (manual) action from you in the backoffice

== Screenshots ==

1. Go to the settings page from the Dashboard.
2. Activate the mode you prefer.

== Frequently asked questions ==

= The plugin is not working =
Please ensure that you picked the right mode from the list.      

= Uh-uh, still not working! =
If you are testing with PayPal, you must ensure that the [ICN](https://developer.paypal.com/webapps/developer/docs/classic/products/instant-payment-notification/) - Instant Payment notification - is working properly. One of the most common problem is using in WooCommerce an e-mail which is not the primary email used to register the PayPal account.      

= No dude, not working yet! =
Please deactivate my plugin (or select mode "Off"), test with a product marked as "virtual" and "downloadable". If it still doesn't work, then there's something wrong in your WooCommerce configuration or in your payment gateway. If it does work, please write me through the forum!       

== Changelog ==

= 1.1 =    
* Solved PHP Notices and Warnings    
* Plugin is now compatible with WooCommerce Product Bundles    

= 1.0 =
* Plugin completely rewritten to comply with WordPress 4.0 and WooCommerce 2.0.      
* Added 3 different modes to activate the plugin     
* Added a settings page (in WooCommerce dashboard) to select the mode we want to activate (under WooCommerce > Settings > Woo Extra Options).      
	
= 0.1.2 =    
* Updated compatibility.     
* Added localization support.     
* Added Italian localization.     
* Added Spanish localization.     

= 0.1.1 =
* Added links to support and the official page.     
	
= 0.1 =
* First release.     

== Upgrade notice ==
The latest version of the plugin introduced some PHP Notice and Warning messages. All of them are solved now.
The plugin is now compatible with WooCommerce Product Bundles: please read the description to understand how to configure your bundle.
