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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('portfolio_heading')->nullable()->after('hero_subheadline');
            $table->string('portfolio_subheading')->nullable()->after('portfolio_heading');
            $table->string('corporate_heading')->nullable()->after('portfolio_subheading');
            $table->string('corporate_subheading')->nullable()->after('corporate_heading');
            $table->string('older_heading')->nullable()->after('corporate_subheading');
            $table->string('older_subheading')->nullable()->after('older_heading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'portfolio_heading',
                'portfolio_subheading',
                'corporate_heading',
                'corporate_subheading',
                'older_heading',
                'older_subheading',
            ]);
        });
    }
};
