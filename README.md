# Vendor Patches

[![Downloads total](https://img.shields.io/packagist/dt/migrify/vendor-patches.svg?style=flat-square)](https://packagist.org/packages/migrify/vendor-patches/stats)

Check NEON/YAML/TWIG/LATTE files for existing classes and class constants

## Install

```bash
# this package needs to be in root to work 
composer require cweagans/composer-patches --dev
```

```bash
composer require migrify/vendor-patches --dev
```

In legacy projects, we expect many version conflicts that would normally stop us hard... we're ready for that with prefixed version:

```bash
composer require migrify/vendor-patches-prefixed --dev
```

## Usage

How to create [a patch for a file in `/vendor`](https://pehapkari.cz/blog/2017/01/20/jak-snadno-a-rychle-upravovat-soubory-ve-vendoru)?

1. Create a copy of your file in vendor with any suffix, e.g. `*.old`

For example, if you edit:
 
```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php
# copy of the file
vendor/nette/di/src/DI/Extensions/InjectExtension.php.old
```

2. Open the original file and change the lines you need:

```diff
 			if (DI\Helpers::parseAnnotation($rp, 'inject') !== null) {
-				if ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
+				if ($type = \Amateri\Reflection\Helper\StaticReflectionHelper::getPropertyType($rp)) {
+				} elseif ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
 					$type = Reflection::expandClassName($type, Reflection::getPropertyDeclaringClass($rp));
```

Only `*.php` file is loaded, not the `*.php.old` one. This way you can **be sure the new code** is working before you generate patches.

3. Run `generate` command ~~for every single files changed this way~~... once for all files 🎆

```bash
vendor/bin/vendor-patches generate
```

4. This tool will generate patch files for all files created this way in `/patches` directory:

```bash
/patches/nette-di-di-extensions-injectextension.php.patch
```

The patch path is based on original file path, so **the patch name is always unique**. 

5. In the last step, configuration for `cweagans/composer-patches` is added your `composer.json`:

```json
{
    "extra": {
        "patches": {
            "nette/di": [
                "patches/nette/di/di_extensions_injectextension.patch"
            ]
        }
    }
}
```

(If the file is already there, it won't be added of course.)

That's it!

Now all you need to do is run composer:

```bash
composer install
``` 

And your patches are applied to your code! 

<br>

If not, get more information from composer to find out why:

```bash
composer install --verbose
```
