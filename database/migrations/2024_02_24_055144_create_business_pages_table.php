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
        Schema::create('business_pages', function (Blueprint $table) {
            $table->id();
            $table->integer("business_id")->unsigned();
            $table->enum("file_position", ["banner", "slider", "catalogue"]);
            $table->enum("file_type", ["image", "video"]);
            $table->text("file_name");
            $table->text("file_url");
            $table->text("file_size");
            $table->text("file_extension");
            $table->timestamps();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_pages');
    }
};
