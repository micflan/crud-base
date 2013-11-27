<?php namespace Micflan\CrudBase;

use \Route;
use \App;
use \File;

class CrudBaseRouter {

    public static function build($name, array $options = array()) {
        $model      = ucfirst($name);
        $controller = ucfirst($name.'Controller');

        App::bind($name, function($app) use ($model) { return new $model; });

        return Route::resource($name, class_exists($controller) ? $controller : '\Micflan\CrudBase\CrudBaseController', $options);
    }

}
