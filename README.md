Recurly module for ZF2
======================

Recurly module provide a small wrapper to the Recurly client library that interact with Recurly's subscription management through [REST API](http://docs.recurly.com/api).

[![Build Status](https://secure.travis-ci.org/neeckeloo/Recurly.png?branch=master)](http://travis-ci.org/neeckeloo/Recurly)
[![Latest Stable Version](https://poser.pugx.org/neeckeloo/Recurly/v/stable.png)](https://packagist.org/packages/neeckeloo/Recurly)
[![Coverage Status](https://coveralls.io/repos/neeckeloo/Recurly/badge.png)](https://coveralls.io/r/neeckeloo/Recurly)
[![Dependencies Status](https://depending.in/neeckeloo/Recurly.png)](http://depending.in/neeckeloo/Recurly)

Introduction
------------

Recurly module provide a config file to initialize simply [REST API](http://docs.recurly.com/api) client.

Installation
------------

If you're using [Composer](http://getcomposer.org/), you can simply add a dependency on `neeckeloo/recurly` to your project's `composer.json` file.

    {
        "require": {
            "neeckeloo/recurly": "1.1.0"
        }
    }

Configuration
-------------

Set your subdomain and API key. If you are using Recurly.js, specify also your private key.

```php
<?php
return array(
    'recurly' => array(
        'subdomain'   => 'your-subdomain',
        'api_key'     => '012345678901234567890123456789ab',
        'private_key' => '0123456789abcdef0123456789abcdef',
    ),
);
```
