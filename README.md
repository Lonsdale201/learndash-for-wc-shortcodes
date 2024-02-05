# learndash-for-wc-shortcodes
Apró kiegészítő a Learndash és WooCommerce-hez

## Bevezető

A LearnDash ugyan a hivatalos kiegészítő lévén kompatibilis a WooCommerce-el, tehát vásárlást követően a hozzáférést beállíthatod, azonban a termék direkt megjelenítése a kurzus oldalon nehézkes.

Ez az apró Shortcode alapú paraméterezhető bővítmény egy exrta lehetőséget biztos ahhoz, hogy összekösd a terméket és a kurzust, amit a kurzus single oldalán megjeleníthetsz, így a látogató a kurzust a kosárhoz adhatja 
anélkül, hogy külön elnavigáljon a termék oldalra. A rendszer érzékeli ha a felhasználónak van már hozzáférése a kurzushoz, ilyenkor a kosrához adás gomb már nem jelenik meg. Kezeli ha nincs már készleten, és nem engedélyez csak egy termék hozzáaádását az adott kurzus termékből. A hibaüezenetet a kosár oldalon fogja megjeleníteni ha többször szeretnék hozzáadni. A gomb nem kap letiltást ha már kosárban van. Ha ajax alapon törli  a terméket a kosárból, kell egy page refresh hogy a gomb visszaálljon, de ha időközben ismét lekattintják, ugyan úgy kosárba tudja rakni. (EZ NEM BUG? NEM, EZ FÍCSÖR)

Ha szeretnéd felülírni, hogy ne csak egyet lehessen hozzáadni (bár nincs number input, és a felirat sem fog változni, használhatod a következőt):

`function custom_remove_ld_wc_filter() {
    remove_filter('woocommerce_add_to_cart_validation', 'ld_wc_only_one_course_in_cart', 10);
}
add_action('init', 'custom_remove_ld_wc_filter');`


Ez a kiegészítő egy saját projekthez kellett. Amennyiben szükséged van neked is hasonlóra, használd egészséggel.

![image](https://github.com/Lonsdale201/learndash-for-wc-shortcodes/assets/23199033/53652489-78ca-47df-b6ba-15c5cb58ee11)

Egyéb extra beállításokhoz navigálj a **WooCommerce -** **- LearnDash Extras** menöpontra

**Tesztelve:**

* PHP 8.1
* WooCommerce: 8.3
* LearnDash: 4.9.1

> [!IMPORTANT]
> Ez a bővítmény nem kompatibilis a WPML vagy egyéb fordító bővítményekkel...Még :)

### Tájékoztató

Ez a plugin nem helyettesíti a hivatalos **LearnDash LMS - WooCommerce Integration** bővítményt. A **LearnDash for WooCommerce Shortcodes** egy kiegészítő. Ahhoz, hogy megjelenítsd a Shortcode tartalmát, a saját rendszeremet össze kell kötnöd. tehát a tanfolyam szerkesztőn belül még rá kell kapcsolnod a Kurzust a megvásárolandó WooCommerce termékkel.

A Kurzusok listázásánál (backend) a bővítmény egy extra oszlopot ad hozzá, mely jelzi, mely terméket kapcsoltad rá.

![image](https://github.com/Lonsdale201/learndash-for-wc-shortcodes/assets/23199033/fc07379e-0a89-400c-a5b5-cd8ab01bcd77)



### Shortcode paraméterek:

`[ld_wc_product_name]`

Alapértelmezetten megjelenik a termék neve, és a kosárhoz adás gomb. Ha a termék nevét nem kívánod megjeleníteni, akkor a title="false" értéket kell megadnod: `[ld_wc_product_name  title="true"]`

**További paraméterek:**

- **image="true"**     |  Ha true, akkor megjelenik a termék kiemelt képe. Alapértelmezetten false állapotban van.
- **badge="s% akció"** |  A badge alapértelmezetten nem jelenik meg. A **s%** automatikusan az adott termék százalékos akciós értékét jeleníti meg. Elé, vagy mögé is írhatsz bármit, vagy törölheted a s% értéket. *(Ha a termékkép                      nincs megjelenítve, akkor a badge sem jelenik meg, akkor sem, ha valóban akciós.)*
- **price="true"**     |  Ha true, akkor megjeleníti a termék árát. (wc standard tehát a sale price is megjelenik ha van). Alapértelmezetten megjelenik, ha nem szeretnéd megjeleníteni, írd át **false-ra.**
- **stock="false"**    |  Készlet megjelenítése. Alapból false, tehát nem jelenik meg, csak ha attributumban definiálod true értékkel.
- **addtocart=""**     |  Kosárhoz adás gomb szöveg. A kosár gomb kötelezően megjelenik, és csak a szöveget írhatod át. Az alapértelmezett szöveg, ha nem definiálod: **Kosárhoz adás**
- **shortdesc="false"**  | A termék rövid leírása. Alapból false, ha szeretnéd megjeleníteni, definiáld true értékkel.
- **customlabel=""**    | Egyedi szöveg címke. Alap esetben a kosrához adás gomb felett jelenik meg közvetlenül. Bármilyen szöveget vagy mondatot beleírhatsz. Alapból nincs definiálva, és nem is jelenik meg.
- **separator="false"** | Egyszerű elválasztó, ami a kosrához adás gomb felett jelenik meg közvetlenül. Ha szeretnéd, hogy megjelenjen, true értékkel definiáld.
- **fallbackimg=""**   | Ha a termékednek nincs kiemelt képe, de te engedélyezted azt, akkor megadhatsz url formájában egy másik képet. Ezt tartalékként fogja betölteni, és csak akkor, ha nincs érték amit kivehetne.
- **onsuccess-text=""** | Amikor a kosárhoz adod a terméket *(mivel ajax alapú),* kell egy jelzés a felhasználó felé. Ez definiálás nélkül: **Termék a kosárban**, írd át ha mást szeretnél a gomb szövegének megadni.
- **access-text=""**  | Ha szeretnéd akkor, megjeleníthetsz ebben a shortcode blokkban egy üzenetet, amely az add to cartot helyettesíi, egy jelzéssel, hogy már hozzáférsz a kurzushoz. Nincs alapértelmezett érték.
- **access-link="false"**     | Ha a shortcode-ot egy loop ba rakod, pl Elementor Loop, akkor hasznos, ha az access szövegre kattintásával átlehet menni maga a tanfolyam oldalra. Alapértelmezetten false.
- **outofstock=""**   | Ha a termék már nem kapható, akkor megadhatsz egy egyedi szöveget. Ilyen esetben a Shortcode teljes tartalma eltűnik, és csak a paraméter szöveg jelenik meg. Alapértelmezett érték: **A kurzus már nem                           megvásárolható.**
- **footer=""**  | Lábléc szöveg amely a kosárhoz adás gomb alatt jelenik meg. NIncs alapértelmezett érték, azaz ha nem definiálod semmi nem jelenik meg a "footer-ben".
  
Példa Shortcode:

`[ld_wc_product_name  title="true" image="true" price="true" stock="false" shortdesc="false" separator="true" badge=" s% akció" addtocart="Add a kosárhoz" shortdesc="false" customlabel="Custom label" 
onsuccess-text="Már a kosárban van" access-text="A kurzushoz hozzáférsz" footer="30 napos pénzvisszafizetés"]`


### Extra Shortcodeok
Az új extra shortcode-okat két részre bontjuk:

Kurzus single oldalon használható kódok és attributumok:

* **Label** -> ez jelenik meg a tartalom előtt.
* **Icon** -> ez szintén maga a tartalom , és ha van akkor a label előtt
* **loggedin** -> true vagy false, alapértelmezetten false, ha megadod, hogy true, csak a bejelentkezett felhasználó láthatja
* **enrolled** -> true vagy false, true esetében csak az látja az adott kurzus oldalán, aki enrollolta a kurzust.
* **empty** -> ha az érték üres, vagy 0, és az empty false ra állítod, nem jelent meg semmit. Ha true, akkor is megjeleníti ha az érték üres, vagy nulla. (leckék esetében az empty paraméter nincs definiálva)


**Példák:**

- `[ld_extra_lessons label="Leckék: " icon="<i class='fa fa-book'></i>"]`
- `[ld_extra_topics label="Témák: " icon="<i class='fa fa-book'></i>" empty="false"]`
- `[ld_extra_quiz label="Kvízek: " icon="<i class='fa fa-book'></i>" empty="true"]`
- `[ld_extra_reward_points label="Megszerezhető pontok: " icon="<i class='fa fa-book'></i>" empty="false"]`
- `[ld_extra_access_points label="Szükséges pontok: " icon="<i class='fa fa-trophy'></i>"]`
- `[ld_extra_access_type label="Hozzáférés típusa: " icon="<i class='fa fa-trophy'></i>"]`
- A kategória esetében az empty nem szükséges, ha nincs kategória nem jelenik meg a shortcode. Extra attributum: -> linkable , ha true, akkor kattintható lesz a kategória.
- `[ld_extra_course_category label="Kategória:" loggedin="true" enrolled="true" empty="true" linkable="false"]`
- `[ld_extra_status label="Kurzus státusz:" loggedin="true" enrolled="true" empty="true"]`

**Globális shortcodeok:**


- `[ld_extra_mypoints label="Az Ön pontjai: " empty="false"]`
- `[ld_extra_total_courses_owned label="Összes kurzusom: " empty="true"]`
- `[ld_extra_completed_courses_count label="Befejezett kurzusaim: " empty="true"]`

**Kurzus Loop**
*(csak label attributumot adhatunk meg)*
- `[ld_extra_product_price label="ár"]`

Ez egy speciális shortcode, amit célszerű a loop-ban megadni, pl jetEngine kurzus listing, elementor stb. A bővítmény által biztosított másodlagos összekötés alapján megjeleníti a kurzushoz társított termék árát (tehát, nem a hivatalos összekötésből nézi az árat). Name your price kompatibilis.
Label attributum használata opcionális

### Elementor Class Visibility

A használatházo először a bővítmény beállításaiba be kell kapcsolni az **Enable Elementor Visibility** opciót.
Ezek csak a kurzus single oldalán használhatóak!

Classok: 
* *learndash--enrolled* csak akkor jeleníti meg a widgetet ha az adott felhasználónak a kurzushoz van már hozzáférése
* *learndash--logged_in* csak akkor jelenik meg ha be vagy jelentkezve
* *learndash--logged_out* csaj akkor jelenik meg, ha nem vagy bejelentkezve

*Amelyik widgetre használni szeretnéd, a haladó fülön a Css Osztály mezőbe másold bele a megfelelő class-t.*

### Menu Visibility

Az Elementorhoz hasonlóan előbb ezt is be kell kapcsolnod.
Bekapcsolást követően navigálj a Megjelenés - Menük majd a Mit lássunk fülön kapcsold be a CSS Osztályok megjelenítését.

classok:
* *learndash--menu-anyenrolled* akkor jeleik meg a menüpont ha a felhasználónak van bármilyen kurzusa
*  *learndash--menu-anygroup_enrolled* csak akkor jelenik meg a menüpont ha a felhasználónak van csoporttagsága (legalább egy)


### Új tartalom hozzáadása az új kuruzsaim fiókom menüponthoz. 
Példa kódok:

```
add_action('ld_course_list_after', function($user_id) {
    // Itt írhatnak saját kódot, amely a kurzuslista után jelenik meg
    echo '<p>Egyéni tartalom a felhasználó ID-je: ' . esc_html($user_id) . '</p>';
});

add_action('ld_course_list_after_menu_label', function($label, $user_id) {
    echo '<p>Egyéni tartalom a ' . esc_html($label) . ' címke alatt.</p>';
}, 10, 2);
```


### Használat

Telepít, aktivál. Ha ez megvan, akkor lépj be abba a kurzusba, amihez szeretnél WooCommerce terméket hozzákötni. Belépést követően jobb oldal-t A **Kapcsolódó tartalom** alatt egy új opció jelenik meg: **WC Termék Kiválasztása** Itt add hozzá azt a terméket, aminek a megvásárlása szüksége a kurzushoz. Utolsó lépésként a shortcode-ot helyezd el, például a Tanfolyam Single oldalán (amit lehet Elementorral is szerkeszteni), ha hasznáslz elementor loop builder-t ott is használható.

A Shortcode, automatikusan tudja, hogy a felhasználó által megnyitott kurzus tanfolyam melyik, és megnézi, hogy van-e hozzákötve termék, ha talál egyezést, a rákapcsolt termék adatait fogja megjeleníteni. 
**Ez a kis rendszer globálisra lett tervezve, ezért sem lehet, egyedi ID paraméter-t megadni!**

#### Sorrend:

Nincs rá mód, hogy egyedileg állítsd a shortcode tartalmának sorrendjét shortcode segítségével, azaz nem számít, milyen sorrendben írod be a paramétereket. Minden elem alapvetően egymás alatt van.

* Kiemelt kép (+ benne a badge)
* Termék név
* Stock
* Ár
* Custom label
* Separator
* Add to cart / succes / acces text
* Footer

> [!IMPORTANT]
> Variálható termékek esetében a kosárhoz adás gomb (Opciók választása) szöveget jelenít meg, kattintásra pedig átviszi a felhasználót a termék adatlapra!

CSS segítségével bármit formázhatsz a Shortcode-on belül, és a flexnek köszönhetően az order-el módosíthatod a sorrendet.

#### CLASSOK:

* `wc-ld-wrapper`
*  `wc-ld-image-wrapper`
*  `wc-ld-badge`
*  `(h3) wc-ld-title`
*  `wc-ld-outofstock`
*  `extraaccess`
*  `wc-ld-shortdesc`
*  `wc-ld-price`
*  `wc-ld-custom-label`
*  `wc-ld-separator`
*  `wc-ld-already-have-access`
*  `wc-ld-footer-text`

## Changelog
2024. 02.05

* ÚJ Frissítő szerver bekötve

2023.12.15
V2.0-beta3

* ÚJ shortcode: [ld_extra_product_price] használható a loop-ban. (pl jetengine listing grid stb), a Plugin által biztosított másodlagos WooCommerce termék összekötésből megjeleníti a bekötött Termék árát ami a Kurzus árával egyenlő

2023.11.29
V2.0-beta2

* FIX Flush rew javítás. Ritka esetben régiből újba való update esetében okozott hibát
* Verzió számozás átállítva a beta stage állapotokra

2023.11.16 
V2.0-beta

* ÚJ extra shortcodeok egyes részeit csak a single kurzus oldalon, egyes részeit pedig globálisan is használhatóak
* ÚJ beállítások menüpont : WooCommerce / LearnDash Extras néven
* ÚJ visibility funkció (láthatóság), elementor specifikus, és globális menü specifikus. Class megadással működik.
* Új WooCommerce fiókom menüpont készítés a kurzusokhoz, és számos beállítás hozzá
* ÚJ learndash statisztikai adatok az új wc végponthoz. (felhasználói saját adatai)
* ÚJ extra instrukció megjelenítése ha kurzus terméket vásároltak, a WooCommerce thankyou page oldalon
* TWEAK kód opatimalizálás, fájl struktúra
* TWEAK a Plugin neve a 2.0 verziótól kezdődően: **LearnDash for WooCommerce Extras**
* Két do_action hozzáadva az új végponthoz: add_action('ld_course_list_after', function($user_id) | add_action('ld_course_list_after_menu_label', function($label, $user_id)


2023.08.28
v1.1 

* Javítva lett - Undefined variable $user_id
* Name Your price kompatibilitás - Mostantól ha a termék name Your price alapú, a variációs termékhez hasonlóan viselkedeik a kosárhoz adás gomb. (azaz átirányít a termék oldalra)
* Mostantól az ár sem jelenik meg, ha a felhasználónak van már hozzáférése a kurzushoz.
* Kompatibilitás ellenőrzés: LD 4.8.0
