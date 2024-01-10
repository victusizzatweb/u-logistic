<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("weight");
            $table->double("price", 10, 2)->nullable();
            $table->string("pick_up_address");
            $table->string("shipping_address");
            $table->string("description")->nullable();
            $table->integer("user_id");
            $table->integer("role_id");
            $table->integer("status")->default(1);
            $table->string("get_latitude")->nullable();
            $table->datetime('time');
            $table->string("get_longitude")->nullable();
            $table->string("to_go_latitude")->nullable();
            $table->string("to_go_longitude")->nullable();
            $table->integer('driver_id')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
