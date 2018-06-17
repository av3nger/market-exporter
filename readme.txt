 === Market Exporter ===
Contributors: vanyukov
Donate link: http://yasobe.ru/na/market_exporter
Tags: market, export, yml, woocommerce, yandex market 
Requires at least: 4.7.3
Tested up to: 4.9.6
Stable tag: 1.0.4
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Плагин для экспорта товарных предложений из WooCommerce в YML файл для Яндекс Маркет.

== Description ==

Если Вы используете WooCommerce и хотите экспортировать все Ваши товары в Яндекс Маркет, то этот плагин однозначно для Вас! Market Exporter предоставляет возможность создавать файлы YML для экспорта товаров в Яндекс Маркет.

Плагин находится в активной разработке. На данный момент поддерживается только упрощенный тип описания для экспортированного списка товарных предложений (т.е. выгружаются следующие поля: название, описание, цена, категория и изображение). Большой упор сделан на соответствие требованиям Яндекс Маркет. Поддерживаются пять валют: рубль, белорусский рубль, гривна, доллар и евро.

Я собираю отзывы и предложения о том какой функционал Вы хотите видеть в плагине.

== Installation ==

1. Загрузите 'Market Exporter' в папку с плагинами на Вашем сайте WordPress (/wp-content/plugins/).
2. Активируйте 'Market Exporter' через раздел 'Плагины' в WordPress.
3. Выберите 'Market Exporter' в разделе 'WooCommerce' в WordPress.
4. Нажмите кнопку 'Генерировать YML файл'.

== Frequently Asked Questions ==

= Какие типы валют поддерживаются плагином? =

Данные о ценах принимаются в рублях (RUR, RUB), белорусских рублей (BYN), гривнах (UAH), долларах (USD) и евро (EUR). На данный момент в WooCommerce не реализована поддержка тенге (KZT), так что плагин их тоже не поддерживает. На Яндекс Маркете цены могут отображаться в рублях, гривнах, белорусских рублях и тенге в зависимости от региона пользователя.

В качестве основной валюты (для которой установлено rate="1") могут быть использованы только рубль (RUR, RUB) и гривна (UAH). Если в WooCommerce установлены доллары (USD) или евро (EUR), то используется курс Центрального Банка той страны, которая указана в настройках магазина на Яндекс Маркет. Применяется курс, установленный на текущий день. Курс обновляется ежедневно в 00.00.

= Как поменять настойки плагина? =

Настройки плагина можно осуществить на вкладке 'Настройки' в менюю 'WooCommerce' - 'Market Exporter'.

В настоящий момент поддерживаются следующие настройки:
* Изменение названия магазина и компании;
* Изменение количества изображений при экспорте товарных предложений;
* Использование произвольного поля для элементов vendor, model и typePrefix;
* Использование произвольного поля для элемента market_category;
* Использование произвольного поля для элементов delivery, pickup и store;
* А так же поддержка элементов: delivery-options, sales_notes, manufacturer_warranty, country_of_origin;
* Поддержка элементов param (включая габариты и вес);
* Поддержка товаров со статусом предзаказ;
* Выгрузка товаров по расписанию (cron), автоматическое пересоздание прайс-листа при обновлении товарных предложений;
* И другие полезные функции.

= Какие требования к WordPress, WooCommerce и оборудованию? =

Плагин был протестирован на последних версиях WordPress, но, скорее всего, он будет работать и на более старых версиях.

WooCommerce также тестировался на последних версиях.

Версия PHP должна быть не ниже 5.2. Полная поддержка версии 7.2.

== Screenshots ==

1. Скриншот главной страницы плагина.
2. Скриншот страницы настроек.
3. Скриншот страницы настроек.
4. Скриншот страницы настроек.

== Changelog ==

= 1.0.4 =
* NEW: Возможность задавать sales_notes для отдельных товаров (необходимо создать произвольное поле для товара с именем me_sales_notes, в каечестве значение указывается sales_notes для товара)
* NEW: Фильтр me_product_price для внесения изменений в цену товарного предложения
* NEW: Поддержка элемента stock_quantity
* NEW: Обновлены файлы переводов
* FIXED: Ошибка, когда не выгружались все рисунки

= 1.0.3 =
* NEW: Скачиваемые товары теперь выгружаются с элементом downloadable
* NEW: Добавлена поддержка delivery-options. Для отдельных товаров delivery-options можно задать через произвольные параметры me_do_cost, me_do_days и me_do_order_before
* CHANGED: Обновление select2 до версии 4.0.5
* FIXED: Ошибка при выгрузке параметров на старых версиях WooCommerce
* FIXED: Выгрузка изображений
* FIXED: Выгрузка товаров со статусом предзаказ

= 1.0.2 =
* ADDED: Возможность выгружать все параметры для товара
* FIXED: Невозможно удалить последний элемент в настройках при выборе категорий или параметров
* FIXED: Дублирующиейся изображения
* FIXED: typePrefix не выгружался у вариативного товара
* FIXED: Для вариативного товара выгружается нужный атрибут в param, а не все сразу

= 1.0.1 =
* FIXED: Исправлены ошибки при прохождении валидации на яндекс маркете
* FIXED: Не выгружалось краткое описание
* FIXED: Выбранные элементы param не отображались в настройках
* FIXED: Не удалялись категории из списка категорий
* FIXED: Плагин не всегда видел товары на сайте

= 1.0.0 =
* ADDED: Возможность устанавливать typePrefix для товаров
* ADDED: Возможность устанавливать manufacturer_warranty для товаров
* ADDED: Возможность устанавливать country_of_origin для товаров
* ADDED: Поддержка параметров: shop, pickup и delivery
* ADDED: Возможность изменять какое описание товара будет выгружаться в файл
* ADDED: Поддержка Тенге (KZT)
* ADDED: Поддержка выгрузки до 10 изображений на товарное предложение
* ADDED: Возможность автоматического обновления файла при изменении или создании товарных предложений
* CHANGED: Обнволение интерфейса
* CHANGED: Исправлены переводы
* CHANGED: Экспорт веса и габаритов в элементы weight и dimensions
* CHANGED: Библиотека select2 обновлена до последней версии
* FIXED: Улучшена поддержка PHP 5.2
* FIXED: Фильтр категорий не отображал категории глубже 2 уровня

= 0.4.4 =
* ADDED: Возможность устанавливать время крон.
* FIXED: При отсутствии изображения у варации, плагин попытается взять изображение основного товара.
* FIXED: Исправлены проблемы с крон, удалены лишние расписания.
* FIXED: Разные мелки исправления и улучшения.
* FIXED: Добавлены недростающие скрипты и стили select2.
* CHANGED: Скрпиты и стили загружаются только на страницах плагина, а не везде.
* CHANGED: Выгружать только актуальные категории в раздел categories.
* CHANGED: Обновлены переводы.

= 0.4.3 =
* FIXED: Поддержка для WordPress версии 4.7.3.
* FIXED: Чекбоксы в настройках отображают выбранные настройки.
* FIXED: Исправлена ошибка, когда Vendor и Model не выгружались у вариативных товаров.
* FIXED: Исправлена ошибка, из-за которой не выгружались габариты и вес у вариативного товара.
* FIXED: Исправлена ошибка с невозможностью выгрузить market_cateogry у вариативного товара.
* CHANGED: Улучшения в стилях CSS.

= 0.4.2 =
* FIXED: Исправлена оишбка при активации на PHP 5.5 и младше.

= 0.4.1 =
* FIXED: Исправлена ошибка, из-за которой не выгружались файлы из Woocommerce версии младше 3.0.0.
* CHANGED: По умолчанию поле sales_notes будет пустым и не будет содержать 'no' в качестве текста.
* CHANGED: Если задано описание вариации, то будет использовано оно вместо общего описания товара.
* CHANGED: У вариативных товаров выгружаются изображения вариаций, а не изображение главного товара.

= 0.4.0 =
* NEW: Добавлена поддержка тега model.
* NEW: Добавлена поддержка параметров (тег param).
* NEW: Добавлена поддержка белорусских рублей (BYN).
* NEW: Добавлена возможность выгружать вес и габариты (длина, ширина и высота) в качестве соответствующих параметров.
* FIXED: Исправлены ошибки с полями input.
* FIXED: Исправлена ошибка с невыгрузкой параметра vendor.
* FIXED: Исправлена ошибка, когда прайс-лист не создавался, если одна из вариаций товара была недоступна (например, ее нет в наличии).
* FIXED: Ссылки теперь генерируются согласно RFC.
* FIXED: Исправлена ошибка с неработающим крон.
* CHANGED: Вместо короткого описания, теперь в настройках плагина доступно отдельное поле для элемента sales_notes.
* CHANGED: По многочисленным просьбам, добавлена поддержка PHP 5.3.
* CHANGED: Переработан код для поддержки последних версий WooCommerce.

= 0.3.1 =
* FIXED: Официальный релиз 0.3.*

= 0.3.0 =
* NEW: Добавлена фильтрация выгрузки по категориям.
* FIXED: Исправлены ошибки с невозможностью экспорта импортированных товаров.
* FIXED: В названии товаров заменяются запрещенные символы на соответствующие коды.
* CHANGED: Настройки и генерация файла объеденены под одним пунктом меню. Теперь вся информация о плагине доступна в разделе WooCommerce - Market Exporter.
* CHANGED: Стили интерфейса придевены в соответствие общему стилю WordPress.

= 0.2.7 =
* FIXED: Исправлена работа Cron. Файлы, как и задумано, должны генерироваться раз в сутки.

= 0.2.6 =
* FIXED: Исправлены некоторые ошибки при работы с вариативными товарами. Остается ошибка с товарами, где вариации строятся по нескольким атрибутам.
* FIXED: Исправлены неточности в переводе.
* FIXED: Исправлена ссылка при генерации файла.

= 0.2.4 =
* FIXED: Исправлена ошибка при активации плагина на PHP 5.3.
* FIXED: Исправлены ошибки. Оптимизация кода. Обновлены переводы.

= 0.2.3 =
* CHANGED: Code optimization.

= 0.2.2 =
* NEW: Added support for cron. Now file will be automatically generated daily.
* CHANGED: Added CDATA to description.
* FIXED: Couldn't export on multisite installations.
* FIXED: Images didn't export correctly or sometimes didn't export at all.

= 0.2.0 =
* NEW: Added support for variable products.

= 0.1.0 =
* NEW: Added a list of generated files to the plugin main page. Files can be viewed or deleted.
* NEW: Added new option to enable or disable date at the end of the file name.
* NEW: Added support for products on backorder.
* FIXED: Issues with HTML tags and unsupported characters in description field.

= 0.0.7 =
* NEW: Now it's possible to select a custom attribute to be used as a 'vendor' property.
* NEW: Added support for custom element 'market_category'.
* NEW: Added support for custom element 'sales_notes'.
* CHANGED: Product field 'description' (previously 'short description') is now used as value in 'description' element.
* CHANGED: If 'discount price' is set for a product, old price will be exported to 'oldprice' element.
* CHANGED: Added current Russian translations.
* FIXED: Product 'description' element will not be set if product 'description' field is left blank.

= 0.0.6 =
* NEW: Added new option - 'Number of images'. Specify how many images to export per product.
* CHANGED: Code cleanup and optimization.
* FIXED: Image export... Again.

= 0.0.5 =
* NEW: Added support for the following currencies: RUB, UAH, USD and EUR.
* CHANGED: Export up to 10 product images.
* CHANGED: Use arrays for storing plugin options in DB instead of single values. Better for performance in the long run.
* CHANGED: Items out of stock will not be exported.
* CHANGED: Moved settings page to WooCommerce settings page under Products tab.
* FIXED: Image export.

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

= 0.4.2 =
Исправлена оишбка при активации на версиях PHP 5.5 и младше.

= 0.4.1 =
Исправлена ошибка с неработающей выгрузкой на WooCommerce версии 2.*.

= 0.3.0 =
Добавлена фильтрация по категориям. Переделан механизм экспорта. Более детально на (https://wordpress.org/plugins/market-exporter/changelog/).

= 0.2.2 =
Added daily cron task. Bugfixes.

= 0.2.0 =
Added support for variable products.

= 0.1.0 =
End of the year release. For a full list of changes refer to (https://wordpress.org/plugins/market-exporter/changelog/).

= 0.0.7 =
For a full list of changes refer to (https://wordpress.org/plugins/market-exporter/changelog/).

= 0.0.6 =
Bug fixes. New image options on settings page.

= 0.0.5 =
Now supports RUB, UAH, USD and EUR currencies. Export up to 10 product images. Items out of stock are not exported anymore. Fixed various bugs.

= 0.0.4 =
Fixed delivery price issues. Added support for flat rate shipping method. NAME and COMPANY fields now customizable.

= 0.0.3 =
Fixed various bugs.

= 0.0.2 =
Utilize SKU field as vendorCode in YML file. Hidden products no longer export. Full changelog can be found at (https://wordpress.org/plugins/market-exporter/changelog/).

= 0.0.1 =
Initial release of the plugin. Basic Yandex offer support.
