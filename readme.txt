=== Variation URLs for WooCommerce – Shareable Links to Product Variations ===
Contributors: imalinawaz
Donate link: https://www.buymeacoffee.com/bealinawaz
Tags: woocommerce variations, variation url, product variations, variable product, share link
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Give every WooCommerce product variation its own URL. The link opens the product with the color, size, or other options already selected.

== Description ==

Variation URLs for WooCommerce gives each product variation a direct link. When a shopper picks a color, size, or any other option on a variable product, the address bar updates to a URL that opens the same product with the same options selected. Shoppers can share it, bookmark it, or send it to a friend, and you can use it in ads, emails, and social posts to link straight to a specific variation.

The links use the query arguments WooCommerce already understands, such as `?attribute_pa_color=blue&attribute_size=large`, so they keep working even if the plugin is turned off. There are no new pages, no rewrite rules, and nothing extra for search engines to index.

**Features**

* **Variation URL in the address bar:** the link updates as soon as a complete variation is selected and clears when the shopper resets the options.
* **Works with every attribute:** global attributes (Products → Attributes) and custom product attributes, including values with spaces, ampersands, and accents.
* **Back button still works:** the address is replaced, not added to the browser history, so Back leaves the product page as shoppers expect.
* **Copy link button:** an optional, accessible button below the variation form copies the link and confirms it for screen reader users. You can change its label or turn it off.
* **Links in the product editor:** each variation panel shows its ready-to-share link, handy for ads, emails, and support replies.
* **SEO safe:** the canonical URL stays on the main product page, so variation links never compete with it in search results.
* **Lightweight:** one small script, loaded on variable product pages only.

**Custom development**

Need variation swatches, separate SEO landing pages per variation, or a custom product configurator? The plugin is built and maintained by Ali Nawaz, a WordPress and WooCommerce developer. [Get in touch for custom work](https://bealinawaz.com/contact-me/?utm_source=wordpress.org&utm_medium=plugin-readme&utm_campaign=variation-urls-for-woocommerce).

== Installation ==

1. Install and activate WooCommerce, then this plugin.
2. Open any variable product and choose its options. The address bar now shows the variation link.
3. Optional: go to **WooCommerce → Settings → Products → Variation links** to change the Copy link button label or turn the button off.

== Frequently Asked Questions ==

= How do I link directly to a product variation in WooCommerce? =

Open the product, choose the options, and copy the address from the browser (or use the Copy link button). You can also copy the link from the variation's panel under **Product data → Variations** on the product edit screen.

= What does a variation URL look like? =

The product URL plus one query argument per attribute, for example `https://example.com/product/t-shirt/?attribute_pa_color=blue&attribute_size=Large`. Global attributes use the `pa_` prefix and the term slug; custom attributes use the attribute name and the value as you entered it.

= Will variation links create duplicate content? =

No. WordPress and SEO plugins such as Yoast SEO and Rank Math print a canonical URL without query arguments, so search engines treat every variation link as the main product page. The plugin does not change the canonical URL.

= Do the links work with variation swatches plugins? =

Usually, yes. Most swatches plugins keep WooCommerce's variation form and update its fields behind the scenes, which is what this plugin reads. If your theme or plugin replaces the variation form entirely, get in touch.

= Why does a variation in the product editor have no link? =

One of its attributes is set to "Any", so there is no single value to put in the link. Shoppers who pick a value for that attribute still get a full link in the address bar.

= What happened to the old /product/name/variation/ links? =

Version 1.0 used a custom URL format that did not work reliably. Version 2.0 removes it and uses WooCommerce's own format. Old links now show a "not found" page, so replace any you have shared with the new links.

= Does it work with the block editor product templates? =

Yes, as long as the product page uses WooCommerce's standard variation form, which the Single Product template in block themes such as Twenty Twenty-Five does.

== Changelog ==

= 2.0.0 =
* Complete rewrite.
* New: uses WooCommerce's own `attribute_` query arguments, so links preselect the variation with no custom rewrite rules.
* New: Copy link button with a screen reader status message, plus a setting for its label.
* New: a shareable link in each variation panel on the product edit screen.
* New: settings under WooCommerce → Settings → Products → Variation links.
* Fixed: broken URLs with repeated question marks when a product had more than one attribute.
* Fixed: each selection added a browser history entry, so Back no longer left the page.
* Fixed: custom (non-global) attributes and values with spaces or special characters.
* Removed: the /product/name/variation/ rewrite rule. Rewrite rules are flushed on update.
* Declares compatibility with WooCommerce High-Performance Order Storage and the block-based cart and checkout.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 2.0.0 =
Complete rewrite. Variation links now use WooCommerce's ?attribute_ format, the old /product/name/variation/ URLs are removed, and a Copy link button is added.
