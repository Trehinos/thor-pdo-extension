# Thor PDO Extension

A tiny helper library around PHP's PDO to make connecting and running SQL easier, with a simple Criteria builder and small CRUD helper.

- Handler: lazy-connecting PDO wrapper
- Requester: execute parameterized queries conveniently
- Criteria: build WHERE clauses from arrays
- ArrayCrud: minimal CRUD for associative array rows
- PdoCollection: manage multiple connections

## Installation

Install via Composer:

```
composer require trehinos/thor-pdo-extension
```

Requires PHP 8.2+ and ext-pdo.

## Quick Start

Create a connection and run queries:

```php
use Thor\Database\PdoExtension\Handler;
use Thor\Database\PdoExtension\Requester;

$handler = new Handler('sqlite::memory:');
$req = new Requester($handler);
$req->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL)');
$req->execute('INSERT INTO users (name) VALUES (?)', ['Alice']);
$row = $req->request('SELECT * FROM users WHERE name = ?', ['Alice'])->fetch();
```

Build WHERE clauses with Criteria:

```php
use Thor\Database\PdoExtension\Criteria;

$criteria = new Criteria([
    'company' => 'Xerox',
    'OR' => [
        'employee_id' => [1, 14, 999],
        'manager_id'  => 4,
    ],
]);
$sql    = Criteria::getWhere($criteria); // WHERE company = ? AND ("employee_id" IN (?,?,?) OR manager_id = ?)
$params = $criteria->getParams();        // ['Xerox', 1, 14, 999, 4]
```

Minimal CRUD with ArrayCrud:

```php
use Thor\Database\PdoExtension\ArrayCrud;
use Thor\Database\PdoExtension\Criteria;

$crud = new ArrayCrud('users', ['id'], $req);
$crud->createOne(['id' => 1, 'name' => 'Alice']);
$user = $crud->readOne(['id' => 1]);
$crud->update(['name' => 'Alice Cooper'], new Criteria(['id' => 1]));
$crud->delete(new Criteria(['id' => 1]));
```

Manage multiple connections with PdoCollection:

```php
use Thor\Database\PdoExtension\PdoCollection;
use Thor\Database\PdoExtension\Handler;

$pdos = (new PdoCollection())
    ->add('default', new Handler('sqlite::memory:'))
    ->add('analytics', new Handler('sqlite::memory:'));
$pdo = $pdos->get('default')?->getPdo();
```

## Testing

This project uses PHPUnit 10. To run the test suite:

```
./vendor/bin/phpunit --colors=always
```

The tests use an in-memory SQLite database, so no external services are required.

## License
&copy; 2023-2025 Sébastien Geldreich
Distributed under the MIT License.
