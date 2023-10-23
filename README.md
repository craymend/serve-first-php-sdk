# Serve First API SDK

This package is a PHP wrapper for the [Serve First API](https://docs.mysfsgateway.com/api/v2).

### Requirements

This project works with PHP 7.2+.

## Installation

Install with composer:

```
composer require craymend/serve-first-php-sdk
```

## Examples

Create an instance of Request. Be sure to set sandbox mode when testing.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Craymend\ServeFirst\Request;

$apiVersion = 'v2';

$mode = Request::MODE_SANDBOX;
// $mode = Request::MODE_PRODUCTION;

$sourceKey = 'your-key';
$sourcePin = 'your-pin';

$request = new Request($sourceKey, $sourcePin);

// the mode controls the baseUrl
echo 'baseUrl: ' . $request->getBaseUrl() . '<br>';
echo 'Switch to sandbox mode<br>';
$request->setMode($mode); // Change to sandbox mode
echo 'baseUrl: ' . $request->getBaseUrl() . '<br>';
```

You can now use the Serve First API. For example, [retrieve product categories](https://docs.mysfsgateway.com/api/v2#tag/categories):

```php
<?php

echo "Test getting data:<br><br>";

$uri = '/products/categories';

$data = [];

$result = $request->get($uri, $data);

if (!$result->getStatus()) {
    echo 'error:<br><br>';

	$errors = $result->getErrors();
    echo json_encode($errors);
}else{
    echo 'Success! data:<br><br>';
    $data = $result->getData();
    echo 'data: ' . json_encode($data) . '<br>';
}
```

## License

MIT