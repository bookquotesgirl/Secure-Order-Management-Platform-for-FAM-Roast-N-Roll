<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('category', 40)->index();
            $table->string('description', 500)->nullable();
            $table->decimal('price', 8, 2);                  // ETB
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();     // not guessable / not sequential
            $table->foreignId('user_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->enum('status', ['placed', 'preparing', 'ready', 'collected', 'cancelled'])->default('placed');
            $table->timestamp('pickup_at')->nullable();
            $table->decimal('total', 9, 2);
            $table->string('notes', 300)->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained();
            $table->string('name_snapshot', 120);             // price/name at time of order
            $table->decimal('unit_price', 8, 2);
            $table->unsignedSmallInteger('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
    }
};
