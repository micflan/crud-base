<?php namespace Micflan\CrudBase;

use \Input;
use \App;
use \Controller;
use \Response;
use \Route;
use \Validator;

class CrudBaseController extends Controller {

    protected $data = array();
    protected $items;

    public function __construct($name = null) {
        if (!$name) {
            $route = explode('.', Route::currentRouteName());
            $name = $route[0];
        }
        $this->items = $this->items ?: App::make($name);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // Pagination listens for ?page=n in the query string
        $per_page = Input::has('page') ? 20 : $this->items->prep()->count();
        $result   = $this->items->prep()->paginate($per_page);

        // Organise collection
        $data = [];
        foreach ($result as $item)
            $data = $item->prepCollection($data, Input::has('grouped'));

        $result              = $result->toArray();
        $result['data']      = $data;
        $result['next_page'] = '?' . (Input::has('page') ? 'page='.($result['current_page']+1).'&' : '')
                                   . http_build_query(Input::except(array('except' => 'page')));
        ksort($result['data']);
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        // Validate
        $validator = Validator::make($this->items->getInput(), $this->items->getRules());
        if ($validator->fails()) {
            return Response::json(['error' => true, 'validation_errors' => $validator->messages()->all()], 403);
        }

        // Save
        $this->items->fillValues()->save();

        // Return
        return ['success' => true, 'data' => $this->items->prep()->find($this->items->id)->toArray()];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $items = $this->items->prep()->find($id);
        return $items
                ? ['data' => $items->toArray()]
                : Response::json(['error' => true, 'message' => '404 Not Found'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        if (!$obj = $this->items->prep()->find($id))
            return Response::json(['error' => true, 'message' => '404 Not Found'], 404);

        // Validate
        $validator = Validator::make($obj->getInput(), $obj->getRules());
        if ($validator->fails()) {
            return Response::json(['error' => true, 'validation_errors' => $validator->messages()->all()], 403);
        }

        // Save
        $obj->fillValues()->save();

        // Return
        return ['success' => true, 'data' => $obj->toArray()];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }


}
