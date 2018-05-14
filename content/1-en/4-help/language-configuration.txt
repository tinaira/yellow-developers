---
Title: Language configuration
---
Here's how to set up different languages.

## Single language mode

If you want to translate single web pages, use the single language mode. The default language is defined the [system settings](adjusting-system#system-settings). A different language can be defined in the [settings](markdown-cheat-sheet#settings) at the top of each page, for example `Language: en`.

Here's an English page:

```
---
Title: About us
Language: en
---
Birds of a feather flock together.
```

A German page:

```
---
Title: Über uns
Language: de
---
Wo zusammenwächst was zusammen gehört.
```

A French page:

```
---
Title: À propos de nous
Language: fr
---
Les oiseaux de même plumage volent toujours ensemble.
```

## Multi language mode

If you want to translate an entire website, it's best to use the multi language mode. Open file `system/config/config.ini` and change `MultiLanguageMode: 1`. Go to your `content` folder and create a new folder for each language. Here's an example:

[image screenshot-language1.png Screenshot]

The first screenshot shows the folders `1-en`, `2-de` and `3-fr`. This gives you the URLs `http://website/` `http://website/de/` `http://website/fr/` for English, German and French. Here's another example:

[image screenshot-language2.png Screenshot]

The second screenshot shows the folders `1-en`, `2-de`, `3-fr` and `default`. This gives you the URLs `http://website/en/` `http://website/de/` `http://website/fr/` and a home page `http://website/` that automatically detects the visitor's language. 

To show a [language selection](/language/), you can create a page that lists available languages. This provides visitors the possibility to choose any language. You can add a link somewhere on your website, for example in the navigation or the footer.

## Languages

The installation comes with three languages and you can download more [language files](https://github.com/datenstrom/yellow-plugins/tree/master/language). Download a language file and copy it into your `system/plugins` folder. The default language is defined the [system settings](adjusting-system#system-settings). This text can be customised in the [text settings](adjusting-system#text-settings).

[Next: Security configuration →](security-configuration)