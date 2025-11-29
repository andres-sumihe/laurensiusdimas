<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Add work_type column (select field: freelance, full-time, contract, etc.)
            $table->string('work_type')->nullable()->after('subtitle');
            // Rename subtitle to work_description for clarity (keeping subtitle for backwards compat)
            $table->string('work_description')->nullable()->after('work_type');
        });
        
        // Migrate existing subtitle data to work_description
        DB::table('projects')->whereNotNull('subtitle')->update([
            'work_description' => DB::raw('subtitle'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['work_type', 'work_description']);
        });
    }
};
