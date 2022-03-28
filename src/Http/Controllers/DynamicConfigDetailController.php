<?php
namespace GeniussystemsNp\DynamicConfig\Http\Controllers;


use GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigDetailInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Http\Request;

class DynamicConfigDetailController extends Controller {
    protected $dynamic_config_detail;
    protected $context;

    public function __construct(DynamicConfigDetailInterface $dynamic_config_detail) {
        $this->dynamic_config_detail = $dynamic_config_detail;
        $this->context = "dynamic_config_detail";


    }

    public function index(Request $request) {
        $params=$request->all();
        $params['sort_by'] = $request->get("sort_by", "desc");
        $params['sort_field']=$request->get("sort_field");
        $params["limit"] = $request->get("limit");;
        try {
            return $this->dynamic_config_detail->getAllWithParam($params, '10');
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }
    }

    public function store(Request $request) {
        try {
            $this->context = 'Store Config Details';
            $this->validate($request, [
                    "name"              => "required|string",
                    "dynamic_config_id" => "required|exists:dynamic_config_details,id",
                    "description"       => "sometimes",
                    "required_fields"   => "required|string",
                    "config"            => "required|string",
                    "status"            => "sometimes",
            ]);
        } catch (\Exception $ex) {
            return $this->message($ex->response->original, 422, $this->context);
        }


        try {
            $create = ["name"              => $request->input('name'),
                       "dynamic_config_id" => $request->input('dynamic_config_id'),
                       "slug"              => $this->dynamic_config_detail->createNewSlug($request->input('name')),
                       "description"       => $request->input('description'),
                       "required_fields"   => $request->input('required_fields'),
                       "config"            => $request->input('config'),
                       "status"            => ($request->input('status')) && $request->input('status') !== '' ? $request->input('status') : ''];

            $this->dynamic_config_detail->create($create);
            return $this->message("Config details added successfully.", 200, 'Store Config details');
        } catch
        (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, 'Store Config details');
        }
    }

    public function show($id) {
        try {
            $data = $this->dynamic_config_detail->getDetailWithConfig($id);
            return $this->response($data, 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);
        }
    }

    public function delete($id) {
        $this->context = 'Delete Config Detail';
        try {
            $data = $this->dynamic_config_detail->getSpecificByIdOrSlug($id);
            $this->dynamic_config_detail->delete($data->id);
            return $this->message("Config detail deleted successfully.", 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);

        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }

    }

    public function update($id, Request $request) {

        try {
            $this->context = 'Update Config';
            $configDetail = $this->dynamic_config_detail->getSpecificByIdOrSlug($id);
            try {
                $this->validate($request, [
                        "name"              => "required|string",
                        "dynamic_config_id" => "required",
                        "description"       => "sometimes",
                        "required_fields"   => "required|string",
                        "config"            => "required|string",
                        "status"            => "sometimes",
                ]);
            } catch (\Exception $ex) {
                return $this->message($ex->response->original, 422, $this->context);
            }
            $update = [
                    "name"              => $request->input('name'),
                    "dynamic_config_id" => $request->input('dynamic_config_id'),
                    "slug"              => $this->dynamic_config_detail->createNewSlug($request->input('name')),
                    "description"       => $request->input('description'),
                    "required_fields"   => $request->input('required_fields'),
                    "config"            => $request->input('config'),
                    "status"            => ($request->input('status')) && $request->input('status') !== '' ? $request->input('status') : ''
            ];
            $data = $this->dynamic_config_detail->update($configDetail->id, $update);
            return $this->response($data, 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }
    }
}