=== morkva UA Shipping – Nova Poshta, Ukrposhta, Rozetka, Nova Global ===
Contributors: bandido, dpmine
Tags: Nova Poshta, Нова Пошта, Укрпошта, доставка, Rozetka Delivery
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.15.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Nova Poshta, Ukrposhta, Rozetka and Nova Global in one plugin. Shipping labels (TTN) right from orders, block checkout, no third-party sign-up.

== Description ==

**morkva UA Shipping is WooCommerce shipping for stores in Ukraine.** Nova Poshta, Ukrposhta, Rozetka Delivery and Nova Global in one plugin: the customer picks a branch, a parcel locker or an address at checkout, and you create the shipping label (TTN) right from the order, without copying data into the carrier's account.

= 👉 Why stores choose morkva UA Shipping =

* **4 carriers in one plugin.** No need to install a separate plugin for each carrier and make them work together.
* **Works with the WooCommerce block checkout** (Checkout Blocks) and the classic checkout, and supports HPOS.
* **No sign-up on third-party platforms.** The plugin talks to the carriers' APIs directly with your own keys. Rozetka Delivery needs no API keys at all.
* **Checkout keeps working when the Nova Poshta API is down.** City and branch search switches to the morkva backup directory.
* **Shipping labels in the free version.** Create, print, delete, bulk actions, automatic status updates.
* **Support in Ukrainian.** Rated 4.9 from 29 reviews: users like our service.
* Compatible with WPML, Polylang, PHP 7.4–8.x, morkva plugins (Plata by Mono, LiqPay, Dilovod, KeyCRM, Checkbox) and TurboSMS.

[Documentation](https://docs.morkva.co.ua/uk/plugins) · [Pro version](https://morkva.co.ua/shop/morkva-ua-shipping/)

= 🛒 What your customers get =

* Separate shipping methods: Nova Poshta (branch, parcel locker, address, international to a branch or an address), Ukrposhta (branch, parcel locker, address; international — **Pro**), Rozetka Delivery (branch), Nova Global (address)
* Fast city and branch search, including search by branch number
* Street autocomplete for address delivery
* Only branches and parcel lockers that accept a parcel of this weight are shown
* Delivery details are remembered for registered customers
* Shipping label number in "My account" → Orders
* Latin-only name check for international shipments
* Works with the standard Local pickup method (hides unneeded fields automatically)

= 📦 What your store gets =

**💰 Shipping prices**

* Within Ukraine: flat rate or free shipping
* A separate free shipping threshold for each method
* Nova Poshta international: price calculated via the Nova Poshta API
* Nova Global: price by weight (or volumetric weight) from a built-in rate table
* Price within Ukraine calculated from the weight and dimensions of the products — **Pro**

**🚚 Nova Poshta shipping labels**

* Create from the order page or right from the orders list
* Sender: individual, sole proprietor (FOP) or company (TOV) — picked from your Nova Poshta account; several contact persons
* Warehouse–warehouse, warehouse–door, door–warehouse, parcel lockers
* Cargo types: parcels, documents, tires/wheels, pallets (tire type and document weight are set on the product)
* Number of seats, automatic or one global shipment description
* Change the city and branch after the order is placed
* Prepayment (if you use morkva payment plugins) is deducted from the declared value automatically — **Pro**
* Attach shipping labels created manually — **Pro**
* Shipping label is passed to your CRM (with morkva plugins) — **Pro**
* Cash on delivery and payment control — **Pro**
* Nova Poshta international and Nova Global shipping labels — **Pro**

**✉️ Ukrposhta shipping labels**

* Create from the order page or from the orders list
* STANDARD or EXPRESS, warehouse–warehouse and warehouse–door
* Cash on delivery
* Sender: individual (company — **Pro**)
* International shipments: HS code, shipment type, CP71, CN22, TFP3 forms — **Pro**

**📍 After shipping**

* Print shipping labels and stickers, bulk print and delete (Ukrposhta bulk print — **Pro**)
* Automatic Nova Poshta status check for a period you choose (30 days by default; Ukrposhta statuses — **Pro**)
* Order status changes automatically based on the shipping label status

**⚙️ Automation — Pro**

* Automatic shipping label creation by rules (order status + payment method)
* Email to the customer with the shipping label number
* Cash on delivery payment → label with cash on delivery automatically
* Change who pays for shipping depending on the order total

= ⭐ Free or Pro =

**Free version** — shipping methods for 4 carriers, manual shipping labels within Ukraine, printing, Nova Poshta statuses.

**Pro** adds: automatic shipping labels, Nova Poshta cash on delivery and payment control, automatic prepayment deduction, price calculation within Ukraine by weight and dimensions, Ukrposhta international, Ukrposhta company sender, international labels (Nova Poshta, Ukrposhta, Nova Global), Ukrposhta statuses, email with the label number, attaching manually created labels, passing labels to CRM.

[Pro version pricing →](https://morkva.co.ua/shop/morkva-ua-shipping/)

= 🛠️ For developers =

Hooks: `mrkv_ua_shipping_settings_page_row`, `mrkv_ua_shipping_option_serialize`, `mrkv_ua_shipping_ttn_create_row`, `mrkv_ua_shipping_popup_settings`, `mrkv_ua_shipping_invoice_settings`, `mrkv_invoice_statuses_args`, `mrkv_ua_shipping_np_proxy_url`.

= 🔗 External services =

The plugin connects to third-party services only to deliver orders. All requests are sent from your site's server, not from the customer's browser.

* **Nova Poshta API** (api.novaposhta.ua) — city, branch and street search at checkout; creating, deleting and tracking shipping labels. Sent: your API key, the search text and, when a label is created, sender and recipient details (name, phone, address or branch), weight and value. Labels and stickers are printed via my.novaposhta.ua. [Terms of use](https://novaposhta.ua/more/offer), [privacy policy](https://novaposhta.ua/more/privacy-policy).
* **Nova Post API** (api.novapost.com, api.novaposhta.international) — Nova Poshta international and Nova Global: branches abroad, price calculation, statuses. Sent: your API key, the destination country and branch, cart weight and dimensions. [Privacy policy](https://novapost.com/en-pl/more/privacy-policy).
* **Ukrposhta API** (www.ukrposhta.ua) — city, branch and street search; creating and printing shipping labels. Sent: your API keys, the search text and shipment details. [Privacy policy](https://www.ukrposhta.ua/ua/polityka-konfidentsiinosti).
* **Rozetka Delivery API** (rz-delivery.rozetka.ua) — city and branch search. Sent: the search text. [Privacy policy](https://rozetka.com.ua/pages/privacy/).
* **morkva backup directory** (np.morkva.co.ua) — only when the Nova Poshta API key is missing or inactive, or the Nova Poshta API does not respond. Sent: the city or branch search text, the Nova Poshta city ID, filters, the plugin version and your site address. Your API keys and customer data are not sent. Request logs are kept for 90 days. [Privacy policy](https://morkva.co.ua/privacy-policy).

== Installation ==

1. Install and activate the plugin. If you used another Nova Poshta or Ukrposhta shipping plugin, deactivate it.
2. In the plugin settings, turn on the carriers you need.
3. Enter the API keys for each carrier (Rozetka Delivery needs none).
4. In WooCommerce → Settings → Shipping, add the shipping methods to your Ukraine shipping zone.
5. Check that the shipping methods and fields appear at checkout. Questions — support@morkva.co.ua

https://youtu.be/GNPhDFcuM-Q

Pro version overview:

https://youtu.be/47-VrH_QJAE

== Frequently Asked Questions ==

= Where do I get the API keys? =

Nova Poshta: in your Nova Poshta account, Settings → Security → API. Ukrposhta: sign a contract with Ukrposhta (it can be done online); after that Ukrposhta issues three keys (PRODUCTION BEARER eCom, BEARER StatusTracking, COUNTERPARTY TOKEN), see [Ukrposhta for business](https://ukrposhta.ua/ukrposhta-dlya-biznesu.html). Rozetka Delivery works without API keys.

= How is the shipping price calculated? =

Within Ukraine: a flat rate or free shipping above an order total (the threshold is set for each method separately). Nova Poshta international: calculated via the Nova Poshta API. Nova Global: from a built-in rate table by weight. Calculation within Ukraine by product weight and dimensions is available in Pro.

= Where do weight and dimensions come from if the product has none? =

For a shipping label the plugin uses, in this order: the value you enter in the label form; the total weight of the order's products; the default weight from the carrier settings (Nova Poshta: 0.5 kg if not set). Nova Poshta dimensions work the same way (form → product dimensions → defaults, 10 cm if not set). The plugin does not change weight or dimensions in your products.

= Does it work with the block checkout? =

Yes, since version 1.11. Tested with Flatsome, Woodmart, Hello Elementor, Blocksy and Storefront, and it should work with any theme. No special setup is needed: in the plugin's Global settings, choose how to display the extra fields.

= What happens if the Nova Poshta API is down? =

City and branch search at checkout switches to the morkva backup directory, so customers can still place orders.

= How do I show branches without parcel lockers? Why are closed branches shown? =

In the "Branch" shipping method settings you can remove parcel lockers from the list (and offer them as a separate "Parcel locker" method). The branch list comes from the Nova Poshta API, so a branch that Nova Poshta has not yet removed from its directory is shown too. Only branches that accept a parcel of this weight are shown.

= Does the order status change automatically? =

Yes, based on the shipping label status. You choose how many days back the statuses are checked (30 days by default).

= Can the customer see the shipping label number? =

Yes, in "My account" → Orders. An email with the label number is available in Pro.

= Does it work with cash on delivery? =

The plugin recognizes the standard "Cash on delivery" payment method as cash on delivery. Nova Poshta labels with cash on delivery are available in [Pro](https://morkva.co.ua/shop/morkva-ua-shipping/). Ukrposhta cash on delivery works in the free version.

= Can shipping labels be created automatically? =

Yes, in [Pro](https://morkva.co.ua/shop/morkva-ua-shipping/): by rules based on the order status and payment method.

= Is the "Patronymic" field required? =

You turn the field on in the carrier's checkout settings, where you can also make it required and choose its position. For Ukrposhta branch delivery with cash on delivery the field is always shown, as Ukrposhta requires it.

= Are cities and branches available in English? Does it work with WPML and Polylang? =

City and branch names within Ukraine (Nova Poshta, Ukrposhta) are shown in Ukrainian, as the carriers provide them. Nova Poshta international and Nova Global show English or Ukrainian depending on the site language. The plugin interface can be translated with Loco Translate, Polylang or WPML.

= Does it support HPOS? =

Yes.

= Can I ship as a sole proprietor (FOP) or a company (TOV)? =

Yes, for Nova Poshta: pick the sender counterparty from the list in your Nova Poshta account.

== Screenshots ==

1. Turning on the carriers you need
2. Nova Poshta API key settings
3. Nova Poshta sender settings
4. Default values settings
5. Settings for the email with the Nova Poshta shipping label
6. Automation settings (Pro only)
7. Checkout field placement
8. Nova Poshta debug log
9. WooCommerce shipping methods
10. Shipping method settings
11. Ukrposhta settings within Ukraine
12. Ukrposhta international sender settings (Pro only)

== Changelog ==

= 1.15.4 =
* [ui] оновили налаштування

= 1.15.3 =
* [fix] виправили перевірку статусу Нова Глобал 

= 1.15.2 =
* [new] повернули фільтр по вазі при пошуку відділень/поштоматів Нової Пошти
* [new] оптимізували виклики БД у плагіні

= 1.15.1 =
* [new] додали спосіб доставки УП Поштамат
* [new] додали вилучення поштаматів з відділень УП
* [fix] виправили налаштування відправника

= 1.15.0 =
* [new] об'єднали вкладки «Базові» та «Відправник» Нової Пошти: API ключ → тип відправника (фізична / юридична особа) → контактна особа → місце відправки
* [new] вибір контрагента-відправника (приватна особа, ФОП, ТОВ) зі списку API замість першого з кабінету
* [new] зберегти можна лише API ключ або всі дані відправника; при незавершеному заповненні — попередження
* [fix] телефон і ПІБ відправника тепер відповідають обраній контактній особі
* [fix] списки відправників кешуються, сторінка налаштувань не звертається до API Нової Пошти при кожному відкритті

= 1.14.3 =
* [fix] виправили вивід назви безкоштовної доставки

= 1.14.2 =
* [new] додали маскування API у логах
* [fix] виправили рахунок накладних
* [fix] виправили warning змінних

= 1.14.1 =
* [new] оптимізували запит перевірки активного ключа АПІ НП
* [fix] виправили збереження полів на блочному чекауті
* [fix] виправили вивід помилки налаштувань НП
* [fix] виправили перевірку типу чекауту

= 1.14.0 =
* [new] додали власний резервний сервер з довідниками міст та відділень/поштоматів Нової Пошти
* [new] оптимізували запити для великих міст
* [fix] виправили помилку ліцензії 

= 1.13.3 =
* [new] оптимізували запити до оффлайн НП

= 1.13.2 =
* [fix] виправили оффлайн завантаження полів НП 
* [fix] виправили warnings
* [fix] виправили налаштування по-батькові НП

= 1.13.1 =
* [new] додали новий статус ТТН НП Знищено внаслідок ворожої атаки
* [fix] виправили крон оновлення статусів ТТН

= 1.13.0 =
* [new] оптимізували налаштування
* [new] оптимізували перевірку ключів API НП
* [fix] виправили оновлення статусів ТТН НП HPOS
* [fix] виправили переклад

= 1.12.3 =
* [new] додали вивід напису за тарифами перевізника у оновленому шаблоні email Woocommerce 

= 1.12.2 =
* [hotfix] виправили помилку класу Rozetka

= 1.12.1 =
* [new] прибрали вилучення поштоматів з переліку відділень НП
* [new] оптимізували кешування запитів
* [new] додали автоматичне завантаження файлів
* [new] оптимізували завантаження файлів плагіну
* [new] оптимізували файли стилів та функцій

= 1.12.0 =
* [new] додали перевірку спеціальних символів при пошуку відділень та міст
* [new] змінили посилання на відео API ключа НП
* [new] додали запис ref відділення Rozetka
* [fix] виправили розрахунок вартості доставки
* [fix] виправили завантаження стилів select2 для блочного чекаута

= 1.11.5 =
* [new] додали активацію служби доставки при першому збереженні апі в налаштуваннях

= 1.11.4 =
* [new] оновили UI налаштувань НП
* [fix] виправили визначення ваги для вантажних відділень НП
* [fix] виправили вантаження вантажних відділень НП

= 1.11.3 =
* [fix] виправили вагу доставки Укрпошта

= 1.11.2 =
* [fix] виправили вивід мета полів у адресах акаунта

= 1.11.1 =
* [fix] виправили помилку налаштувань

= 1.11.0 =
* [new] додали підтримку WooCommerce Checkout Blocks

= 1.10.5 =
* [fix] виправили розрахунок вартості доставки

= 1.10.4 =
* [fix] виправили пошук відділень НП за номером

= 1.10.3 =
* [fix] виправили вивід попапу створення ТТН на сторінці замовлень
* [fix] виправили warning функції ID

= 1.10.2 =
* [new] змінили метод отримання PDF накладної УП
* [new] змінили метод завантаження відділень НП
* [new] переробили метод збереження полів користувача
* [fix] виправили отримання відділень НП Міжнародна
* [fix] виправили створення замовлення з увімкнутим створенням користувача
* WP 7.0 - сумісний

= 1.10.1 =
* [hotfix] виправили помилку option fields

= 1.10.0 =
* [new] оптимізували запити

= 1.9.3 =
* [fix] виправили форматування файлу замовлення

= 1.9.2 =
* [new] додали друк накладних на сторінці замовлень
* [fix] виправили помилку sender does not belong to this address

= 1.9.1 =
* [new] додали перевірку адреси відправника в налаштуваннях

= 1.9.0 =
* [new] змінили UI налаштувань на сторінці товару

= 1.8.8 =
* [new] додали перевірку сторінки розрахунку вартості доставки

= 1.8.7 =
* [fix] виправили перевірку опису створення ТТН

= 1.8.6 =
* [new] змінили UI налаштувань

= 1.8.5 =
* [new] додано хук налаштувань mrkv_ua_shipping_settings_page_row
* [new] додано хук перевірки полів налашутвань mrkv_ua_shipping_option_serialize
* [new] додано хук форми створення ТТН mrkv_ua_shipping_ttn_create_row
* [new] додано хук налаштувань форми створення ТТН mrkv_ua_shipping_popup_settings
* [new] додано хук налаштувань при створенні запиту mrkv_ua_shipping_invoice_settings

= 1.8.4 =
* [new] змінили UI налаштувань НП

= 1.8.3 =
* [new] додали автоматичну зміну статусу замовлення згідно отриманого статусу ТТН
* [new] додали фільтр отримання статусів доставки mrkv_invoice_statuses_args

= 1.8.2 =
* [fix] виправили налаштування класів доставки

= 1.8.1 =
* [new] поле По-батькові УП обов'язкове для післяплати
* [new] відключили автозаповнення адреси браузером

= 1.8.0 =
* [new] додали новий тип відправлення Шини/диски НП
* [new] приховали Тип вантажу "Палети" НП
* [new] додали вибір ваги товару типу Documents НП
* [fix] виправили створення ТТН типу Documents НП
* [ui] змінили стилі сторінки налаштувань

= 1.7.10 =
* [fix] виправили Stored Cross-Site Scripting
* [fix] виправили запит отримання відділень Rozetka Delivery

= 1.7.9 =
* [fix] виправили weight warning

= 1.7.8 =
* [fix] виправили текст пошуку відділень НП по номеру

= 1.7.7 =
* [fix] виправили налаштування відправника
* [fix] виправили помилку TypeError get_api_key_active при збої API Нової пошти

= 1.7.6 =
* [fix] виправили поля доставки при створені замовлення в адмінці

= 1.7.5 =
* [new] додали валідацію опису при створенні ТТН
* WP 6.9 - сумісний

= 1.7.4 =
* [fix] виправили призначення методу на замовлення

= 1.7.3 =
* [new] зробили недоступним віджет зміни методу доставки поки замовлення не буде створене
* [fix] вулиці вантажаться раніше загального спінера вукомерс
* [fix] додати оновлення по-батькові УП при існуючому клієнті у БД УП
* [ui] Нова Пошта - зробити Select2 якщо обрано пошук лише за номером

= 1.7.2 =
* [new] змінили поля адресної доставки НП

= 1.7.1 =
* [new] додали завантаження існуючих клієнтів УП

= 1.7.0 =
* [new] додали міжнародну доставку Нова Пошта на адресу та у відділення Nova Post

= 1.6.7 =
* [new] додали перевірку ПІБ на латинські літери
* [fix] виправили очищення логу

= 1.6.6 =
* [fix] виправили обробку полів чекаута при відсутності методів доставки

= 1.6.5 =
* [fix] виправили поля форми створення ТТН УП

= 1.6.4 =
* [fix] виправили налаштування способів доставки

= 1.6.3 =
* [fix] виправили завантаження міст та відділень Rozetka Delivery

= 1.6.2 =
* [fix] виправили налаштування ваги
* PHP 8.4 compatible

= 1.6.1 =
* [new] додали вивід вартості доставки Nova Global у кошик

= 1.6.0 =
* WooCommerce 10.0 tested
* PHP 8.4 compatible

= 1.5.3 =
* [fix] виправили warnings

= 1.5.2 =
* [fix] виправили завантаження відділень

= 1.5.1 =
* [new] Після створення ТТН сторінка замовлення перезавантажується автоматично

= 1.5.0 =
* [new] додали оффлайн завантаження відділень НП
* [new] оновлення тарифів Nova Global з 12.06

= 1.4.2 =
* [fix] виправили метод отримання відділень Rozetka Delivery

= 1.4.1 =
* [new] додали валідацію опису
* [new] додали валідацію імені та прізвища на латинські літери

= 1.4.0 =
* [dev] НП: змінили підхід роботи select2 поля: збільшили затримку перед відправленням запиту до API на пошук міста (виправляє помилку 429 too many requests)

= 1.3.1 =
* [fix] виправили помилку По-батькові

= 1.3.0 =
* [new] додали спосіб доставки Nova Global
* [dev] оптимізували роботу Нової Пошти

= 1.2.9 =
* [fix] виправили метод додавання способу доставки
* WP 6.8 - сумісний
* WooCommerce 9.8 - сумісний

= 1.2.8 =
* [new] додали можливість скопіювати ТТН

= 1.2.7 =
* [fix] виправили текст пошуку select2

= 1.2.6 =
* [fix] виправили фільтр відділень по вазі НП

= 1.2.5 =
* [new] додали налаштування врахування промокодів

= 1.2.4 =
* [fix] виправили помилку завантаження методів

= 1.2.3 =
* [fix] виправили помилку завантаження полів Оформлення замовлення
* [dev] fix for WP 6.7 _load_textdomain_just_in_time

= 1.2.2 =
* [dev] прибрали помилково додану можливість створення ТТН від імені юридичних осіб

= 1.2.1 =
* [fix] виправили завантаження полів НП

= 1.2.0 =
* [new] додали перехід на сайт НП для трекінгу на сторінці замовлень
* [new] оновили доставки УП по Україні

= 1.1.1 =
* [new] додали кнопки друку на сторінці результату створення ТТН

= 1.1.0 =
* [new] додали новий спосіб доставки Rozetka Delivery
* [new] додали механізм перевірки ключа API Нової Пошти

= 1.0.27 =
* [fix] виправили переклад налаштувань

= 1.0.26 =
* [new] додали можливість отримати післяплату при отриманні
* [fix] виправили друк ТТН Укрпошта

= 1.0.25 =
* [new] додали налаштування зміни позиції По-батькові Укрпошта
* [fix] виправили вивід ттн на сторінці акаунту 
* [fix] виправили переклад налаштувань
* [fix] відключили підвантаження даних browser autocomplete

= 1.0.24 =
* [fix] виправили конвертацію опису ттн

= 1.0.23 =
* [fix] виправили блок зміни способу доставки
* [fix] виправили переклад полів

= 1.0.22 =
* [new] виправили збереження полів доставки
* [fix] виправили завантаження ТТН УП
* [new] додали підтримку Mono Prepay

= 1.0.21 =
* [new] зробили поле номер квартири УП не обов'язковим
* [new] оновили вивід міст НП
* [new] додали вивід ТТН та статуса доставки на сторінці замовлень клієнта

= 1.0.20 =
* [new] додали параметри розміру відправлення для Укрпошти
* [dev] додали відсутні переклади
* [fix] виправили завантаження поштоматів

= 1.0.19 =
* [new] додали видалення ТТН у кабінеті Нова Пошта
* [fix] виправили розміри УкрПошти

= 1.0.18 =
* [fix] виправили завантаження полів доставки 

= 1.0.17 =
* [new] додали налаштування відключення збереження даних адреси
* [fix] виправили переклад

= 1.0.16 =
* [new] зміни UI
* [fix] виправили дані ваги

= 1.0.15 =
* WP 6.7 - сумісний
* WooCommerce 9.4 - сумісний

= 1.0.14 =
* [new] зміни UI
* [new] додали налаштування по-батькові для УП
* [fix] виправили переклад

= 1.0.13 =
* [fix] виправили завантаження скриптів autocomplete

= 1.0.12 =
* [new] додали метабокс зміни способа доставки
* [fix] виправили символи

= 1.0.11 =
* [new] внесли зміни UI
* [fix] виправили перевірку символів

= 1.0.10 =
* [new] додали перевірку символів
* [new] додали заборону впливу на приховані поля
* [new] змінили крок у числових полях

= 1.0.9 =
* [new] додали налаштування пошуку відділення за номером
* [fix] виправили пусте поле відділення

= 1.0.8 =
* [new] зробили поле квартира НП не обов'язковим
* [fix] виправили переклад placeholder street
* [fix] виправили сумістність із floating field labels
* [fix] виправили завантаження відділень

= 1.0.7 =
* [fix] виправили переклад placeholder checkout
* [fix] виправили checkout select2 opening

= 1.0.6 =
* [fix] виправили вивід адреси НП

= 1.0.5 =
* [fix] виправили отримання НП address_ref
* [fix] виправили контроль оплати

= 1.0.4 =
* [fix] виправили обов'язкові поля shipping

= 1.0.3 =
* [fix] виправили контроль оплати
* [new] додали перевірку номера телефону без 380

= 1.0.2 =
* [fix] виправили створення ТТН НП NonCash

= 1.0.1 =
* [fix] виправили створення ТТН Нової Пошти

1.0.0 - перший реліз плагіна
