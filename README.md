# WIISARI
Wiisari is a web-based timeclock system.

Wiisari is originally based on [PHP Timeclock](http://timeclock.sourceforge.net/) and more specifically [UnitedTechGroup fork](https://github.com/UnitedTechGroup/timeclock) of it.

## Käyttäjätasot
Suuremmilla tasoilla on omien oikeuksien lisäksi alempien tasojen oikeudet.

* [taso0] Työntekijä (kaikilla on vähintään tämä)
	* Näkee omat työtuntiraporttinsa

* [taso1] Normaali valvoja
	* Näkee valittujen ryhmien työntekijät sekä heidän työtuntiraportit
	* Voi hakea työntekijöiden viivakoodeja tulostettavaksi

* [taso2] Valvoja + editointi
	* Voi muokata valittujen ryhmien työntekijöiden(taso 0) työtunteja
	* Voi muokata valittujen ryhmien työntekijöiden(taso 0) tietoja
	* Voi luoda tason 0 käyttäjiä

* [taso3] Admin
	* Pääsy kaikkialle ja kaikkiin työntekijöihin
	* Voi luoda minkä vain tason käyttäjiä
	* Voi muokata kaikkien tasojen henkilöitä täysin valtuuksin (oikeuksien muokkaus)

## Code used from these projects
PHP Timeclock
https://github.com/UnitedTechGroup/timeclock

Chart.js
https://github.com/chartjs/Chart.js

Tablesorter
https://github.com/mottie/tablesorter

Text Input Effects
https://github.com/codrops/TextInputEffects

PHP Barcode Generator
https://github.com/picqer/php-barcode-generator
