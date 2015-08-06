=== Disable Title ===
Contributors:f.staude
Donate link: http://www.staude.net/donate
Tags: page, post, title, the_title
Requires at least: 3.0
Tested up to: 4.0
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Disable the title per page/post

== Description ==

English:

With this plugin you can define for each page/post whether the title should be suppressed on the homepage, category, archive, detail pages or menu.

If they have more translations for this plugin, please send email to frank@staude.net, I will then install.

Entrys in the changelog with numbers means the ticket id on http://bugs.staude.net


Deutsch: 

Mit diesem Plugin kann man bei jeder Seite und jedem Artikel festlegen ob der Titel auf der Startseite, auf Kategorie, Archiv, Detailseiten oder im Menü unterdrückt werden soll.

Wenn sie weitere Übersetzungen für dieses Plugin haben, bitte per E-Mail an frank@staude.net schicken, ich werde sie dann einbauen.

Einträge im changelog mit Zahlen verweisen auf die Ticket ID auf http://bugs.staude.net

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `disable-title` and all its contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the plugin.

== Frequently Asked Questions ==

English:

= I have a new translation  =

Send them by e-mail to frank@staude.net, I will then install the next update with.

= I found a bug  =

Please report it at http://bugs.staude.net

Select the project "WP Plugin: Disable Title" and report it.

= I have a feature request =

Please report it at http://bugs.staude.net

Select the project "WP Plugin: Disable Title" and report it.




Deutsch:

= Ich habe eine neue Übersetzung fertiggestellt  =

Schick sie mir per e-mail an frank@staude.net, ich werde sie dann beim nächsten Update mit einbauen

= Ich habe einen Fehler gefunden  =

Bitte geh auf die Seite http://bugs.staude.net und melde dich dort an.

Wähle das Projekt "WP Plugin: Disable Title" und melde den Fehler.

= Ich möchte eine neue Funktion vorschlagen  =

Bitte geh auf die Seite http://bugs.staude.net und melde dich dort an.

Wähle das Projekt "WP Plugin: Disable Title" und trag deinen Wunsch ein.





== Screenshots ==

1. The settings dialog

== Changelog ==

= 0.9 =
- Add feature to disable in widgets
- fix: get_title replacement only within the_loop
- fix: disable page in menu, when wordpress uses the default menu

= 0.8 =
- filter recent posts widget to disable post on startpage. Thanks to Gert Jan Zeilstra for reporting the issue.

= 0.7 =
- 0000050: disable in menu. 
  Add feature to disable a page in menu

= 0.6 =
- Disable some php notices if WP Debug active

= 0.5 =
- 0000045: Catch calls without ID in disable_title::the_title()

= 0.4 =
- 0000030: I like to deactivate and delete the plugin and get this error message:Fatal error: Call to a member function query() on a non-object

= 0.3.1 =
- 0000025: language files falsch

= 0.3 =
- 0000025: language files falsch

= 0.2 =
- 0000024: Support for Custom Post Types

= 0.1 =
First version.

