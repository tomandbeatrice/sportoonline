<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagePathToVariantsTable extends Migration
{
    public function up()
    {
        // color zaten var, tekrar eklenmemeli!
        // image kolonu da ana tabloda varsa burası boş kalabilir
    }

    public function down()
    {
        // varsa geri alma işlemini buraya ekleyebilirsin
    }
}