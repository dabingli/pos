<?php
/**
 * @author Nikolai Kordulla
 */
namespace common\library\uni_push\uni_push\protobuf\type;

use common\library\uni_push\uni_push\protobuf\PBMessage;

class PBEnum extends PBScalar
{
	public $wired_type = PBMessage::WIRED_VARINT;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{
		$this->value = $this->reader->next();
	}

	/**
	 * Serializes type
	 */
	public function SerializeToString($rec=-1)
	{
		$string = '';

		if ($rec > -1)
		{
			$string .= $this->base128->set_value($rec << 3 | $this->wired_type);
		}

		$value = $this->base128->set_value($this->value);
		$string .= $value;

		return $string;
	}
}

