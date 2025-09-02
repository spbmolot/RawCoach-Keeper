<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Создание разрешений
        $permissions = [
            // Управление пользователями
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.impersonate',
            
            // Управление контентом
            'content.view',
            'content.create',
            'content.edit',
            'content.delete',
            'content.publish',
            
            // Управление меню и рецептами
            'menus.view',
            'menus.create',
            'menus.edit',
            'menus.delete',
            'menus.publish',
            
            'recipes.view',
            'recipes.create',
            'recipes.edit',
            'recipes.delete',
            'recipes.publish',
            
            // Управление подписками и платежами
            'subscriptions.view',
            'subscriptions.create',
            'subscriptions.edit',
            'subscriptions.delete',
            'subscriptions.manage',
            
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.refund',
            
            // Управление купонами
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            
            // Управление персональными планами
            'personal-plans.view',
            'personal-plans.create',
            'personal-plans.edit',
            'personal-plans.delete',
            'personal-plans.approve',
            
            // Управление анкетами
            'questionnaires.view',
            'questionnaires.create',
            'questionnaires.edit',
            'questionnaires.delete',
            'questionnaires.review',
            
            // Управление рекламой
            'ads.view',
            'ads.create',
            'ads.edit',
            'ads.delete',
            'ads.moderate',
            'ads.reports',
            
            // Доступ к контенту
            'content.access.current',
            'content.access.archive',
            'content.access.early',
            'content.access.personal',
            
            // Административные функции
            'admin.dashboard',
            'admin.settings',
            'admin.reports',
            'admin.logs',
            'admin.backups',
            
            // Файлы и экспорт
            'files.download',
            'files.export.pdf',
            'files.export.excel',
            
            // Списки покупок
            'shopping-lists.create',
            'shopping-lists.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Создание ролей
        $this->createRoles();
    }

    private function createRoles(): void
    {
        // Гость (неавторизованный пользователь) - без разрешений
        
        // Пользователь (зарегистрированный, но без подписки)
        $userRole = Role::firstOrCreate(['name' => 'user']);
        // Базовые права для зарегистрированных пользователей
        
        // Подписчик Базовый/Стандарт
        $subscriberStandardRole = Role::firstOrCreate(['name' => 'subscriber_standard']);
        $subscriberStandardRole->givePermissionTo([
            'content.access.current',
            'files.download',
            'files.export.pdf',
            'files.export.excel',
            'shopping-lists.create',
            'shopping-lists.export',
        ]);

        // Подписчик Годовой (Premium)
        $subscriberPremiumRole = Role::firstOrCreate(['name' => 'subscriber_premium']);
        $subscriberPremiumRole->givePermissionTo([
            'content.access.current',
            'content.access.archive',
            'content.access.early',
            'files.download',
            'files.export.pdf',
            'files.export.excel',
            'shopping-lists.create',
            'shopping-lists.export',
        ]);

        // Подписчик Индивидуальный
        $subscriberPersonalRole = Role::firstOrCreate(['name' => 'subscriber_personal']);
        $subscriberPersonalRole->givePermissionTo([
            'content.access.current',
            'content.access.archive',
            'content.access.early',
            'content.access.personal',
            'files.download',
            'files.export.pdf',
            'files.export.excel',
            'shopping-lists.create',
            'shopping-lists.export',
            'questionnaires.create',
            'personal-plans.view',
        ]);

        // Рекламодатель
        $advertiserRole = Role::firstOrCreate(['name' => 'advertiser']);
        $advertiserRole->givePermissionTo([
            'ads.view',
            'ads.create',
            'ads.edit',
            'ads.reports',
        ]);

        // Редактор/Нутрициолог
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'content.view',
            'content.create',
            'content.edit',
            'content.publish',
            'menus.view',
            'menus.create',
            'menus.edit',
            'menus.publish',
            'recipes.view',
            'recipes.create',
            'recipes.edit',
            'recipes.publish',
            'personal-plans.view',
            'personal-plans.create',
            'personal-plans.edit',
            'personal-plans.approve',
            'questionnaires.view',
            'questionnaires.review',
            'files.export.pdf',
            'files.export.excel',
        ]);

        // Администратор
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
