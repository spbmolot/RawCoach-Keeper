<?php

namespace App\Console\Commands;

use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:process-renewals 
                            {--dry-run : Показать подписки для продления без создания платежей}
                            {--days=3 : Количество дней до истечения для обработки}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обработка автопродления подписок, истекающих в ближайшие дни';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService): int
    {
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');

        $this->info("Обработка автопродления подписок (истекают в течение {$days} дней)");
        
        if ($dryRun) {
            $this->warn('РЕЖИМ ТЕСТИРОВАНИЯ - платежи не будут созданы');
        }

        try {
            $result = $paymentService->processAutoRenewals($days, $dryRun);

            $this->info("Результаты обработки:");
            $this->table(
                ['Метрика', 'Значение'],
                [
                    ['Найдено подписок для продления', $result['found']],
                    ['Успешно обработано', $result['processed']],
                    ['Ошибки', $result['errors']],
                    ['Пропущено', $result['skipped']],
                ]
            );

            if (!empty($result['details'])) {
                $this->info("\nДетали обработки:");
                foreach ($result['details'] as $detail) {
                    $status = $detail['success'] ? '✅' : '❌';
                    $this->line("{$status} Подписка #{$detail['subscription_id']}: {$detail['message']}");
                }
            }

            if ($result['errors'] > 0) {
                $this->warn("Обнаружены ошибки при обработке. Проверьте логи для подробностей.");
                return self::FAILURE;
            }

            $this->info("Обработка завершена успешно!");
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Ошибка при обработке автопродлений: " . $e->getMessage());
            Log::error('Subscription renewal command failed', [
                'exception' => $e,
                'options' => $this->options(),
            ]);
            return self::FAILURE;
        }
    }
}
