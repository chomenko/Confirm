<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm;

use Nette\Application\Request;
use Nette\SmartObject;

/**
 * @method onConfirmCreate(Confirm $confirm)
 * @method onBeforeForward(Confirm $confirm, Request $request)
 */

class ConfirmSignals
{

	use SmartObject;

	/**
	 * @var callable[]
	 */
	public $onConfirmCreate = [];

	/**
	 * @var callable[]
	 */
	public $onBeforeForward = [];

	/**
	 * @param Confirm $confirm
	 */
	public function createConfirm(Confirm $confirm)
	{
		$this->onConfirmCreate($confirm);
	}

	/**
	 * @param Confirm $confirm
	 * @param Request $request
	 */
	public function beforeForward(Confirm $confirm, Request $request)
	{
		$this->onBeforeForward($confirm, $request);
	}

}
