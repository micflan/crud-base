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

    public function __construct(CrudBaseModel $items) {
        $this->items = $items;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        // Pagination listens for ?page=n in the query string
        return $this->items->buildResult(Input::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

        // Fill values from input
        $obj = $this->items->fillValues();

        // Validate
        $validator = Validator::make($obj->toArray(), $obj->getRules());
        if ($validator->fails()) {
            return Response::json(['error' => true, 'validation_errors' => $validator->messages()->all()], 403);
        }

        // Save
        $obj->save();

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
        $data = $this->items->where('id','=',$id)->buildResult();
        return Response::json($data, count($data['data']) > 0 ? 200 : 404);
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
