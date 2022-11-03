<?php 
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Generators\Way\Generators\Compilers;

class TemplateCompiler implements Compiler {

    /**
     * Compile the template using
     * the given data
     *
     * @param $template
     * @param $data
     * @return mixed
     */
    public function compile($template, $data)
    {
        foreach($data as $key => $value)
        {
            $template = preg_replace("/\\$$key\\$/i", $value, $template);
        }

        return $template;
    }

}