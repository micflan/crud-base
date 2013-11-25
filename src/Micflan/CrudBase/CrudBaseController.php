<?php namespace Micflan\CrudBase;

use \Input;
use \Controller;
use \Response;

class CrudBaseController extends Controller {

    protected $data = array();

    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // Pagination listens for ?page=n in the query string
        $per_page = Input::has('page') ? 20 : $this->items->prep()->count();
        $result = $this->items->prep()->paginate($per_page);

        // Organise collection
        $data = [];
        foreach ($result as $item) {
            $data = $item->prepCollection($data, Input::has('grouped'));
        }

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
        $obj = $this->items;
        return $obj->fillValues()->save() ? ['success' => true, 'data' => $this->items->prep()->find($obj->id)->toArray()] : Response::json(['error' => true, 'validation_errors' => $obj->errors()->all()], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        return ['data' => $this->items->prep()->find($id)];
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

        return $obj->fillValues()->save() ? ['success' => true, 'data' => $obj->toArray()] : Response::json(['error' => true, 'validation_errors' => $obj->errors()->all()], 403);
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
