# H:Dev+ PHP Connector

A PHP connector library for integrating your application with the [H:Dev+](https://www.hdevplus.com) backend service.

This package allows you to send declarative API requests to the H:Dev+ backend with minimal configuration.

## Installation

Install the package via Composer:

```bash
composer require hdevplus/connector-php
```

## Usage
```php
<?php

use HDevPlus\Connector;

$apiUrl = 'https://api.hdevplus.com'; // Replace with your actual API URL
$secureKey = 'your-secure-key';       // Replace with your actual secure key

// Step 1 – Specify the data identifier to request
$requestData = [
    'dataID' => 'GET_POST_LIST'
];

// Step 2 – Invoke the connector with the request data
$connector = new Connector($apiUrl, $secureKey);
$response = $connector->request($requestData);

// Step 3 – Process and utilize the response data
print_r($response);
```

## Requirements
- **PHP >= 7.4**
- **PHP extensions:**
  - ext-curl
  - ext-json