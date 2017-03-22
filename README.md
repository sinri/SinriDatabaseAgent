# SinriDatabaseAgent
Provide a old-style alike quick SQL handler for PHP

## Composer

    composer require sinri/sinri-database-agent

## Usage

1. Include the `autoload.php` into your PHP project.
2. Declare what you need for convenience.

```PHP
use sinri\SinriDatabaseAgent\test\TestPDO;
use sinri\SinriDatabaseAgent\test\TestMySQLi;
```

## Code Rule: PSR2 

### Check

	phpcs --report=full --standard=PSR2 --ignore=vendor . 
	phpcs --report=summary --standard=PSR2 --ignore=vendor . 

### Correct

	phpcbf --report=full --standard=PSR2 --ignore=vendor .

## License

SinriDatabaseAgent is published under MIT License.