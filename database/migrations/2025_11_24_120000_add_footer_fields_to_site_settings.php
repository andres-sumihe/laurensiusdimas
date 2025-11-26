<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('footer_text')->nullable()->after('social_links');
            $table->string('footer_cta_label')->nullable()->after('footer_text');
            $table->string('footer_cta_url')->nullable()->after('footer_cta_label');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['footer_text', 'footer_cta_label', 'footer_cta_url']);
        });
    }
};
