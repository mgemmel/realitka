<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealitneKancelarieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realitne_kancelarie', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kraj_id');
            $table->string('nazov');
            $table->string('ulica_cislo');
            $table->string('mesto');
            $table->integer('PSC');
            $table->string('kontaktna_osoba');
            $table->string('telefon');
            $table->string('email');
            $table->string('ICO');
            $table->string('DIC');
            $table->string('url_logo')->nullable();
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
        Schema::dropIfExists('realitne_kancelarie');
    }
}