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
            ['name' => 'Айдос Нурланов'],
            [
                'role' => 'manager',
                'contact' => '+7 (701) 123-45-67',
                'status' => 'online',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Нурлан Омаров'],
            [
                'role' => 'manager',
                'contact' => '+7 (702) 234-56-78',
                'status' => 'offline',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Ерлан Тлеубергенов'],
            [
                'role' => 'mechanic',
                'contact' => '+7 (707) 345-67-89',
                'status' => 'vacation',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Гаухар Сейтова'],
            [
                'role' => 'admin',
                'contact' => '+7 (777) 456-78-90',
                'status' => 'offline',
            ]
        );

        StaffMember::updateOrCreate(
            ['name' => 'Мадиар Сулейменов'],
            [
                'role' => 'mechanic',
                'contact' => '+7 (708) 567-89-01',
                'status' => 'online',
            ]
        );

        // Create test leads
        Lead::create([
            'name' => 'Аян Тураров',
            'contacts' => '+7 (701) 123-45-67',
            'comment' => 'Интересуется обслуживанием техники для офиса',
            'source' => 'Телефон',
        ]);

        Lead::create([
            'name' => 'Мадина Серикова',
            'contacts' => 'madina@example.com',
            'comment' => 'Нужна консультация по технике',
            'source' => 'Email',
        ]);

        Lead::create([
            'name' => 'Алихан Төлегенов',
            'contacts' => '+7 (701) 123-45-67',
            'comment' => 'Интересуется обслуживанием техники для офиса',
            'source' => 'Телефон',
        ]);

        Lead::create([
            'name' => 'Айдана Нурсеитова',
            'contacts' => 'aidana@example.com',
            'comment' => 'Нужна консультация по технике',
            'source' => 'Email',
        ]);

        Lead::create([
            'name' => 'Данияр Ашимов',
            'contacts' => '+7 (707) 234-56-78',
            'comment' => 'Необходимо сервисное обслуживание техники',
            'source' => 'Сайт',
        ]);

        Lead::create([
            'name' => 'Аружан Касымова',
            'contacts' => '+7 (777) 345-67-89',
            'comment' => 'Покупка оборудования для офиса',
            'source' => 'Рекомендация',
        ]);
