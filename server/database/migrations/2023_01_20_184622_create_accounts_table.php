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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_type_id');
            $table->unsignedBigInteger('currency_id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->foreign('account_type_id')->references('id')->on('account_types')->onDelete(null);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete(null);
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
        Schema::dropIfExists('accounts');
    }
};
