# Vendor Patches

[![Downloads total](https://img.shields.io/packagist/dt/symplify/vendor-patches.svg?style=flat-square)](https://packagist.org/packages/symplify/vendor-patches/stats)

Generate vendor patches for packages with single command.

## Install

```bash
composer require symplify/vendor-patches --dev

# If you are applying patches to production, be sure to also explicitly add cweagans/composer-patches.
composer require cweagans/composer-patches
```

<br>

## Usage

How to create [a patch for a file in `/vendor`](https://tomasvotruba.com/blog/2020/07/02/how-to-patch-package-in-vendor-yet-allow-its-updates/)?

<br>

### 1. Create a Copy of `/vendor` file you Want To Change with `*.old` Suffix

For example, if you edit:

```bash
vendor/nette/di/src/DI/Extensions/InjectExtension.php
# copy of the file
vendor/nette/di/src/DI/Extensions/InjectExtension.php.old
```

<br>

### 2. Open the original file and change the lines you need:

```diff
 			if (DI\Helpers::parseAnnotation($rp, 'inject') !== null) {
-				if ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
+				if ($type = \App\Reflection\Helper\StaticReflectionHelper::getPropertyType($rp)) {
+				} elseif ($type = DI\Helpers::parseAnnotation($rp, 'var')) {
 					$type = Reflection::expandClassName($type, Reflection::getPropertyDeclaringClass($rp));
```

Only `*.php` file is loaded, not the `*.php.old` one. This way you can **be sure the new code** is working before you generate patches.

Make sure to back up other modified files in the vendor/ directory as well as some of the commands below may overwrite them.

<br>

### 3. Run `generate` command 🥳️

```bash
vendor/bin/vendor-patches generate
```

This tool will generate **patch files for all files created this** way in `/patches` directory:

```bash
/patches/nette-di-di-extensions-injectextension.php.patch
```

The patch path is based on original file path, so **the patch name is always unique**.

<br>

Also, it will add configuration for `cweagans/composer-patches` to your `composer.json`:

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

<br>

#### 3.1 When using cweagans/composer-patches v2

`cweagans/composer-patches` v2 requires the execution of 2 additional steps after generating the patches:

Updating the `patches.lock.json` file:

```bash
composer patches-relock
```

Applying the new patches:

```bash
composer patches-repatch
```

### 4. Final steps

Now you need to do run composer to update the lock file as the checksum of `composer.json` has changed:

```bash
composer update --lock
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

### Summary

To summarize, the generate workflow is:

```bash
# generate patches
vendor/bin/vendor-patches generate 
# (if using cweagans/composer-patches v2)
composer patches-relock 
composer patches-repatch
# update the lock file
composer update --lock 
# install with patches applied
composer install 
```

## Patches File and Patches Folder Options

Optionally, if you use a [patches file](https://docs.cweagans.net/composer-patches/usage/defining-patches/#patches-file) you can specify its path using the `--patches-file` option:

```bash
vendor/bin/vendor-patches generate --patches-file=patches.json
```

You can choose to write the patches to a different folder than the default 'patches' folder by specifying the folder name using the `--patches-folder` option:

```bash
vendor/bin/vendor-patches generate --patches-folder=patches-composer
```

<br>

## TroubleShooting

### Upgrading from older versions of cweagans/composer-patches (pre 2.0.0)

If you are upgrading `cweagans/composer-patches` to 2.0.0 and newer versions, you may need to adjust your patches to ensure compatibility.

The new version requires that `--- /dev/null` needs to be replaced with `--- <file-path>` in your patch files.

For example, if you have an old patch file that starts with:

```diff
--- /dev/null
+++ ../src/SomeFile.php
@@ -0,0 +1,10 @@
+<?php
+// some code
``` 
You need to change it to:

```diff
--- ../src/SomeFile.php
+++ ../src/SomeFile.php
@@ -0,0 +1,10 @@
+<?php
+// some code
```

### macOS

If you are on macOS, and got hang on applying patch, you may need to install `gpatch`, you can install with:

```
brew install gpatch
```

and register to `.bash_profile` or `.zshrc` (if you're using [oh-my-zsh](https://ohmyz.sh/)):

```
PATH="/opt/homebrew/opt/gpatch/libexec/gnubin:$PATH"
```

<br>
