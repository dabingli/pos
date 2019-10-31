<?php
/**
 * @author Nikolai Kordulla
 */
namespace common\library\uni_push\uni_push\protobuf\type;

use common\library\uni_push\uni_push\protobuf\PBMessage;

class PBScalar extends PBMessage
{
	/**
	 * Set scalar value
	 */
	public function set_value($value)
	{	
		$this->value = $value;	
	}

	/**
	 * Get the scalar value
	 */
	public function get_value()
	{
		return $this->value;
	}
}

