<?php namespace Micflan\CrudBase;

use \Route;
use \App;
use \File;
use \Config;

class CrudBaseRouter {

    public static function build($name, array $options = array()) {

        !empty($options) or $options = ['index','store','show','update','destroy'];

        $namespace = Config::get('crud-base::namespace') . '\\';
        $model = $namespace . ucfirst($name);
        $controller = $model . 'Controller';

        $controller = class_exists($controller)
                    ? new $controller(new $model)
                    : new CrudBaseController(new $model);

        foreach($options as $o) {
            switch ($o) {
                case 'index':
                    Route::get($name, function() use ($controller) {
                        return $controller->index();
                    });
                    break;
                case 'store':
                    Route::post($name, function() use ($controller) {
                        return $controller->store();
                    });
                    break;
                case 'show':
                    Route::get($name.'/{id}', function($id) use ($controller) {
                        return $controller->show($id);
                    });
                    break;
                case 'update':
                    Route::put($name.'/{id}', function($id) use ($controller) {
                        return $controller->put($id);
                    });
                    break;
                case 'delete':
                    Route::delete($name.'/{id}', function($id) use ($controller) {
                        return $controller->delete($id);
                    });
                    break;
            }
        }

        return true;
    }

}
