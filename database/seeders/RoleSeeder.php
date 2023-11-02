<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            "name" =>[
                "uz"=> "Haydovchi",
                "ru"=> "Водитель",
                "en"=> "Driver",
            ]
            ]);
            Role::create([
                "name" =>[
                    "uz"=> "Mijoz",
                    "ru"=> "Клиент",
                    "en"=> "Customer",
                ]
                ]);
                Role::create([
                    "name" =>[
                        "uz"=> "Operator",
                        "ru"=> "Оператор",
                        "en"=> "Operator",
                    ]
                    ]);
    }
}
