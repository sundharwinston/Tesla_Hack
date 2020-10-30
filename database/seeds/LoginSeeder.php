<?php

use Illuminate\Database\Seeder;

class LoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();
        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
        ]);

        DB::table('expense_categories')->insert([
            'expense_type' => 'Salary',
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
    }
}
