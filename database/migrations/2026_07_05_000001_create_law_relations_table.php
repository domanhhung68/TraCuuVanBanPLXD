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
        Schema::create('law_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_law_id')->constrained('laws')->cascadeOnDelete();
            $table->foreignId('to_law_id')->constrained('laws')->cascadeOnDelete();
            $table->string('relation_type');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['from_law_id', 'to_law_id', 'relation_type']);
            $table->index('from_law_id');
            $table->index('to_law_id');
            $table->index('relation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('law_relations');
    }
};
