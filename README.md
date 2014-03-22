Recurly module for ZF2
======================

Recurly module provide a small wrapper to the Recurly client library that interact with Recurly's subscription management through [REST API](http://docs.recurly.com/api).

[![Dependencies Status](https://depending.in/neeckeloo/Recurly.png)](http://depending.in/neeckeloo/Recurly)

Introduction
------------

NewRelic module provide a config file to initialize simply [REST API](http://docs.recurly.com/api) client.

Installation
------------

If you're using [Composer](http://getcomposer.org/), you can simply add a dependency on `neeckeloo/recurly` to your project's `composer.json` file.

    {
        "require": {
            "neeckeloo/recurly": "dev-master"
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
