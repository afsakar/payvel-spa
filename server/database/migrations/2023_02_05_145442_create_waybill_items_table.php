<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waybill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waybill_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('quantity');
            $table->integer('price');
            $table->foreign('waybill_id')->references('id')->on('waybills')->onDelete(null);
            $table->foreign('material_id')->references('id')->on('materials')->onDelete(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waybill_items');
    }
};
