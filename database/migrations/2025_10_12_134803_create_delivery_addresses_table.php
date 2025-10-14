<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_customer_id')
                ->constrained()
                ->onDelete('cascade'); // 顧客削除時に配送先も削除
            $table->string('type')->default('delivery');
            $table->string('company_name')->nullable();
            $table->string('department')->nullable();
            $table->string('sei')->nullable();
            $table->string('mei')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('zip')->nullable();
            $table->string('add01')->nullable();
            $table->string('add02')->nullable();
            $table->string('add03')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_addresses');
    }
}
