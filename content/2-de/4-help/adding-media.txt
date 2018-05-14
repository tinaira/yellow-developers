---
Title: Medien hinzufügen 
---
Alle Medien befinden sich im `media`-Verzeichnis. Hier speichert man Bilder und andere Dateien.

[image screenshot-media.png Screenshot]

Das `downloads`-Verzeichnis enthält Dateien zum Herunterladen. Das `images`-Verzeichnis ist zum Speichern von Bildern gedacht. Das `thumbnails`-Verzeichnis enthält Miniaturbilder. Man kann auch weitere Verzeichnisse hinzufügen und Dateien so organisieren wie man will. Im Grunde genommen kann jede Mediendatei von der Webseite heruntergeladen werden. 

## Bilder

Du kannst das [Image-Plugin](https://github.com/datenstrom/yellow-plugins/tree/master/image) benutzen um Bilder einzubinden. Um ein neues Bild hinzuzufügen, kopierst du eine neue Datei ins `images`-Verzeichnis und erstellst eine `[image]`-Abkürzung. Die Bildformate GIF, JPG, PNG und SVG werden unterstützt. Hier ist ein Beispiel:


    [image picture.jpg]
    [image picture.jpg Picture]
    [image picture.jpg "Dies ist ein Beispielbild"]

Bilder in unterschiedliche Stilen:

    [image picture.jpg Beispiel left]
    [image picture.jpg Beispiel centre]
    [image picture.jpg Beispiel right]

Bilder in unterschiedliche Größen:

    [image picture.jpg Beispiel - 64 64]
    [image picture.jpg Beispiel - 320 200]
    [image picture.jpg Beispiel - 50%]

## Videos

Du kannst das [Youtube-Plugin](https://github.com/datenstrom/yellow-plugins/tree/master/youtube) benutzen um Videos einzubinden.

    [youtube fhs55HEl-Gc]
    [youtube fhs55HEl-Gc left 200 112]
    [youtube fhs55HEl-Gc right 200 112]

[Weiter: System anpassen →](adjusting-system)