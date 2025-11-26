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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('section')->default('curated')->after('layout');
            // section: 'curated' or 'older'
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->integer('older_year_from')->nullable()->after('older_subheading');
            $table->integer('older_year_to')->nullable()->after('older_year_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('section');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['older_year_from', 'older_year_to']);
        });
    }
};
