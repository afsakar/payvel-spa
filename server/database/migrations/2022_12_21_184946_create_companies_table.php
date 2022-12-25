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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('owner')->nullable();
            $table->string('tel_number');
            $table->string('gsm_number')->nullable();
            $table->string('fax_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number')->nullable()->unique();
            $table->string('logo')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('companies');
    }
};
