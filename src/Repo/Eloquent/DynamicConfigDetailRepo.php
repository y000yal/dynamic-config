<?php
namespace  GeniussystemsNp\DynamicConfig\Repo\Eloquent;


use \GeniussystemsNp\DynamicConfig\Models\DynamicConfigDetail;
use App\Repo\Eloquent\BaseRepo;
use \GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigDetailInterface;



class DynamicConfigDetailRepo extends BaseRepo implements DynamicConfigDetailInterface {
    private $dynamic_config_detail;

    public function __construct(DynamicConfigDetail $dynamic_config_detail) {
        parent::__construct($dynamic_config_detail);
        $this->dynamic_config_detail = $dynamic_config_detail;
    }

    public function getDetailWithConfig($id) {
        $field = is_numeric($id) ? "id" : "slug";
        return $this->model->where($field, $id)
                           ->with('dynamicConfig')
                           ->firstOrFail();
    }

}