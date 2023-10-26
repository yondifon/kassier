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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignUlid('price_id');
            $table->string('name');
            $table->string('status');
            $table->string('quantity')->default(1);
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at')->index();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'price_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
