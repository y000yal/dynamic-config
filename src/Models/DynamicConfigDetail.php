<?php

namespace GeniussystemsNp\DynamicConfig\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicConfigDetail extends Model
{
    protected $fillable=['name','slug','description','required_fields','config','status','dynamic_config_id'];
    public function dynamicConfig() {
        return $this->belongsTo("App\Models\DynamicConfig", "dynamic_config_id");
    }
}
