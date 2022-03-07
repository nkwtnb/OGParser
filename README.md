# OGParser
This is a library for parse OGP.

## Install
```
$ composer require nkwtnb/ogparser
```

## Usage
```
<?php
use nkwtnb\ogparser\Ogparser;

require_once(__DIR__ . '/vendor/autoload.php');

$ogp = new Ogparser("https://example.com");
$ogp->fetch();
var_dump($ogp->get_url());
var_dump($ogp->get_title());
var_dump($ogp->get_description());
var_dump($ogp->get_image());
var_dump($ogp->get_site_name());
```

## License
MIT
