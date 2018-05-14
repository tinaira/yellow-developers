---
Title: Ajouter des fichiers
---
Les fichiers sont localisés dans le dossier `media`. Vous pouvez y placer des images et des fichiers.

[image screenshot-media.png Screenshot]

Le dossier `downloads` contient des fichiers à télécharger. Le dossier `images` est l'endroit où stocker vos images. Le dossier `thumbnails` contient les vignettes d'image. Vous pouvez aussi créer des dossiers additionnels et organiser vos fichiers comme il vous convient. Essentiellement, n'importe quel fichier multimédia peut être téléchargé à partir du site web.

## Images

Vous pouvez utiliser le [plugin image](https://github.com/datenstrom/yellow-plugins/tree/master/image) pour intégrer les images. Pour ajouter une nouvelle image, copiez un nouveau fichier dans le dossier `images` et créez un raccourci `[image]`. Les formats d'image GIF, JPG, PNG et SVG sont pris en charge. Voici un exemple:

    [image picture.jpg]
    [image picture.jpg Picture]
    [image picture.jpg "Ceci est un exemple d'image"]

Images de différentes styles:

    [image picture.jpg Exemple left]
    [image picture.jpg Exemple centre]
    [image picture.jpg Exemple right]

Images de différentes tailles:

    [image picture.jpg Exemple - 64 64]
    [image picture.jpg Exemple - 320 200]
    [image picture.jpg Exemple - 50%]

## Vidéos

Vous pouvez utiliser le [plugin Youtube](https://github.com/datenstrom/yellow-plugins/tree/master/youtube) pour intégrer des vidéos: 

    [youtube fhs55HEl-Gc]
    [youtube fhs55HEl-Gc left 200 112]
    [youtube fhs55HEl-Gc right 200 112]

[Suivant: Ajuster le système →](adjusting-system)