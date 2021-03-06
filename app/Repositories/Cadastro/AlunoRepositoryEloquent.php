<?php

namespace Emaj\Repositories\Cadastro;

use Emaj\Entities\Cadastro\Aluno;
use Emaj\Exceptions\ValidationException;
use Emaj\Repositories\AbstractRepository;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Repository responsável por gerenciar a entidade Aluno
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
class AlunoRepositoryEloquent extends AbstractRepository implements AlunoRepository
{

    /**
     * @var AvatarRepository
     */
    private $avatarRepository;

    /**
     * @var DiaPeriodoAlunoRepository
     */
    private $diaPeriodoAlunoRepository;

    /**
     * @var ProtocoloAlunoProfessorRepository
     */
    private $protocoloAlunoProfessorRepository;

    public function __construct(Container $app, ProtocoloAlunoProfessorRepository $protocoloAlunoProfessorRepository, DiaPeriodoAlunoRepository $diaPeriodoAlunoRepository, AvatarRepository $avatarRepository)
    {
        parent::__construct($app);
        $this->protocoloAlunoProfessorRepository = $protocoloAlunoProfessorRepository;
        $this->diaPeriodoAlunoRepository = $diaPeriodoAlunoRepository;
        $this->avatarRepository = $avatarRepository;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Aluno::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @override
     * Save a new entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $this->avatarRepository->saveOrUpdateAvatar($attributes);
            $aluno = parent::create($attributes);
            
            DB::commit();
            return $aluno;
        } catch (ValidationException $ex) {
            DB::rollback();
            throw $ex;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    
    /**
     * @override
     * Update a entity in repository by id
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();
            
            $ativo = isset($attributes['ativo']) ? $attributes['ativo'] : null;
            if (!$ativo) {
                $this->inativaProtocoloAlunosProfessores($attributes, $id);
                $this->deleteDiaPeriodosAluno($id);
            }
            
            $this->avatarRepository->saveOrUpdateAvatar($attributes);
            $aluno = parent::update($attributes, $id);
            
            DB::commit();
            return $aluno;
        } catch (ValidationException $ex) {
            DB::rollback();
            throw $ex;
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    
    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $aluno = $this->find($id);
            $deletado = parent::delete($id);
            if ($aluno->avatar) {
                $this->avatarRepository->delete($aluno->avatar->id);
            }           

            DB::commit();
            return $deletado;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Método responsável por retornar as regras a serem aplicadas ao criar ou editar
     * um registro
     * 
     * @param array $data
     * @param int $id
     * 
     * @return array Regras para serem aplicadas
     */
    public function getRules(array $data, int $id = null)
    {
        return [
            'nome_completo' => 'required|min:5',
            'disciplina_id' => 'required|numeric',
            'email' => ['nullable', 'email', Rule::unique('alunos')->ignore($id)]
        ];
    }

    /**
     * Método responsável por realizar a busca pelo valor e campo passado
     * @param array $values
     * @return mixed
     */
    public function getBySearch(array $values)
    {
        $criteria = $this->model->newQuery();
        if (isset($values['id'])) {
            $criteria->where('id', '=', (int) $values['id']);
        }
        if (isset($values['nome_completo'])) {
            $criteria->where('nome_completo', 'like', "%{$values['nome_completo']}%");
        }
        if (isset($values['email'])) {
            $criteria->where('email', 'like', "%{$values['email']}%");
        }
        if (isset($values['celular'])) {
            $criteria->where('celular', 'like', "%{$values['celular']}%");
        }
        if (isset($values['matricula'])) {
            $criteria->where('matricula', 'like', "%{$values['matricula']}%");
        }
        if (isset($values['ativo'])) {
            $criteria->where('ativo', '=', (boolean) $values['ativo']);
        }

        return $criteria;
    }

    /**
     * Método responsável por buscar os dados e retornar para o autocomplete
     * 
     * @param string $value
     */
    public function getDataAutocomplete($value)
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->where(function ($query) use ($value) {
                    $query->where('nome_completo', 'LIKE', "%{$value}%")
                    ->orWhere('id', '=', (int) $value);
                })
                ->orderBy('nome_completo', 'asc')
                ->limit(10)
                ->get(['id', 'nome_completo']);

        $this->resetModel();

        return $model;
    }

    /**
     * Método responsável por inativar os protocolos alunos professores quando o protocolo
     * está sendo inativado.
     *
     * @param int $id
     *
     * @return void
     */
    private function inativaProtocoloAlunosProfessores($id)
    {
        $protocoloAlunosProfessores = $this->protocoloAlunoProfessorRepository->findByField('aluno_id', (int) $id);
        foreach ($protocoloAlunosProfessores as $protocoloAlunoProfessor) {
            $protocoloAlunoProfessor->ativo = false;
            $this->protocoloAlunoProfessorRepository->update($protocoloAlunoProfessor->toArray(), $protocoloAlunoProfessor->id);
        }
    }

    /**
     * Método responsável por deletar o vínculo dos períodos com o aluno quando o mesmo está
     * sendo inativado.
     * 
     * @param int $id
     */
    private function deleteDiaPeriodosAluno($id)
    {
        $diaPeriodosAluno = $this->diaPeriodoAlunoRepository->findByField('aluno_id', $id);
        foreach ($diaPeriodosAluno as $diaPeriodoAluno) {
            $diaPeriodoAluno->delete();
        }
    }

}
