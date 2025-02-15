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
        Schema::create('post_comments', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("forum_post_id")->unsigned();
            $table->integer("customer_id")->unsigned();
            $table->integer("likes")->default(0);
            $table->longText("comment");
            $table->string("comment_type")->default("main");
            $table->timestamps();
            $table->foreign('forum_post_id')->references('id')->on('forum_posts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
