<?php 
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Generators\Way\Generators\Compilers;

interface Compiler {

/**
 * Compile the template using
 * the given data
 *
 * @param $template
 * @param $data
 */
public function compile($template, $data);
} 