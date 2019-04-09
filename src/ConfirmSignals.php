<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm;

use Nette\SmartObject;

/**
 * @method onConfirmCreate(Confirm $confirm)
 */

class ConfirmSignals
{

	use SmartObject;

	/**
	 * @var callable[]
	 */
	public $onConfirmCreate = [];

	/**
	 * @param Confirm $confirm
	 */
	public function createConfirm(Confirm $confirm)
	{
		$this->onConfirmCreate($confirm);
	}

}
