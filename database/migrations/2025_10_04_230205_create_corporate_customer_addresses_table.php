<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_customer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['order', 'delivery'])->comment('どちらの住所か');
            $table->string('company_name')->nullable();
            $table->string('department')->nullable()->comment('部署名');
            $table->string('sei')->nullable();
            $table->string('mei')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('zip')->nullable();
            $table->string('add01')->nullable();
            $table->string('add02')->nullable();
            $table->string('add03')->nullable();
            $table->string('tel')->nullable();
            $table->string('fax')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_customer_addresses');
    }
}
