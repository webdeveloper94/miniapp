<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fees', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 12, 2)->comment('Minimal summa');
            $table->decimal('max_amount', 12, 2)->comment('Maksimal summa');
            $table->decimal('fee_percentage', 5, 2)->comment('Xizmat haqi foizi');
            $table->boolean('is_active')->default(true)->comment('Faol');
            $table->integer('sort_order')->default(0)->comment('Tartib raqami');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};
