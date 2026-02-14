<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(
        private ReferralService $referralService
    ) {}

    /**
     * Реферальная страница в ЛК
     */
    public function index()
    {
        $user = auth()->user();
        $stats = $this->referralService->getStats($user);

        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referred:id,name,email,created_at')
            ->latest()
            ->limit(50)
            ->get();

        $rewards = ReferralReward::where('user_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('dashboard.referrals', compact('stats', 'referrals', 'rewards'));
    }

    /**
     * Публичная landing-страница по реферальной ссылке
     */
    public function landing(string $code)
    {
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return redirect()->route('home');
        }

        // Сохраняем код в cookie на 30 дней
        $cookie = cookie('referral_code', $code, 60 * 24 * 30);

        // Если уже авторизован — просто редирект
        if (auth()->check()) {
            return redirect()->route('dashboard')->withCookie($cookie);
        }

        return response()
            ->view('referral.landing', [
                'referrer' => $referrer,
                'code' => $code,
                'discount' => ReferralService::REFEREE_DISCOUNT,
            ])
            ->withCookie($cookie);
    }
}
