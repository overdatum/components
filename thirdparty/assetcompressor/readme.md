# AssetCompressor

Inspired by ShawnMcCool's closure-compiler bundle, I started adding on to his code until the AssetCompressor was born.
closure-compiler (and readme.md) credits go to ShawnMcCool & friends =)

AssetCompressor is installable via the Artisan CLI:

    php artisan bundle:install assetcompressor

**Important:** Closure Compiler requires that Java is installed.  Consequently, it can run on virtually any operating system.

### Description

This bundle automatically minifies a site's Assets (JS / CSS) and updates them only when necessary.


### 1. Bundle Registration

Add 'assetcompressor' to your **application/bundles.php** file:

    return array(
        'assetcompressor' => array(
            'auto' => true
        )
    );

### 2. Configuration

This bundle assumes that the Java binary is available and already in your PATH.  This is almost always the case.  If --for whatever reason-- you need to specify the absolute path to the binary, you can do so in the java_binary_path_overrides array().  You can add as many paths as you'd like and may mix and match paths for different operating systems.  Invalid paths will simply be ignored.

### License

The AssetCompressor bundle is released under the MIT license.

The Google Closure Compiler has been included in this package (in the drivers/closure-compiler directory).  It is important to note that the Closure Compiler has been released under the Apache License, Version 2.0.

More information can be found here: [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)