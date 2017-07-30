[![Build Status](https://travis-ci.org/php-mq/client.svg?branch=master)](https://travis-ci.org/php-mq/client)
[![Latest Stable Version](https://poser.pugx.org/php-mq/client/v/stable)](https://packagist.org/packages/php-mq/client) 
[![Total Downloads](https://poser.pugx.org/php-mq/client/downloads)](https://packagist.org/packages/php-mq/client) 
[![Coverage Status](https://coveralls.io/repos/github/php-mq/client/badge.svg?branch=master)](https://coveralls.io/github/php-mq/client?branch=master)

# PHPMQ\Client

## Description

The PHPMQ client to send, consume and acknowledge messages in interaction with the PHPMQ server.

## Installation

```bash
composer require php-mq/client
```

## Usage

### Sending a message to the message queue server

```php
<?php declare(strict_types=1);

namespace YourVendor\YourProject;

use PHPMQ\Client\Client;
use PHPMQ\Client\Types\QueueName;
use PHPMQ\Client\Sockets\ClientSocket;
use PHPMQ\Client\Sockets\Types\NetworkSocket;

$networkSocket = new NetworkSocket( '127.0.0.1', 9100 );
$clientSocket  = new ClientSocket( $networkSocket );
$client        = new Client( $clientSocket );

$client->sendMessage( 
	new QueueName( 'Example-Queue' ), 
	'This is an example message.'
);

$client->disconnect();
```

## Contributing

Contributions are welcome and will be fully credited. Please see the [contribution guide](CONTRIBUTING.md) for details.


