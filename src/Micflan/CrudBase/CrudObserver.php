<?php namespace Micflan\CrudBase;

use \Auth;

class CrudObserver
{
    public function creating($item) {
        if (Auth::guest()) return false;
        $item->company_id = Auth::user()->company_id;
        $item->created_by = Auth::user()->id;
        $item->updated_by = Auth::user()->id;
    }

    public function updating($item) {
        if (Auth::guest() or $item->created_by !== Auth::user()->id) return false;
        $item->updated_by = Auth::user()->id;
    }
}
