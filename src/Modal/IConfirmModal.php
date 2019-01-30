<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm\Modal;

interface IConfirmModal
{

	/**
	 * @return ConfirmModal
	 */
	public function create(): ConfirmModal;

}
