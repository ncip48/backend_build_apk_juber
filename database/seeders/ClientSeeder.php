<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //generate multi client
        Client::insert([
            [
                'name' => 'Juber',
                'folder' => 'juber',
                'username' => 'oto',
                'password' => 'password',
                'version' => '1.0.24',
                'package' => 'id.juber.com'
            ],
            [
                'name' => 'BmTronik',
                'folder' => 'bmtronik',
                'username' => 'bmtronik',
                'password' => 'password',
                'version' => '1.0.24',
                'package' => 'id.bmtronik.com',
            ],
            [
                'name' => 'KemangTravel',
                'folder' => 'kemangtravel',
                'username' => 'kemangnt',
                'password' => 'password',
                'version' => '1.0.24',
                'package' => 'id.kemangtravel.com'
            ],
        ]);
    }
}
