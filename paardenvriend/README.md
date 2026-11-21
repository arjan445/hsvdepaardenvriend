# Thema HSV De Paardenvriend – v2.2.0

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
- Alle logo's staan in een **vierkant (1:1) vak**; een vierkant of liggend logo met weinig witruimte werkt het best.
- Categorieën: Hoofdsponsor, Stersponsor, Sponsor. Uit te breiden; per categorie een **volgorde** (pagina Sponsoren) en een **gewicht** (1–10).
- **Sponsorbalk** (homepage en boven de footer): bij elk bezoek een willekeurige selectie van *n* logo's (Customizer → Homepage, standaard 6). Het gewicht bepaalt hoe vaak een categorie voorkomt; standaard Hoofdsponsor 5, Stersponsor 3, Sponsor 1. De selectie gebeurt in de browser, dus ook met paginacache wisselt hij per bezoek.
- De pagina **Sponsoren** toont iedereen, per categorie.
- Shortcode: `[sponsoren]` (alles), `[sponsoren categorie="hoofdsponsor,stersponsor"]`, `[sponsoren aantal="6"]` (willekeurige, gewogen selectie).

## Overig

- `[contactgegevens]` – adres, telefoon, mail, Facebook uit de Customizer.
- Blokstijl "Grijs kader met groene lijn" voor groepen, alinea's en citaten.
