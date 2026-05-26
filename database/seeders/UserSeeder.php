<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
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
        $this->assignPermissionsToRoles($roles, $permissions);
        $this->createUsers($roles);
    }

    private function createRoles() : array {
        return [
            'admin' =>  Role::firstOrCreate(['name' => 'admin']),
            'owner' =>  Role::firstOrCreate(['name' => 'owner']),
            'user' =>  Role::firstOrCreate(['name' => 'user']),
        ];
    }

    private function createPermissions() : Collection {
        $permissions = [
            'Create Movie',
            'Read Movie',
            'Edit Movie',
            'Delete Movie',

            'Create Theater',
            'Read Theater',
            'Edit Theater',
            'Delete Theater',

            'Create Screen',
            'Read Screen',
            'Edit Screen',
            'Delete Screen',

            'Create Movie Show',
            'Read Movie Show',
            'Edit Movie Show',
            'Delete Movie Show',
        ];

        foreach ($permissions as $value) {
            Permission::firstOrCreate(['name' => $value]);
        }

        return Permission::all();
    }

    private function assignPermissionsToRoles(array $roles, Collection $permissions) : void {
        $roles['admin']->syncPermissions($permissions);
        $roles['owner']->syncPermissions([
            'Create Movie',

            'Create Theater',
            'Read Theater',
            'Edit Theater',
            'Delete Theater',

            'Create Screen',
            'Read Screen',
            'Edit Screen',
            'Delete Screen',

            'Create Movie Show',
            'Read Movie Show',
            'Edit Movie Show',
            'Delete Movie Show',
        ]);

        $roles['user']->syncPermissions([
            'Read Movie',
            'Read Theater',
            'Read Screen',
            'Read Movie Show',
        ]);
    }

    private function createUsers(array $roles) : void {
        $admin = User::firstOrCreate(
            ['email' => 'admin2@portal.me'],
            [
                'name' => "Admin User",
                'password' => Hash::make('Password@1'),
            ]
        );
        $admin->assignRole($roles['admin']);

        $admin = User::firstOrCreate(
            ['email' => 'theater.owner@portal.me'],
            [
                'name' => "Theater Owner",
                'password' => Hash::make('Password@1'),
            ]
        );
        $admin->assignRole($roles['owner']);

        $admin = User::firstOrCreate(
            ['email' => 'user@portal.me'],
            [
                'name' => "Viewer User",
                'password' => Hash::make('Password@1'),
            ]
        );
        $admin->assignRole($roles['user']);
    }
}
