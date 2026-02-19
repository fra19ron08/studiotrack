<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('studio_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('slot_id')
                ->constrained('studio_slots')
                ->cascadeOnDelete();

            // pending_payment | confirmed | canceled
            $table->string('status')->default('pending_payment');

            $table->unsignedInteger('total_cents')->default(0);

            // Stripe (opzionale per dopo)
            $table->string('payment_intent_id')->nullable();

            $table->timestamps();

            // Regola d’oro: 1 slot può avere UNA sola prenotazione
            $table->unique(['slot_id']);

            $table->index(['studio_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
