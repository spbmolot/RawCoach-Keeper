<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\PersonalPlan;
use App\Models\Coupon;
use App\Models\AdCampaign;
use App\Policies\UserPolicy;
use App\Policies\MenuPolicy;
use App\Policies\RecipePolicy;
use App\Policies\UserSubscriptionPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PersonalPlanPolicy;
use App\Policies\CouponPolicy;
use App\Policies\AdCampaignPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Menu::class => MenuPolicy::class,
        Recipe::class => RecipePolicy::class,
        UserSubscription::class => UserSubscriptionPolicy::class,
        Payment::class => PaymentPolicy::class,
        PersonalPlan::class => PersonalPlanPolicy::class,
        Coupon::class => CouponPolicy::class,
        AdCampaign::class => AdCampaignPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Дополнительные Gates для специфичных проверок
        Gate::define('access-admin-panel', function (User $user) {
            return $user->hasRole(['admin', 'editor']);
        });

        Gate::define('manage-content', function (User $user) {
            return $user->hasAnyPermission([
                'content.create',
                'content.edit',
                'menus.create',
                'menus.edit',
                'recipes.create',
                'recipes.edit'
            ]);
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->hasAnyPermission([
                'admin.reports',
                'admin.dashboard'
            ]);
        });

        Gate::define('manage-subscriptions', function (User $user) {
            return $user->hasAnyPermission([
                'subscriptions.view',
                'subscriptions.manage',
                'payments.view'
            ]);
        });
    }
}
