<?php namespace Micflan\CrudBase;

use \Auth;
use \Input;
use \Str;
use \LaravelBook\Ardent\Ardent;

class CrudBaseModel extends Ardent {

    private $input = array();
    protected $softDelete = true;

    public function __construct() {
        $this->input = $this->input ?: Input::all();
        $this->hidden = array_merge(['created_by', 'updated_by', 'created_at', 'updated_at', 'company_id', 'deleted_at'], (array)$this->hidden);
        parent::__construct();
    }

    public function company() { return $this->belongsTo('Company'); }

    public function setInput(array $input) {
        $this->input = $input;
        return $this;
    }

    /**
     * Scope Active
     * Filter out inactive, expired or deleted objects
     */
    public function scopeActive($query) {
        return $query->where('company_id','=',Auth::user()->company_id);
    }

    /**
     * Scope Order
     * Apply default ordering to collection
     */
    public function scopeOrder($query) {
        return $query->orderBy('url_title','asc');
    }

    /**
     * Scope Pre-Fetch
     * Perform common collection filters
     */
    public function scopeOrderByInput($query) {
        if ($order_by = array_pull($this->input, 'order_by') and key_exists($order_by, static::$rules)) {
            $query->orderBy($order_by, array_pull($this->input, 'order_direction') === 'desc' ?: 'asc');
        }

        return $query;
    }

    /**
     * Scope Pre-Fetch
     * Perform common collection filters
     */
    public function scopePrep($query) {
        return $query->orderByInput()->order()->active();
    }

    /**
     * Set url_title Attribute
     * Iterate url_title until value is unique
     */
    public function setUrlTitleAttribute($value) {
        $original = Str::slug($value);
        $url_title = $original;

        $i = 1;
        while (self::where('url_title','=',$url_title)->where('id','!=',$this->id ?: 'abc')->count() > 0) {
            $url_title = $original."-$i";
            $i++;
        }

        $this->attributes['url_title'] = $url_title;
    }

    public function fillValues(array $input = array()) {
        $input = $input ?: $this->input;

        foreach(static::$rules as $key => $value)
            if (!empty($input[$key])) $this->{$key} = $input[$key];

        if (!empty(static::$rules['url_title']) and !empty($this->name))
            $this->url_title = Str::slug($this->name);

        return $this;
    }

    /**
     * To Array
     * Transform object to array
     */
    public function toArray() {
        $array = parent::toArray();
        return $array;
    }

    /**
     * Prep Collection
     * Transforms objects to array and group if necessary
     */
    public function prepCollection(array $array = null, $grouped = false) {
        if ($grouped) {
            if ($this->group) {
                $array[$this->group][] = $this->toArray();
            } else {
                $group = substr($this->url_title, 0, 1);
                $group = is_numeric($group) ? '0 ... 9' : $group;
                $array[strtoupper($group)][] = $this->toArray();
            }
        } else {
            $array[] = $this->toArray();
        }

        return $array;
    }

    public function beforeSave() {
        $this->company_id = Auth::user()->company_id;
        $this->updated_by = Auth::user()->id;
        if (!$this->created_by) $this->created_by = Auth::user()->id;
    }
}
