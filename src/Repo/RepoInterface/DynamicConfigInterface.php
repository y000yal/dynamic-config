<?php
namespace  GeniussystemsNp\DynamicConfig\Repo\RepoInterface;


use App\Repo\RepoInterface\BaseInterface;

interface DynamicConfigInterface extends BaseInterface
{
    public function create(array $data);
    public function getWithDetail($id);
}