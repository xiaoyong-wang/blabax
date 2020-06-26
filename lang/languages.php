<?php

/*
elements: abbreviation, name, file

requires base64 encoded SVG flag as CSS entry in the main CSS file: 
   .svg_f_en{background-image: url("data:image/svg+xml;base64,SVG_BASE64")}
   .svg_f_de{background-image: url("data:image/svg+xml;base64,SVG_BASE64")}
 */


$lang_list=array();
$lang_list[] = array('en',    'English',         'lang_english.utf8');
$lang_list[] = array('de',    'Deutsch',         'lang_german.utf8');
$lang_list[] = array('fr',    'Français',        'lang_french.utf8');
$lang_list[] = array('es',    'Español',         'lang_spanish.utf8');
$lang_list[] = array('pt-br', 'Português-BR',    'lang_portuguese_br.utf8');
$lang_list[] = array('cz',    'Čeština',         'lang_czech.utf8');
$lang_list[] = array('nl',    'Nederlands',      'lang_dutch.utf8');
$lang_list[] = array('tr',    'Türkçe',          'lang_turkish.utf8');
$lang_list[] = array('it',    'Italiano',        'lang_italian.utf8');
$lang_list[] = array('pl',    'Polski',          'lang_polish.utf8');
$lang_list[] = array('gr',    'Ελληνικά',        'lang_greek.utf8');
$lang_list[] = array('hu',    'Magyar',          'lang_hungarian.utf8');
$lang_list[] = array('ro',    'Română',          'lang_romanian.utf8');
$lang_list[] = array('da',    'Dansk',           'lang_danish.utf8');
$lang_list[] = array('ru',    'Русский',         'lang_russian.utf8');
$lang_list[] = array('id',    'bahasa Indonesia','lang_indonesian.utf8');

/*

- new entries must go to the end of the list!
- languages are alphabetically sorted by abbr
- entries below: flags already set, no language files translated/available

$lang_list[] = array('no', 'Norsk',      'lang_norwegian.utf8');
$lang_list[] = array('pt', 'Português',  'lang_portuguese.utf8');
$lang_list[] = array('se', 'Svenska',    'lang_swedish.utf8');

*/

$lang_admin=array();
$lang_admin[]='english_admin.utf8';

?>