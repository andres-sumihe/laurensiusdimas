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
        // Rename the table
        Schema::rename('corporate_project_media', 'project_media');
        
        // Modify layout to be nullable (for non-corporate projects that use project.layout instead)
        Schema::table('project_media', function (Blueprint $table) {
            $table->string('layout')->nullable()->change();
        });
        
        // Add slot_index for grid-based layouts (curated/older projects)
        Schema::table('project_media', function (Blueprint $table) {
            $table->integer('slot_index')->nullable()->after('layout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_media', function (Blueprint $table) {
            $table->dropColumn('slot_index');
        });
        
        Schema::rename('project_media', 'corporate_project_media');
    }
};
