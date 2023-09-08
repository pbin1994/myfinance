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
        Schema::table('clients', function (Blueprint $table) {
            $table->index('user_id');
        });        
        Schema::table('pendings', function (Blueprint $table) {
            $table->index('user_id');
        });        
        Schema::table('debts', function (Blueprint $table) {
            $table->index('user_id');
        });       
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('clients', function (Blueprint $table) {
        $table->dropIndex('user_id');
    });
       Schema::table('pendings', function (Blueprint $table) {
        $table->dropIndex('user_id');
    });        
       Schema::table('debts', function (Blueprint $table) {
        $table->dropIndex('user_id');
    });       
       Schema::table('transactions', function (Blueprint $table) {
        $table->dropIndex('user_id');
        $table->dropIndex('client_id');
    });
   }
};
