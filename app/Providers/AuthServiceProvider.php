<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gate para super_admin
        Gate::define('super-admin', function (User $user) {
            return $user->hasRole('super_admin');
        });

        // Gate para administrador
        Gate::define('administrador', function (User $user) {
            return $user->hasRole('administrador');
        });

        // Gate para operador
        Gate::define('operador', function (User $user) {
            return $user->hasRole('operador');
        });

        // Gate para gestor
        Gate::define('gestor', function (User $user) {
            return $user->hasRole('gestor');
        });
    }
}
