# learndash-for-wc-shortcodes
Apró kiegészítő a Learndash és WooCommerce-hez

### Bevezető

A LearnDash ugyan a hivatalos kiegészítő lévén kompatibilis a WooCommerce-el, tehát vásárlást követően a hozzáférést beállíthatod, azonban a termék direkt megjelenítése a kurzus oldalon nehézkes.

Ez az apró Shortcode alapú paraméterezhető bővítmény egy exrta lehetőséget biztos ahhoz, hogy összekösd a terméket és a kurzust, amit a kurzus single oldalán megjeleníthstz, így a látogató a kurzust a kosárhoz adhatja 
anélkül, hogy külön elnavigáljon a termék oldalra.

> [!FIGYELEM]
> Ez a bővítmény nem kompatibilis a WPML vagy egyéb fordító bővítményekkel...Még :)

**Shortcode paraméterek:**

`[ld_wc_product_name]`

Alapértelmezetten megjelenik a termék neve, és a kosárhoz adás gomb. Ha a termék nevét nem kívánod megjeleníteni, akkor a title="false" értéket kell megadnod: `[ld_wc_product_name  title="true"]`

**További paraméterek:**

- image="true"     |  Ha true, akkor megjelenik a termék kiemelt képe. Alapértelmezetten false állapotban van.
- badge="s% akció" |  A badge alapértelmezetten nem jelenik meg. A **s%** automatikusan az adott termék százalékos akciós értékét jeleníti meg. Elé, vagy mögé is írhatsz bármit, vagy törölheted a s% értéket. *(Ha a termékkép                      nincs megjelenítve, akkor a badge sem jelenik meg, akkor sem, ha valóban akciós.)*
- price="true"     |  Ha true, akkor megjeleníti a termék árát. (wc standard tehát a sale price is megjelenik ha van). Alapértelmezetten megjelenik, ha nem szeretnéd megjeleníteni, írd át **false-ra.**
- stock="false"    |  Készlet megjelenítése. Alapból false, tehát nem jelenik meg, csak ha attributumban definiálod true értékkel.
- addtocart=""     |  Kosárhoz adás gomb szöveg. A kosár gomb kötelezően megjelenik, és csak a szöveget írhatod át. Az alapértelmezett szöveg, ha nem definiálod: **Kosárhoz adás**
