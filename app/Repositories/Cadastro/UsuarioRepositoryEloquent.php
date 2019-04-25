<?php

namespace Emaj\Repositories\Cadastro;

use Emaj\Entities\Cadastro\Usuario;
use Illuminate\Validation\Rule;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use function app;

/**
 * Repository responsável por gerenciar a entidade User
 *
 * PHP version 7.2
 *
 * @category   Repository
 * @package    Cadastro
 * @author     Gabriel Schenato <gabriel@uniplaclages.edu.br>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link       https://www.uniplaclages.edu.br/
 * @since      1.0.0
 */
class UsuarioRepositoryEloquent extends BaseRepository implements UsuarioRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Usuario::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public static function getRules($data)
    {
        $id = isset($data['id']) ? $data['id'] : null;
        return [
            'nome_completo' => 'required|min:5',
            'email' => ['required', 'email', Rule::unique('usuarios')->ignore($id)],
            'password' => 'required|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'secretaria', 'aluno'])],
            'avatar' => 'nullable',
            'telefone' => 'required|min:8',
        ];
    }

}