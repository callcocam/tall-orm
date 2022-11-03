<?php 
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Tall\Orm\Core\Generators\Syntax;

/**
 * Class AddToTable
 * @package Tall\Orm\Core\Generators\Syntax
 */
class AddToTable extends Table {

	/**
	 * Return string for adding a column
	 *
	 * @param array $field
	 * @return string
	 */
	protected function getItem(array $field)
	{
		$property = data_get($field,'field');

		if($property){
			// If the field is an array,
			// make it an array in the Migration
			if (is_array($property)) {
				$property = "['". implode("','", $property) ."']";
			} else {
				$property = $property ? "'$property'" : null;
			}

			$type = data_get($field,'type');

			$output = sprintf(
				"\$table->%s(%s)",
				$type,
				$property
			);

			// If we have args, then it needs
			// to be formatted a bit differently
			if (isset($field['args'])) {
				$output = sprintf(
					"\$table->%s(%s, %s)",
					$type,
					$property,
					$field['args']
				);
			}
			if (isset($field['decorators'])) {
				$output .= $this->addDecorators( $field['decorators'] );
			}
			return $output . ';';
		}
		
	}
}
