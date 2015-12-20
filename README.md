iron-mq
=================

[![Build Status](https://travis-ci.org/infusephp/iron-mq.png?branch=master)](https://travis-ci.org/infusephp/iron-mq)
[![Coverage Status](https://coveralls.io/repos/infusephp/iron-mq/badge.png)](https://coveralls.io/r/infusephp/iron-mq)
[![Latest Stable Version](https://poser.pugx.org/infuse/iron-mq/v/stable.png)](https://packagist.org/packages/infuse/iron-mq)
[![Total Downloads](https://poser.pugx.org/infuse/iron-mq/downloads.png)](https://packagist.org/packages/infuse/iron-mq)
[![HHVM Status](http://hhvm.h4cc.de/badge/infuse/iron-mq.svg)](http://hhvm.h4cc.de/package/infuse/iron-mq)

IronMQ module for Infuse Framework

## Installation

1. Install the package with [composer](http://getcomposer.org):

```
composer require infuse/iron-mq
```

2. Add the service to `services` in your app's configuration:

```php
'services' => [
	// ...
	'ironmq' => /App\Iron\Services\IronMQ'
	// ....
]
```

3. Add the console command to run jobs to `modules.commands` in your app's configuration:

```php
'modules' => [
	// ...
	'commands' => [
		// ...
		'App\Iron\Console\SetupCommand',
		'App\Iron\Console\ProcessCommand'
	]
]
```

## Usage

If you are using iron.io push queues they can be installed with `./infuse iron-setup`