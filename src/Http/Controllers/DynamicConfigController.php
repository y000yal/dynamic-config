<?php

namespace GeniussystemsNp\DynamicConfig\Http\Controllers;

use GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Http\Request;

class DynamicConfigController extends Controller {
    protected $dynamic_config;
    protected $context;

    public function __construct(DynamicConfigInterface $dynamic_config) {
        $this->dynamic_config = $dynamic_config;
        $this->context = "dynamic_config";

    }

    public function index(Request $request) {
        $params=$request->all();
        $params['sort_by'] = $request->get("sort_by", "desc");
        $params['sort_field']=$request->get("sort_field");
        $params["limit"] = $request->get("limit");
        try {
            $all = $this->dynamic_config->getAllWithParam($params, '');
            return $all;
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);

        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }
    }

    public function store(Request $request) {
        try {
            $this->validate($request, [
                    "name"        => "required|string",
                    "description" => "sometimes",
            ]);
        } catch (\Exception $ex) {
            return $this->message($ex->response->original, 422, $this->context);
        }
        try {
            $create = [
                    "name"        => $request->input('name'),
                    "slug"        => $this->dynamic_config->createNewSlug($request->input('name')),
                    "description" => $request->input('description')
            ];
            $this->dynamic_config->create($create);
            return $this->message("Config created successfully.", 200, 'Store Config');
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, 'Store Config');
        }
    }

    public function show($id) {
        try {
            $data = $this->dynamic_config->getWithDetail($id);
            return $this->response($data, 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);
        }
    }

    public function delete($id) {
        $this->context = 'Delete Configs';
        try {
            $data = $this->dynamic_config->getSpecificByIdOrSlug($id);
            $this->dynamic_config->delete($data->id);
            return $this->message("config deleted successfully.", 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);

        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }

    }

    public function update($id, Request $request) {
        try {
            $this->context = 'Update Config';
            $config = $this->dynamic_config->getSpecificByIdOrSlug($id);
            try {
                $this->validate($request, [
                        "name"        => "required|string",
                        "description" => "sometimes",
                ]);
            } catch (\Exception $ex) {
                return $this->message($ex->response->original, 422, $this->context);
            }
            $update = [
                    "name"        => $request->input('name'),
                    "slug"        => $this->dynamic_config->createNewSlug($request->input('name')),
                    "description" => $request->input('description')
            ];
            $config = $this->dynamic_config->update($config->id, $update);
            return $this->response($config, 200, $this->context);
        } catch (ModelNotFoundException $ex) {
            return $this->message("No record found", 204, $this->context);
        } catch (\Exception $ex) {
            return $this->message($ex->getMessage(), 500, $this->context);

        }
    }
}