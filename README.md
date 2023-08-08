# learndash-for-wc-shortcodes
Apró kiegészítő a Learndash és WooCommerce-hez

### Bevezető

A LearnDash ugyan a hivatalos kiegészítő lévén kompatibilis a WooCommerce-el, tehát vásárlást követően a hozzáférést beállíthatod, azonban a termék direkt megjelenítése a kurzus oldalon nehézkes.

Ez az apró Shortcode alapú paraméterezhető bővítmény egy exrta lehetőséget biztos ahhoz, hogy összekösd a terméket és a kurzust, amit a kurzus single oldalán megjeleníthstz, így a látogató a kurzust a kosárhoz adhatja 
anélkül, hogy külön elnavigáljon a termék oldalra.

> [!IMPORTANT]
> Ez a bővítmény nem kompatibilis a WPML vagy egyéb fordító bővítményekkel...Még :)

**Shortcode paraméterek:**

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
- **outofstock=""**   | Ha a termék már nem kapható, akkor megadhatsz egy egyedi szöveget. Ilyen esetben a Shortcode teljes tartalma eltűnik, és csak a paraméter szöveg jelenik meg. Alapértelmezett érték: **A kurzus már nem                           megvásárolható.**
- **footer=""**  | Lábléc szöveg amely a kosárhoz adás gomb alatt jelenik meg. NIncs alapértelmezett érték, azaz ha nem definiálod semmi nem jelenik meg a "footer-ben".
  
Példa Shortcode:

`[ld_wc_product_name  title="true" image="true" price="true" stock="false" shortdesc="false" separator="true" badge=" s% akció" addtocart="Add a kosárhoz" shortdesc="false" customlabel="Custom label" 
onsuccess-text="Már a kosárban van" access-text="A kurzushoz hozzáférsz" footer="30 napos pénzvisszafizetés"]`

Sorrend:

Nincs rá mód, hogy egyedileg állítsd a shortcode tartalmának sorrendjét. 

* Minden elem alapvetően egymás alatt van.
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
