<?php

namespace  GeniussystemsNp\DynamicConfig\Repo\Eloquent;


use  \GeniussystemsNp\DynamicConfig\Models\DynamicConfig;
use App\Repo\Eloquent\BaseRepo;
use \GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigInterface;


class DynamicConfigRepo extends BaseRepo implements DynamicConfigInterface {
    private $dynamic_config;

    public function __construct(DynamicConfig $dynamic_config) {
        parent::__construct($dynamic_config);
        $this->dynamic_config = $dynamic_config;
    }

    public function getWithDetail($id) {
        $field = is_numeric($id) ? "id" : "slug";
        return $this->model->where($field, $id)
                           ->with('details')
                           ->firstOrFail();
    }
}