<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // allows trasnactions

            $table->ulid('id')->primary();     
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->string('category')->nullable();
            $table->text('explanation')->nullable();
             $table->float('confidence')->nullable();
            $table->text('note')->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
