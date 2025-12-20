<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Create Account Features Table
|--------------------------------------------------------------------------
| Stores decorators applied to accounts (e.g., overdraft, rewards) with
| flexible metadata, enabling layered capabilities without altering core
| account records.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('feature_name');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_features');
    }
};

