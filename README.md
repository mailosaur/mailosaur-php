# Mailosaur PHP Client Library

[Mailosaur](https://mailosaur.com) lets you automate email and SMS tests, like account verification and password resets, and integrate these into your CI/CD pipeline.

[![](https://github.com/mailosaur/mailosaur-php/workflows/CI/badge.svg)](https://github.com/mailosaur/mailosaur-php/actions)

## Installation

You can install the bindings via Composer. Run the following command:

```
composer require mailosaur/mailosaur
```

To use the client library, use Composer's autoload:

```
require_once('vendor/autoload.php');
```

## Documentation

Please see the [PHP client reference](https://mailosaur.com/docs/email-testing/php/client-reference/) for the most up-to-date documentation.

## Usage

example.php

```php
<?php

require_once('vendor/autoload.php');

use Mailosaur\MailosaurClient;

$mailosaur = new MailosaurClient('YOUR_API_KEY');

$result = $mailosaur->servers->all();

print('You have a server called: ' . $result->items[0]->name);

?>
```

## Development

You must have the following prerequisites installed:

* [Composer](https://getcomposer.org/)

Install all development dependencies:

```sh
composer install
```

The test suite requires the following environment variables to be set:

```sh
export MAILOSAUR_API_KEY=your_api_key
export MAILOSAUR_SERVER=server_id
```

Run all tests:

```sh
composer run-script test
```

## Contacting us

You can get us at [support@mailosaur.com](mailto:support@mailosaur.com)
