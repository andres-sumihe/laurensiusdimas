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
        // For MySQL, we need to modify the ENUM directly to add 'youtube'
        DB::statement("ALTER TABLE project_media MODIFY COLUMN type ENUM('image', 'video', 'youtube') DEFAULT 'image'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'youtube' records to 'video' to prevent data loss
        DB::table('project_media')->where('type', 'youtube')->update(['type' => 'video']);
        
        // Then modify the ENUM back
        DB::statement("ALTER TABLE project_media MODIFY COLUMN type ENUM('image', 'video') DEFAULT 'image'");
    }
};
