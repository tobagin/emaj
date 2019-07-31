<?php

namespace Emaj\Providers;

use Emaj\Repositories\Cadastro\AlunoRepository;
use Emaj\Repositories\Cadastro\AlunoRepositoryEloquent;
use Emaj\Repositories\Cadastro\AvaliacaoArquivoRepository;
use Emaj\Repositories\Cadastro\AvaliacaoArquivoRepositoryEloquent;
use Emaj\Repositories\Cadastro\AvaliacaoRepository;
use Emaj\Repositories\Cadastro\AvaliacaoRepositoryEloquent;
use Emaj\Repositories\Cadastro\ClienteRepository;
use Emaj\Repositories\Cadastro\ClienteRepositoryEloquent;
use Emaj\Repositories\Cadastro\ComposicaoFamiliarRepository;
use Emaj\Repositories\Cadastro\ComposicaoFamiliarRepositoryEloquent;
use Emaj\Repositories\Cadastro\EnderecoRepository;
use Emaj\Repositories\Cadastro\EnderecoRepositoryEloquent;
use Emaj\Repositories\Cadastro\NacionalidadeRepository;
use Emaj\Repositories\Cadastro\NacionalidadeRepositoryEloquent;
use Emaj\Repositories\Cadastro\ParametroTriagemRepository;
use Emaj\Repositories\Cadastro\ParametroTriagemRepositoryEloquent;
use Emaj\Repositories\Cadastro\TelefoneRepository;
use Emaj\Repositories\Cadastro\TelefoneRepositoryEloquent;
use Emaj\Repositories\Cadastro\TipoDemandaRepository;
use Emaj\Repositories\Cadastro\TipoDemandaRepositoryEloquent;
use Emaj\Repositories\Cadastro\UsuarioRepository;
use Emaj\Repositories\Cadastro\UsuarioRepositoryEloquent;
use Emaj\Repositories\Movimento\FichaTriagemRepository;
use Emaj\Repositories\Movimento\FichaTriagemRepositoryEloquent;
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
        $this->app->bind(UsuarioRepository::class, UsuarioRepositoryEloquent::class);
        $this->app->bind(ParametroTriagemRepository::class, ParametroTriagemRepositoryEloquent::class);
        $this->app->bind(ClienteRepository::class, ClienteRepositoryEloquent::class);
        $this->app->bind(EnderecoRepository::class, EnderecoRepositoryEloquent::class);
        $this->app->bind(ComposicaoFamiliarRepository::class, ComposicaoFamiliarRepositoryEloquent::class);
        $this->app->bind(TelefoneRepository::class, TelefoneRepositoryEloquent::class);
        $this->app->bind(NacionalidadeRepository::class, NacionalidadeRepositoryEloquent::class);
        $this->app->bind(FichaTriagemRepository::class, FichaTriagemRepositoryEloquent::class);
        $this->app->bind(AlunoRepository::class, AlunoRepositoryEloquent::class);
        $this->app->bind(AvaliacaoRepository::class, AvaliacaoRepositoryEloquent::class);
        $this->app->bind(AvaliacaoArquivoRepository::class, AvaliacaoArquivoRepositoryEloquent::class);
    }

}
