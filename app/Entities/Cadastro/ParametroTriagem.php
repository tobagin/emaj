<?php

namespace Emaj\Entities\Cadastro;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe da entidade dos parâmetros da triagem
 *
 * PHP version 7.2
 *
 * @category   Entity
 * @package    Cadastro
 * @author     Gabriel Schenato <gabriel@uniplaclages.edu.br>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link       https://www.uniplaclages.edu.br/
 * @since      1.0.0
 */
class ParametroTriagem extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'parametros_triagem';
    
    protected $fillable = [
        'renda'
    ];

}
