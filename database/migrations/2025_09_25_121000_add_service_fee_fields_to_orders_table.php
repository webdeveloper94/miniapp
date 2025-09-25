<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('service_fee_percent', 5, 2)->nullable()->after('total_price');
            $table->decimal('service_fee_amount', 12, 2)->default(0)->after('service_fee_percent');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['service_fee_percent', 'service_fee_amount']);
        });
    }
};


