<?php

namespace GeniussystemsNp\DynamicConfig\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicConfig extends Model {
    protected $fillable = ['name', 'description', 'slug'];

    public function details() {
        return $this->hasMany(DynamicConfigDetail::class, "dynamic_config_id", "id");
    }
}
