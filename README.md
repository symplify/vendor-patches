# Vendor Patches

[![Downloads total](https://img.shields.io/packagist/dt/symplify/vendor-patches.svg?style=flat-square)](https://packagist.org/packages/symplify/vendor-patches/stats)

Generate vendor patches for packages with single command.

## Install

```bash
composer require symplify/vendor-patches --dev
```

## Usage

How to create [a patch for a file in `/vendor`](https://tomasvotruba.com/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates/)?

### 1. Create a Copy of `/vendor` file you Want To Change with `*.old` Suffix

For example, if you edit:

```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php
# copy of the file
vendor/nette/di/src/DI/Extensions/InjectExtension.php.old
```

### 2. Open the original file and change the lines you need:

```diff
 			if (DI\Helpers::parseAnnotation($rp, 'inject') !== null) {
-				if ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
+				if ($type = \App\Reflection\Helper\StaticReflectionHelper::getPropertyType($rp)) {
+				} elseif ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
 					$type = Reflection::expandClassName($type, Reflection::getPropertyDeclaringClass($rp));
```

Only `*.php` file is loaded, not the `*.php.old` one. This way you can **be sure the new code** is working before you generate patches.

### 3. Run `generate` command 🥳️

```bash
vendor/bin/vendor-patches generate
```

This tool will generate **patch files for all vendor files modified this way**.

By default, they will be created in the `patches` subdirectory of your repository,
but you can override this using the environment variable `VENDOR_PATCHES_OUTPUT_PATH`.
If its value is an absolute path, it must describe a path within the repository.
If a relative path, it will be relative to the repository root.

```bash
patches/nette-di-di-extensions-injectextension.php.patch
```

Each patch file name is based on the original file path, so **it is always unique**.

<br>

Also, `generate` will add configuration for `cweagans/composer-patches` to your `composer.json`:

```json
{
    "extra": {
        "patches": {
            "nette/di": [
                "patches/nette_di_di_extensions_injectextension.patch"
            ]
        }
    }
}
```

That's it!

<br>

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

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
