<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm\Modals;

interface IConfirmModal
{

	/**
	 * @return ConfirmModal
	 */
	public function create(): ConfirmModal;

}
