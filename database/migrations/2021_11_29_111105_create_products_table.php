<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
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
            $table->string('name');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('image');
            $table->date('endDate');
            $table->string('category');//TODO
            $table->string('contact');
            $table->double('quantity')->default(1);
            $table->double('price');
            $table->integer('days');
            $table->integer('r1');
            $table->integer('r2');
            $table->integer('r3');
            $table->double('main_price');
            $table->double('dis1');
            $table->double('dis2');
            $table->double('dis3');
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
        Schema::dropIfExists('products');
    }
}
