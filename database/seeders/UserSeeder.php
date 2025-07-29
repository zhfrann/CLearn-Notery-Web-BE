<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private $users = [
        "user1" => [
            "username" => "user1",
            "email" => "user1@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/user1.png",
            "qr_code" => "qr_code/user1.png",
            "role" => "student",
        ],
        "user2" => [
            "username" => "user2",
            "email" => "user2@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/user2.png",
            "qr_code" => "qr_code/user2.png",
            "role" => "student",
        ],
        "evan_john" => [
            "username" => "evan_john",
            "email" => "evanjohn@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/evan_john.png",
            "qr_code" => "qr_code/evan_john.png",
            "role" => "student",
        ],
        "e_khansa" => [
            "username" => "e_khansa",
            "email" => "khansa@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/e_khansa.png",
            "qr_code" => "qr_code/e_khansa.png",
            "role" => "student",
        ],
        "admin1" => [
            "username" => "admin1",
            "email" => "admin1@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/admin1.png",
            "qr_code" => "qr_code/admin1.png",
            "role" => "admin",
        ],
        "admin2" => [
            "username" => "admin2",
            "email" => "admin2@gmail.com",
            "password" => "rahasia123",
            "foto_profil" => "foto_profil/admin2.png",
            "qr_code" => "qr_code/admin2.png",
            "role" => "admin",
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->users as $user) {
            User::create([
                "username" => $user["username"],
                "email" => $user["email"],
                "password" => Hash::make($user["password"]),
                "role" => $user['role'],
                "status_akun" => "aktif",
                "foto_profil" => $user["foto_profil"],
                "qr_code" => $user["qr_code"]
            ]);
        }
    }
}
