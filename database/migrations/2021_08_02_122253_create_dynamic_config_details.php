<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicConfigDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_config_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dynamic_config_id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->integer('status')->default(1);
            $table->text('description');
            $table->longText('required_fields');
            $table->longText('config');
            $table->timestamps();
            $table->foreign('dynamic_config_id')->references('id')->on('dynamic_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_config_details');
    }
}
