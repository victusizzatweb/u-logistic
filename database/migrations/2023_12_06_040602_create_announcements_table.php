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
            $table->string("pick_up_address");
            $table->string("shipping_address");
            $table->string("date");
            $table->integer("time");
            $table->string("description");
            $table->string("user_id");
            $table->string("role_id");
            // $table->string("images");
            $table->string("status")->default("1");
            $table->string("get_latitude");
            $table->string("get_longitude");
            $table->string("to_go_latitude");
            $table->string("to_go_longitude");
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
