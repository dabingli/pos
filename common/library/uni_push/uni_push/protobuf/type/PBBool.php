<?php
/**
 * @author Nikolai Kordulla
 */

namespace common\library\uni_push\uni_push\protobuf\type;

use common\library\uni_push\uni_push\protobuf\PBMessage;

class PBBool extends PBInt
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
		$this->value = ($this->value != 0) ? 1 : 0;
	}

}

