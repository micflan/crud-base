<?php namespace Micflan\CrudBase;

use \Auth;
use \Input;
use \Str;
use \Eloquent;
use \Config;
use \Request;

class CrudBaseModel extends Eloquent {

    private $input = array();
    protected $softDelete = true;

    public function __construct() {
        if (empty($this->input)) $this->setInput();

        $hidden_fields = ['created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];
        if ($parent = Config::get('crud-base::parent')) {
            $hidden_fields[] = strtolower($parent.'_id');
        }
        $this->hidden = array_merge($hidden_fields, (array)$this->hidden);

        parent::__construct();
    }

    public function company() {
        if ($parent = Config::get('crud-base::parent') and $parent !== get_called_class()) {
            return $this->belongsTo($parent);
        }
    }

    public function setInput(array $input = null) {
        if (!$input) $input = Input::all();
        $this->input = [];
        foreach (static::$rules as $key => $value) {
            if (!empty($input[$key])) {
                $this->input[$key] = $input[$key];
            }
        }
        $this->input = $input;
        return $this;
    }

    public function getInput() {
        return $this->input;
    }

    public function getRules() {
        return static::$rules;
    }

    /**
     * Scope Active
     * Filter out inactive, expired or deleted objects
     */
    public function scopeActive($query) {
        if ($parent = Config::get('crud-base::parent')) {
            if ($parent !== get_called_class()) {
                $query->where(strtolower($parent.'_id'), '=', Auth::user()->{strtolower($parent.'_id')});
            } else {
                $query->where('id', Auth::user()->{strtolower($parent.'_id')});
            }
        }

        return $query;
    }

    /**
     * Scope Order
     * Apply default ordering to collection
     */
    public function scopeOrder($query, $order_by = 'url_title') {
        return $query->orderBy($order_by,'asc');
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

    /**
     * Get model values from array
     * Use model's validation $rules as keys
     */
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
    public function prepCollection(array $array = array(), $grouped = false, $group_by = 'url_title') {
        if ($grouped) {
            if ($this->group) {
                $array[$this->group][] = $this->toArray();
            } else {
                $group = substr($this->{$group_by}, 0, 1);
                $group = is_numeric($group) ? '0 ... 9' : $group;
                $array[strtoupper($group)][] = $this->toArray();
            }
        } else {
            $array[] = $this->toArray();
        }

        return $array;
    }

    public function scopeBuildResult($query, array $options = array()) {
        if (isset($options['page'])) {
            $paginated = true;
            $per_page = 20;
            unset($options['page']);
        } else {
            $paginated = false;
        }

        $grouped = isset($options['grouped']);
        $objects = $paginated ? $query->prep()->paginate($per_page) : $query->prep()->get();
        $result = [
            'data' => [],
            'current_page' => trim(Request::url() . '?' . ($paginated ? 'page='.($objects->getCurrentPage()).'&' : '')
                                                  . http_build_query($options), '?'),
            'next_page' => trim(Request::url() . '?' . ($paginated ? 'page='.($objects->getCurrentPage()+1).'&' : '')
                                               . http_build_query($options), '?'),
        ];

        // Organise collection
        foreach ($objects as $item)
            $result['data'] = $item->prepCollection($result['data'], $grouped);
        ksort($result['data']);


        return $paginated
                ? array_merge($objects->toArray(), $result)
                : $result;
    }
}
