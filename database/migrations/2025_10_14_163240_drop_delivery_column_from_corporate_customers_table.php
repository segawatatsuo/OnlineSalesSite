<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeliveryColumnFromCorporateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_customers', function (Blueprint $table) {
            $table->dropColumn('delivery_company_name');
            $table->dropColumn('delivery_department');
            $table->dropColumn('delivery_sei');
            $table->dropColumn('delivery_mei');
            $table->dropColumn('delivery_phone');
            $table->dropColumn('delivery_email');
            $table->dropColumn('delivery_zip');
            $table->dropColumn('delivery_add01');
            $table->dropColumn('delivery_add02');
            $table->dropColumn('delivery_add03');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_customers', function (Blueprint $table) {
            $table->string('delivery_company_name')->nullable();
            $table->string('delivery_department')->nullable()->comment('部署名');
            $table->string('delivery_sei')->nullable();
            $table->string('delivery_mei')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->string('delivery_email')->nullable();
            $table->string('delivery_zip')->nullable();
            $table->string('delivery_add01')->nullable();
            $table->string('delivery_add02')->nullable();
            $table->string('delivery_add03')->nullable();
        });
    }
}
