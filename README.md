# good-food-gone-bad-wp-plugin

# 1. Projekt erstellen

Zum Kompilieren der Übersetzungen muss das gettext Programm `msgfmt` muss im Pfad liegen ([gettext herunterladen](https://www.gnu.org/software/gettext/gettext.html)).

Composer Abhängigkeiten müssen installiert werden:

```
composer install
```

## 1.1 Plugin Zip erstellen

```
composer run build
```

## 1.2 Sprachdateien generieren

```
composer run build-i18n
```

## 1.3 Clean

```
composer run clean
```