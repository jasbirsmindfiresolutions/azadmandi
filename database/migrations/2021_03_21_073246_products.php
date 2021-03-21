<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['fruit', 'vegitable']);
            $table->integer('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price_per_kg');
            $table->boolean('is_dynamic_price_enabled')->default('0');
            $table->boolean('is_out_of_stock')->default(0);
            $table->integer('total_stock')->nullable();
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
        //
    }
}
