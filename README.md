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


**Tesztelve:**

* PHP 8.2
* WooCommerce: 8.0
* LearnDash: 4.7.0.2

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


### Használat

Telepít, aktivál. Ha ez megvan, akkor lépj be abba a kurzusba, amihez szeretnél WooCommerce terméket hozzákötni. Belépést követően jobb oldal-t A **Kapcsolódó tartalom** alatt egy új opció jelenik meg: **WC Termék Kiválasztása** Itt add hozzá azt a terméket, aminek a megvásárlása szüksége a kurzushoz. Utolsó lépésként a shortcode-ot helyezd el, például a Tanfolyam Single oldalán (amit lehet Elementorral is szerkeszteni), ha hasznáslz elementor loop builder-t ott is használható.

A Shortcode, automatikusan tudja, hogy a felhasználó által megnyitott kurzus tanfolyam melyik, és megnézi, hogy van-e hozzákötve termék, ha talál egyezést, a rákapcsolt termék adatait fogja megjeleníteni. 
**Ez a kis rendszer globálisra lett tervezve, ezért sem lehet, egyedi ID paraméter-t megadni!**

Sorrend:

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

CLASSOK:

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

