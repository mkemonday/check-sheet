<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceTables extends Migration
{
    public function up(): void
    {
        // areas table
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // check_methods table
        Schema::create('check_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // check_items table
        Schema::create('check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('method_id')->constrained('check_methods')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // daily_checks table
        Schema::create('daily_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_item_id')->constrained('check_items')->onDelete('cascade');
            $table->date('check_date');
            $table->enum('status', ['checked', 'not_checked'])->nullable();
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();

            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_checks');
        Schema::dropIfExists('check_items');
        Schema::dropIfExists('check_methods');
        Schema::dropIfExists('areas');
    }
}
