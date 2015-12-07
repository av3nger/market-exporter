=== Market Exporter ===
Contributors: vanyukov
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=a%2evanyukov%40testor%2eru&lc=RU&item_name=Plugin%20Support&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest
Tags: market, export, yml, woocommerce, yandex market 
Requires at least: 4.0.0
Tested up to: 4.3.1
Stable tag: 0.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Market Exporter provides an easy way to export products from WooCommerce installations into a YML file for use in Yandex Market.

== Description ==

**Описание на русском**

Если Вы используете WooCommerce и хотите экспортировать все Ваши товары в Яндекс Маркет, то этот плагин однозначно для Вас! Market Exporter предоставляет возможность создавать файлы YML для экспорта товаров в Яндекс Маркет.

Плагин находится в активной разработке и это лишь первая версия, которая поддерживает только упрощенный тип описания для экспортированного списка товарных предложений (т.е. выгружаются следующие поля: название, описание, цена, категория и изображение).

Я собираю отзывы и предложения о том какой функционал Вы хотите видеть в плагине.

**Description in English**

Are you using WooCommerce and want to export all your products to Yandex Market? Then this plugin is definitely for you! Market Exporter gives you the ability to create a valid YML file for Yandex Market.

This is the first release of the plugin. For now it can export only using the basic offer format which includes these product fields: title, description, price, url, picture. It supports unlimited amount of categories and subcategories.

== Installation ==

1. Upload 'Market Exporter' plugin to your WordPress website (`/wp-content/plugins/`).
2. Activate 'Market Exporter' through the 'Plugins' menu in WordPress.
3. Select 'Market Exporter' under the 'Options' menu in WordPress.
4. Click on 'Generate YML file' button.

That's it! After the export process completes, you will get a link to the YML file which you should upload to Yandex Market.

== Frequently Asked Questions ==

= Does this plugin work with newest WP version and also older versions? =

The plugin should work with any version of WordPress, because it mainly takes data from WooCommerce. The latest version of the plugin has been tested with the latest version of WooCommerce.

= What product fields does the plugin support? =

For now it is possible to export using basic Yandex offer format. The next product fields will be exported: title, description, price, url, category and picture.

= What themes can I use with this plugin? =

Market Explorer is theme independent. You can use it with any theme you want.

= Will other Yandex formats be supported? =

Yes.

== Screenshots ==

1. Screenshot of the plugin main page.
2. Screenshot of the setup page.

== Changelog ==

= 0.0.4 =
* NEW: Flat rate shipping support. Plugin first checks if local delivery is enabled. If not - get the price of flat rate shipping.
* NEW: NAME and COMPANY fields are now customizable.
* FIXED: Remove all HTML tags on all text fields in YML file.

= 0.0.3 =
* FIXED: Bugfixes.

= 0.0.2 =
* NEW: YML generation: products with status 'hidden' are not exported.
* NEW: YML generation: use SKU field as vendorCode.
* CHANGED: Optimized run_plugin()
* CHANGED: Export YML to market-exporter/ directory in uploads/ (previously was the YYYY/mm directory), so we don't get a lot of YML files after a period of time.
* FIXED: Language translation.

= 0.0.1 =
* Initial release.

== Upgrade Notice ==

= 0.0.4 =
Fixed delivery price issues. Added support for flat rate shipping method. NAME and COMPANY fields now customizable.

= 0.0.3 =
Fixed various bugs.

= 0.0.2 =
Utilize SKU field as vendorCode in YML file. Hidden products no longer export. Full changelog can be found at (https://wordpress.org/plugins/market-exporter/changelog/).

= 0.0.1 =
Initial release of the plugin. Basic Yandex offer support.