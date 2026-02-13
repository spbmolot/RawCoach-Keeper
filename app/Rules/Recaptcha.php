<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    protected string $action;

    public function __construct(string $action = '')
    {
        $this->action = $action;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Если reCAPTCHA отключена — пропускаем
        if (!config('recaptcha.enabled')) {
            return;
        }

        if (empty($value)) {
            $fail('Проверка reCAPTCHA не пройдена. Попробуйте ещё раз.');
            return;
        }

        try {
            $response = Http::asForm()->post(config('recaptcha.verify_url'), [
                'secret' => config('recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $body = $response->json();

            if (!($body['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'error-codes' => $body['error-codes'] ?? [],
                    'ip' => request()->ip(),
                ]);
                $fail('Проверка reCAPTCHA не пройдена. Попробуйте ещё раз.');
                return;
            }

            // Проверяем action (если указан)
            if ($this->action && ($body['action'] ?? '') !== $this->action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $this->action,
                    'actual' => $body['action'] ?? 'none',
                    'ip' => request()->ip(),
                ]);
                $fail('Проверка reCAPTCHA не пройдена. Попробуйте ещё раз.');
                return;
            }

            // Проверяем score
            $score = $body['score'] ?? 0;
            $minScore = config('recaptcha.min_score', 0.5);

            if ($score < $minScore) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $score,
                    'min_score' => $minScore,
                    'ip' => request()->ip(),
                ]);
                $fail('Проверка reCAPTCHA не пройдена. Попробуйте ещё раз.');
            }

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'message' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            // При ошибке сервиса — пропускаем, чтобы не блокировать пользователей
        }
    }
}
