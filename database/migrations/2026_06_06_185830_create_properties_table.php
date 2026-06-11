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
            $table->softDeletes();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('location');
            $table->index('price');
            $table->index('bedrooms');
            $table->index('status');

            $table->enum('property_type', ['apartment', 'house', 'villa', 'commercial', 'land', 'townhouse'])->default('apartment')->after('description');
            $table->enum('listing_type', ['sale', 'rent', 'short_stay'])->default('rent')->after('property_type');
            $table->enum('furnishing', ['furnished', 'semi_furnished', 'unfurnished'])->default('unfurnished')->after('listing_type');
            $table->json('amenities')->nullable()->after('furnishing');
            $table->string('featured_tag')->nullable()->after('is_featured');
            $table->integer('popularity_score')->default(0)->after('views_count');
            $table->timestamp('last_viewed_at')->nullable()->after('popularity_score');
            
            $table->index('property_type');
            $table->index('listing_type');
            $table->index('popularity_score');
            
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
