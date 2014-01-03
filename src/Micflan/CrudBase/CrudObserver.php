<?php namespace Micflan\CrudBase;

use \Auth;
use \Config;

class CrudObserver
{
    public function creating($item) {
        if (Auth::guest()) return false;
        if (Config::get('crud-base::object.company.enabled')) {
            $item->{Config::get('crud-base::object.company.join_field')} = Auth::user()->{Config::get('crud-base::object.company.join_field')};
        }
        $item->created_by = Auth::user()->id;
        $item->updated_by = Auth::user()->id;
    }

    public function updating($item) {
        if (Auth::guest() or $item->created_by !== Auth::user()->id) return false;
        $item->updated_by = Auth::user()->id;
    }
}
