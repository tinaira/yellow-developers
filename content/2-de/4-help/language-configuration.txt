---
Title: Spracheinstellungen
---
Wie man verschiedene Sprachen konfiguriert.

## Einsprachen-Modus

Falls man einzelne Webseiten übersetzen will, benutzt man den Einsprachen-Modus. Die Standardsprache wird in den [Systemeinstellungen](adjusting-system#systemeinstellungen) festgelegt. Eine andere Sprache lässt sich in den [Einstellungen](markdown-cheat-sheet#einstellungen) ganz oben auf jeder Seite festlegen, zum Beispiel `Language: de`. 

Hier ist eine Englische Seite:

```
---
Title: About us
Language: en
---
Birds of a feather flock together.
```

Eine Deutsche Seite:

```
---
Title: Über uns
Language: de
---
Wo zusammenwächst, was zusammen gehört.
```

Eine Französische Seite:

```
---
Title: À propos de nous
Language: fr
---
Les oiseaux de même plumage volent toujours ensemble.
```

## Mehrsprachen-Modus

Falls man eine komplette Webseite übersetzen will, benutzt man den Mehrsprachen-Modus. Öffne die Datei `system/config/config.ini` und ändere `MultiLanguageMode: 1`. Gehe ins `content`-Verzeichnis und erstelle für jede Sprache ein eigenes Verzeichnis. Hier ist ein Beispiel:

[image screenshot-language1.png Screenshot]

Der erste Screenshot zeigt die Verzeichnisse `1-en`, `2-de` und `3-fr`. Das erzeugt die URLs `http://website/` `http://website/de/` `http://website/fr/` für Englisch, Deutsch und Französisch. Hier ist noch ein Beispiel:

[image screenshot-language2.png Screenshot]

Der zweite Screenshot zeigt die Verzeichnisse `1-en`, `2-de`, `3-fr` und `default`. Das erzeugt die URLs `http://website/en/` `http://website/de/` `http://website/fr/` und die Startseite `http://website/` welche automatisch die Sprache der Besucher ermittelt. 

Um eine [Sprachauswahl](/language/) anzuzeigen, kannst du eine Seite erstellen welche die vorhandenen Sprachen auflistet. Das ermöglicht es Besuchern jede Sprache auszuwählen. Die Sprachauswahl kann man in die Webseite einbauen, beispielsweise in die Navigation oder Fußzeile.

## Sprachen

Die Installation kommt mit drei Sprachen und man kann weitere [Sprachdateien](https://github.com/datenstrom/yellow-plugins/tree/master/language) herunterladen. Lade eine Datei herunter und kopiere sie in das `system/plugins`-Verzeichnis. Die Standardsprache wird in den [Systemeinstellungen](adjusting-system#systemeinstellungen) festgelegt. Dieser Text lässt sich in den [Texteinstellungen](adjusting-system#texteinstellungen) anpassen.

[Weiter: Sicherheitseinstellungen →](security-configuration)