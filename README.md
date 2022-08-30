# PHP AirTable

An Airtable Client.

## Installation

Require this package with composer.
```shell
composer require anthonypauwels/airtable
```

### Laravel without auto-discovery:

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`:
```php
Anthonypauwels\AirTable\Laravel\ServiceProvider::class,
```

Then add this line to your facades in `config/app.php`:
```php
'AirTable' => Anthonypauwels\AirTable\Laravel\AirTable::class,
```

## Usage

```php
use Anthonypauwels\AirTable\AirTable;

$airTable = new AirTable( [
    'key' => 'key**************',
    'base' => 'app**************',
    'url' => AirTable::API_URL,
] );

$recordsA = $airTable->table('Your table')->view('View')->get();
$recordsB = $airTable->table('Another table')->where('key', '=', 'value' )->view('In view this view')->get();
```

### With Laravel

Define your environment variables into your .env file :
```dotenv
AIRTABLE_KEY="key**************"
AIRTABLE_BASE="app**************"
```

The package provides by default a Facade for Laravel application. You can call methods directly using the Facade or use the alias instead.
```php
use Anthonypauwels\AirTable\Laravel\AirTable;

$recordsA = AirTable::table('Your table')->view('View')->get();
```

### API documentation

#### AirTable
```php
/**
 * Get a builder for a table from the default base
 */
function table(string $table_name): Builder;

/**
 * Get a base
 */
function on(string $base_id): Base;
```

#### Base
```php
/**
 * Get a builder for a table 
 */
function table(string $table_name): Builder;
```

#### Client
```php
/**
 * Count the number of elements inside the query
 */
function count(): int;

/**
 * If AirTable must perform an automatic data conversion from string values
 */
function typecast(bool $value): Builder;

/**
 * Delay between request
 */
function delay(int $value): Builder;

/**
 * Search for specific fields from records
 */
function fields(array|string $fields): Builder;

/**
 * Filter records using a logical where operation
 */
function where(string $field, mixed $operator, $value = null): Builder;

/**
 * Filter records using a raw query
 */
function whereRaw(string $formula): Builder;

/**
 * Get records from a specific view
 */
function view(string $view_name): Builder;

/**
 * Order records by a field and direction
 */
function orderBy(string $field, string $direction = 'asc'): Builder;

/**
 * Set the limit value to get a limited number of records
 */
function limit(int $value): Builder;

/**
 * Alias to limit method
 */
function take(int $value): Builder;

/**
 * Set the offset value to get records from a specific page
 */
function offset(int $value): Builder;

/**
 * Alias to offset method
 */
function skip(int $value): Builder;

/**
 * Get records with a limit of 100 by page
 */
function get(): array;

/**
 * Method alias to get, return all records
 */
function all(): array;

/**
 * Get the first record
 */
function first(): array;

/**
 * Find a record using his ID
 */
function find(string $id): array;

/**
 * Insert a record
 */
function insert(array $data): array;

/**
 * Update a record or many records. Destructive way
 */
function update(array|string $id, array $data = null): array;

/**
 * Patch a single record or many records
 */
function patch(array|string $id, array $data = null): array;

/**
 * Delete a single record
 */
function delete(string $id): array;
```

### Requirement

PHP 8.0 or above