# Variation URLs for WooCommerce

Give every WooCommerce product variation its own shareable URL. When a shopper picks options on a variable product, the address bar updates to a link that opens the same product with the same options selected, ready to share, bookmark, or use in ads and emails.

![Tested up to WordPress 7.1](https://img.shields.io/badge/WordPress-7.1-blue)
![WooCommerce 7.1+](https://img.shields.io/badge/WooCommerce-7.1%2B-7f54b3)
![PHP 7.4 to 8.5](https://img.shields.io/badge/PHP-7.4%E2%80%938.5-777bb4)
![License GPLv2 or later](https://img.shields.io/badge/license-GPLv2%2B-green)

The links use WooCommerce's own preselection arguments (`?attribute_pa_color=blue&attribute_size=Large`), so there are no rewrite rules, and the links keep working even without the plugin.

## Features

- **Live variation URL:** updated with `history.replaceState` when a complete variation is chosen, cleared on reset. Back still leaves the page.
- **All attribute types:** global (`pa_`) and custom attributes, with values containing spaces or special characters encoded correctly.
- **Copy link button:** optional, keyboard accessible, with a `role="status"` confirmation message. Label configurable.
- **Product editor links:** each variation panel shows its direct link.
- **SEO safe:** the canonical URL stays the plain product permalink.
- **Small footprint:** one script, loaded on variable product pages only. Compatible with HPOS and the block cart and checkout.

## Setup

1. Install WooCommerce and activate the plugin.
2. Settings: **WooCommerce → Settings → Products → Variation links** (Copy link button on or off, button label).

## How it works

WooCommerce's `wc_dropdown_variation_attribute_options()` reads `$_REQUEST['attribute_{name}']` to preselect each attribute when the product page loads. The plugin's script listens to WooCommerce's `found_variation` and `reset_data` events on `form.variations_form` and writes the selected attribute fields into the address bar. Other query arguments, such as UTM tags, are kept. The address bar is only changed after the shopper picks an option, so a product's default selection does not add arguments on its own.

## Hooks

- `vurl_load_assets` (filter, bool): load the script on pages other than single variable product pages, for example a quick view.

## Project structure

```
variation-urls-for-woocommerce.php     Plugin header, bootstrap, WooCommerce compatibility
includes/class-vurl-settings.php       Options and upgrade routine
includes/class-vurl-frontend.php       Product page script and Copy link button
includes/class-vurl-admin.php          Settings section, plugin links, variation panel links
assets/frontend.js                     Address bar and copy behavior
assets/frontend.css                    Copy link button layout
assets/admin.css                       Admin styles
uninstall.php                          Removes the options
readme.txt                             WordPress.org readme
```

## Custom development

Need variation swatches, SEO landing pages per variation, or a custom product configurator? The plugin is built and maintained by [Ali Nawaz](https://bealinawaz.com/?utm_source=github&utm_medium=readme&utm_campaign=variation-urls-for-woocommerce), a WordPress and WooCommerce developer. [Get in touch for custom work](https://bealinawaz.com/contact-me/?utm_source=github&utm_medium=readme&utm_campaign=variation-urls-for-woocommerce).

## License

GPLv2 or later. See [LICENSE](LICENSE).
