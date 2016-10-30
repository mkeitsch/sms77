# MKcom.SMS77

Small library to send sms via SMS77.

## Installation

### via Composer

**Note:** This package is not registered on packagist.org.

```bash
$ composer config repositories.mkcom/sms77 vcs git@github.com:mkeitsch/sms77.git

$ composer require mkcom/sms77
```

## Using SMS77

Create a sms and set at least one recipient and the message text:

```php
$sms = new Sms();
$sms->addRecipient('555-123456789');
$sms->setMessage('A message for your recipient. Greetings ;)');
```

You can also set an array of recipients or use your SMS77 address book contact names:

```php
$recipients = array(
    '555-123456789',
    'John Doe',
    '555-987654321'
);
$sms->setRecipients($recipients);
```

To send the sms get the instances of the supplied request engine and gateway and send the sms via the gateway:

```php
$requestEngine = SimpleCurl::getInstance();
$httpApiGateway = HttpApiGateway::getInstance($requestEngine, array(/* configuration */));

$httpApiGateway->send($sms);
```

### Interfaces

`GatewayInterface` and `RequestEngineInterface` will help you building your own gateway and request engine.

### Testing

A `TestingGateway` helps you writing tests without sending data.

You can also test with the production API of SMS77 by setting the sms to a dummy: `$sms->setDummySms(true);`

## API documentation of SMS77

You can find all about the API on [http://www.sms77.de/funktionen/http-api](http://www.sms77.de/funktionen/http-api).

The current API documentation is described in [http://www.sms77.de/api.pdf](http://www.sms77.de/api.pdf). 
