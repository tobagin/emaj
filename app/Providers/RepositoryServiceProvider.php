<?php

namespace Emaj\Providers;

use Emaj\Repositories\Cadastro\TipoDemandaRepository;
use Emaj\Repositories\Cadastro\TipoDemandaRepositoryEloquent;
use Emaj\Repositories\Cadastro\UserRepository;
use Emaj\Repositories\Cadastro\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(TipoDemandaRepository::class, TipoDemandaRepositoryEloquent::class);
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
    }
}
