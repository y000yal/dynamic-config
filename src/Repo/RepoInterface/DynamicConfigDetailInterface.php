<?php
namespace  GeniussystemsNp\DynamicConfig\Repo\RepoInterface;


use App\Repo\RepoInterface\BaseInterface;

interface DynamicConfigDetailInterface extends BaseInterface
{
    public function getDetailWithConfig($id);
}