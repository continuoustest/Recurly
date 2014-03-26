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

Requirements
------------

* PHP 5.3 or higher
* [Recurly PHP Library](https://github.com/recurly/recurly-client)

Installation
------------

NewRelic module only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

You can install the module from command line:
```sh
$ php composer.phar require neeckeloo/recurly:1.1.*
```

Alternatively, you can also add manually the dependency in your `composer.json` file:
```json
{
    "require": {
        "neeckeloo/recurly": "1.1.*"
    }
}
```

Enable the module by adding `Recurly` key to your `application.config.php` file. Customize the module by copy-pasting
the `recurly.global.php.dist` file to your `config/autoload` folder.
    

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
