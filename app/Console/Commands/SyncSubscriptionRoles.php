<?php

namespace App\Console\Commands;

use App\Services\SubscriptionRoleService;
use Illuminate\Console\Command;

class SyncSubscriptionRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизировать роли пользователей на основе их активных подписок';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionRoleService $roleService): int
    {
        $this->info('Начинаю синхронизацию ролей подписчиков...');

        $result = $roleService->syncAllUsersRoles();

        $this->info("Обработано пользователей: {$result['processed']}");
        $this->info("Назначено активных ролей: {$result['roles_assigned']}");
        $this->info("Назначено expired/lapsed ролей: {$result['expired_assigned']}");

        return Command::SUCCESS;
    }
}
