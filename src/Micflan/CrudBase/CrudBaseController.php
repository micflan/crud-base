<?php namespace Micflan\CrudBase;

use \Input;
use \App;
use \Controller;
use \Response;
use \Route;

class CrudBaseController extends Controller {

    protected $data = array();
    protected $items;

    public function __construct() {
        $route = explode('.', Route::currentRouteName());
        $this->items = $this->items ?: App::make($route[0]);
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
        return $this->items->fillValues()->save()
                ? ['success' => true, 'data' => $this->items->prep()->find($this->items->id)->toArray()]
                : Response::json(['error' => true, 'validation_errors' => $this->items->errors()->all()], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return $this->items->prep()->find($id)
                ? ['data' => $items]
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

        return $obj->fillValues()->save()
                ? ['success' => true, 'data' => $obj->toArray()]
                : Response::json(['error' => true, 'validation_errors' => $obj->errors()->all()], 403);
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
