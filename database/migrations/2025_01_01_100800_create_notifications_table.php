<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Create Notifications Table
|--------------------------------------------------------------------------
| Keeps user-facing notifications for banking events. Designed to work
| alongside Laravel's notification tools while leaving room for custom
| observer-driven alerts.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('database');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->nullableMorphs('notifiable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

