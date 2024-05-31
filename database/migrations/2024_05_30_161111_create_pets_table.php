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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->string('name');
            $table->float('age');
            $table->float('weight');
            $table->string('breed');
            $table->string('color');
            $table->enum('size', ['small', 'medium', 'large']);
            $table->enum('type', ['dog', 'cat']);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['proposal_id']);
        });
        Schema::dropIfExists('pets');
    }
};
