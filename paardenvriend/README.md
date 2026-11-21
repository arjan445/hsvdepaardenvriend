# Thema HSV De Paardenvriend – v2.0.0

Bootstrap 5-thema in de stijl van de mockup (Lora + PT Sans, lokaal gehost). Bevat de **sponsoren**. De agenda, startlijsten en uitslagen komen uit de plugin **HSV Wedstrijden** (eerst installeren).

## Installatie

1. Plugin `hsv-wedstrijden.zip` uploaden en activeren.
2. Thema `paardenvriend.zip` uploaden en activeren (maakt de sponsorcategorieën aan).
3. Instellingen → Permalinks: één keer opslaan.
4. Pagina's aanmaken: *Home* (instellen als statische voorpagina), *Over ons*, *Sponsoren* (slug `sponsoren`), *Leden*, *Contact*, privacyverklaring.
5. Weergave → Menu's: hoofdmenu met o.a. Agenda en Uitslagen (staan onder "Berichttype-archieven").
6. Customizer:
   - **Homepage**: kopfoto, teksten, blok "Inschrijven", seizoenskalender, mededelingsbalk, welke sponsorcategorieën op de homepage.
   - **Verenigingsgegevens**: adres, telefoon, e-mail, Facebook, KVK. (Standaard al ingevuld met Bergerhofweg 2, Reuver.)
   - **Site-identiteit**: logo (anders wordt het meegeleverde logo gebruikt).

## Sponsoren

- **Sponsoren → Nieuwe sponsor**: naam, website, logo (rechts), categorie.
- Categorieën: Hoofdsponsor, Stersponsor, Sponsor. Uit te breiden; per categorie een *volgorde* en *grootte* van de logo's.
- Volgorde binnen een categorie: *Volgorde* bij Pagina-attributen.
- Getoond op: homepage (gekozen categorieën), balk boven de footer op andere pagina's, en de pagina **Sponsoren** (alles, per categorie).
- Shortcode: `[sponsoren]`, `[sponsoren categorie="hoofdsponsor,stersponsor" koppen="nee"]`.

## Overig

- `[contactgegevens]` – adres, telefoon, mail, Facebook uit de Customizer.
- Blokstijl "Grijs kader met groene lijn" voor groepen, alinea's en citaten.
