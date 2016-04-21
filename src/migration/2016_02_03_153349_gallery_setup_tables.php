<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GallerySetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->boolean('state')->default(0);
            $table->timestamps();
        });

        Schema::create('photo', function(Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->integer('position');
            $table->boolean('state')->default(1;

            $table->integer('gallery_id')->unsigned()->index();
            $table->foreign('gallery_id')->references('id')->on('gallery')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('gallery_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('gallery_gallery_categories', function(Blueprint $table) {
            $table->integer('gallery_id')->unsigned()->index();
            $table->foreign('gallery_id')->references('id')->on('gallery')->onDelete('cascade');
            $table->integer('gallery_categories_id')->unsigned()->index();
            $table->foreign('gallery_categories_id')->references('id')->on('gallery_categories')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('post_blog_categories');
    }
}
