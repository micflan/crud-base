# JSON flavoured CRUD Base for Laravel 4 and Ardent

Simply extend the provided CrudBaseModel and CrudBaseController to effortlessly create a basic JSON CRUD API.

## Installation

Add or ammend to composer.json:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/micflan/crud-base"
        }
    ],
    "require": {
        "micflan/crud-base": "*"
    }
}
```

Run `composer update`

Add to /app/config/app.php, providers array:
```php
        'Micflan\CrudBase\CrudBaseServiceProvider',
```

## Basic Setup

Add to routes.php
```php
    Route::resource('client', 'ClientController');
```

Example Model
```php
<?php

use Micflan\CrudBase\CrudBaseModel;

class Client extends CrudBaseModel {

    public static $rules = array(
        'name'       => 'required',
        'url_title'  => 'required',
        'phone'      => '',
        'email'      => '',
        'address'    => '',
        'twitter'    => '',
        'facebook'   => '',
        'notes'      => '',
        'deleted_at' => ''
    );

}

```

Example Controller
```php
<?php

use Micflan\CrudBase\CrudBaseController;

class ClientController extends CrudBaseController {

    public function __construct() {
        $this->items  = new Client;
        parent::__construct();
    }

}
```


---

## TODO
- More README! (requirements, usage, extending)
- Test all the things.

