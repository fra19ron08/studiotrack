<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studio_slots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studio_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // Per MVP puoi tenere decimal; se vuoi Stripe-friendly meglio cents (integer).
            $table->unsignedInteger('price_cents')->default(0);

            // available | reserved | blocked
            $table->string('status')->default('available');

            $table->timestamps();

            // Query veloci: slot di uno studio in un giorno
            $table->index(['studio_id', 'start_at']);

            // Evita slot duplicati per lo stesso studio allo stesso start
            $table->unique(['studio_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_slots');
    }
};
