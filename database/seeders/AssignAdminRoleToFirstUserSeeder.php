<?php

    namespace Database\Seeders;

    use App\Models\User;
    use Illuminate\Database\Seeder;
    use Spatie\Permission\Models\Role;

    class AssignAdminRoleToFirstUserSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $firstUser = User::first();

            if ($firstUser) {
                $adminRole = Role::where('name', 'admin')->first();
                if (!$firstUser->hasRole('admin')) {
                    $firstUser->assignRole($adminRole);
                }
            }
        }


    }

