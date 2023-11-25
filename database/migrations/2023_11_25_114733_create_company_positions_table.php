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
        if(!Schema::hasTable('company_positions')){
            Schema::create('company_positions', function (Blueprint $table) {
                $table->id();
                $table->string('company_id')->index()->references('company_id')->on('companies');
                $table->string('user_id')->index()->references('user_id')->on('users');
                $table->string('position');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_positions');
    }
};
