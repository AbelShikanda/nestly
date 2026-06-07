<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->string('neighborhood')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('area_sqft')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('price_period')->default('monthly');
            $table->string('main_image')->nullable();
            $table->enum('status', ['active', 'pending', 'sold', 'rented', 'inactive'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('location');
            $table->index('price');
            $table->index('bedrooms');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
