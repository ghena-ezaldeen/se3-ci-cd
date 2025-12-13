<?php

namespace Database\Seeders;

/*
|--------------------------------------------------------------------------
| Database Seeder
|--------------------------------------------------------------------------
| Entry point for seeding the banking system. It delegates to the focused
| banking seeder so base Laravel seeds stay clean while module-specific
| data remains organized.
*/

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            BankingSeeder::class,
        ]);
    }
}
