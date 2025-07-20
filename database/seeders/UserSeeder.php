<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = $this->createRoles();
        $permissions = $this->createPermissions();
        // $this->assignPermissinRoles();
    }

    private function createRoles() : array {
        return [
            'admin' =>  Role::firstOrCreate(['name' => 'admin']),
            'owner' =>  Role::firstOrCreate(['name' => 'owner']),
            'viewer' =>  Role::firstOrCreate(['name' => 'viewer']),
        ];
    }

    private function createPermissions() : Collection {
        $permissions = [
            'Create Movie',
            'Read Movie',
            'Edit Movie',
            'Delete Movie',
        ];

        foreach ($permissions as $value) {
            Permission::firstOrCreate(['name' => $value]);
        }

        return Permission::all();
    }

    private function createUsers() : array {
        return [];
    }
}
