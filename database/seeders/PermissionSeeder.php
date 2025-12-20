<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            ['name' => 'account.create', 'description' => 'Create account'],
            ['name' => 'account.update', 'description' => 'Update account'],
            ['name' => 'account.changeState', 'description' => 'changeState'],
            ['name' => 'report.create', 'description' => 'Create reports'],
        ]);
        //// تحديد الصلاحيات
        $admin = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $teller = Role::where('name', 'teller')->first();
        $customer = Role::where('name', 'customer')->first();

 //////////////// صلاحيات المدير
        $admin->permissions()->sync(
            Permission::whereIn('name', [
                'account.changeState',
                'report.create'
            ])->pluck('id')
        );

        /////////////////صلاحيات ال المشرف
        $manager->permissions()->sync(
            Permission::whereIn('name', [
                'account.update',
                'account.changeState',
                'report.create'
            ])->pluck('id')
        );

        /////////////////// صلاحيات ال  teller
        $teller->permissions()->sync(
            Permission::whereIn('name', [
                'account.create'
            ])->pluck('id')
        );

        /////////////// صلاحيات الزبون

        $customer->permissions()->sync(
            Permission::whereIn('name', [
                'account.create'
            ])->pluck('id')
        );

    }
}
