<?php namespace Micflan\CrudBase;

use \Auth;
use \Config;

class CrudObserver
{
    public function creating($item) {
        if (Auth::guest()) return false;

        if ($parent = Config::get('crud-base::parent')) {
            $parent_id = strtolower($parent.'_id');
            if (isset($item->parent_id)) {
                $item->{$parent_id} = Auth::user()->{$parent_id};
            }
        }

        $item->created_by = Auth::user()->id;
        $item->updated_by = Auth::user()->id;
    }

    public function updating($item) {
        if (Auth::guest() or $item->created_by !== Auth::user()->id) return false;
        $item->updated_by = Auth::user()->id;
    }

    public function restoring($item) {
        dd('here');
    }

}
