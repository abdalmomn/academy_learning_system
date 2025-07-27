<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
<<<<<<< HEAD
            CategorySeeder::class,
            CourseSeeder::class

=======
>>>>>>> ca7ced0 (first version: database, models and spatie role)
        ]);
    }
}
