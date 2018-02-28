<?php

use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'name' => str_random(10),
            'price' => 10,
        ]);
        DB::table('products')->insert([
            'name' => str_random(10),
            'price' => 15,
        ]);
    }
}
