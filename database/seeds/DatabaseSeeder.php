<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $tables = ['users'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        User::create([
            'email' => 'victor@niculae.net',
            'name' => 'Administrator',
            'password' => 'admin',
        ]);

        User::create([
            'email' => 'alt-tab@gmail.com',
            'name' => 'Another User',
            'password' => 'admin',
        ]);
    }
}
