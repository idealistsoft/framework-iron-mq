iron-mq
=================

[![Latest Stable Version](https://poser.pugx.org/infuse/iron-mq/v/stable.png)](https://packagist.org/packages/infuse/iron-mq)
[![Total Downloads](https://poser.pugx.org/infuse/iron-mq/downloads.png)](https://packagist.org/packages/infuse/iron-mq)

IronMQ module for Infuse Framework

## Installation

1. Install the package with [composer](http://getcomposer.org):

```
composer require infuse/iron-mq
```

2. Add the console command to run jobs to `modules.commands` in your app's configuration:
```php
'modules' => [
	// ...
	'commands' => [
		// ...
		'app\iron\console\SetupCommand',
		'app\iron\console\ProcessCommand'
	]
]
```

## Usage

If you are using iron.io push queues they can be installed with `./infuse iron-setup`