<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdersTableAmazonColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('amazon_chargePermissionId')->nullable()->after('id')->comment('与信ID（後でキャプチャに必要）');
            $table->string('amazon_chargeId')->nullable()->after('amazon_chargePermissionId')->comment('与信取引のID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('amazon_chargePermissionId');
            $table->dropColumn('amazon_chargeId');
        });
    }
}
