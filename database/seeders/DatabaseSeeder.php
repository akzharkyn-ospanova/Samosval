<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffMember;
use App\Models\Lead;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Суперадмин',
                'email' => 'admin@test.com',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Менеджер по продажам',
                'email' => 'manager@test.com',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SALES_MANAGER,
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Иван Петров'],
            [
                'role' => 'manager',
                'contact' => '+7 (999) 123-45-67',
                'status' => 'online',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Сергей Кузнецов'],
            [
                'role' => 'mechanic',
                'contact' => '+7 (999) 234-56-78',
                'status' => 'offline',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Мария Соколова'],
            [
                'role' => 'admin',
                'contact' => '+7 (999) 345-67-89',
                'status' => 'vacation',
            ]
        );

        // Create test leads
        Lead::create([
            'name' => 'Иван Петров',
            'contacts' => '+7 (999) 123-45-67',
            'comment' => 'Интересуется принтерами для офиса',
            'source' => 'Телефон',
        ]);

        Lead::create([
            'name' => 'Анна Сидорова',
            'contacts' => 'anna@example.com',
            'comment' => 'Нужна консультация по МФУ',
            'source' => 'Email',
        ]);

        Lead::create([
            'name' => 'Сергей Иванов',
            'contacts' => '+7 (999) 234-56-78',
            'comment' => 'Необходимо сервис ксерокса',
            'source' => 'Сайт',
        ]);

        Lead::create([
            'name' => 'Мария Васильева',
            'contacts' => '+7 (999) 345-67-89',
            'comment' => 'Покупка сканера',
            'source' => 'Рекомендация',
        ]);
    }
}
