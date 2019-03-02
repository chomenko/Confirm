<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm\Modal;

use Chomenko\Confirm\Confirm;
use Chomenko\Confirm\DI\ConfirmExtension;
use Chomenko\ExtraForm\ExtraForm;
use Chomenko\Modal\AccessAction;
use Chomenko\Modal\ModalControl;
use Chomenko\Modal\ModalHtml;
use Chomenko\Modal\WrappedHtml;
use Chomenko\Modal\WrappedModal;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\Component;
use Nette\Utils\Html;

class ConfirmModal extends ModalControl
{

	const PARAMETER_KEY = "@confirmModal";

	/**
	 * @var Confirm
	 */
	private $confirm;

	/**
	 * @param Presenter $presenter
	 */
	public function attached($presenter)
	{
		parent::attached($presenter);
		$request = $presenter->getRequest();
		if (!$request) {
			$presenter->onStartup[] = function (Presenter $presenter) {
				$this->installConfirm($presenter);
			};
			return;
		}

		$this->installConfirm($presenter);
	}

	/**
	 * @param Presenter $presenter
	 */
	public function installConfirm(Presenter $presenter)
	{
		$request = $presenter->getRequest();
		$this->confirm = $request->getParameter(ConfirmExtension::CONFIRM_PARAMETER_KEY);
	}

	/**
	 * @param AccessAction $accessAction
	 * @return bool
	 */
	public function access(AccessAction $accessAction): bool
	{
		$accessAction->setAllowed(TRUE);
		return TRUE;
	}

	/**
	 * @param string $message
	 * @param null|string|array $params
	 * @param bool $html
	 * @return string|Html
	 */
	private function translate(string $message, $params = NULL, $html = FALSE)
	{
		$translator = $this->confirm->getTranslator();
		if ($translator && $this->confirm->translate) {
			return $translator->translate($message, $params, $this->confirm->translateFile, $html);
		}
		return $message;
	}

	/**
	 * @return string
	 */
	public function getTitle() :string
	{
		$label = $this->confirm->label;
		if ($label) {
			return $this->translate($label, NULL, FALSE);
		}
		return "";
	}

	/**
	 * @return ExtraForm
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function createComponentConfirmForm()
	{
		$form = new ExtraForm();

		$form->setTranslator($this->confirm->getTranslator());
		$form->setTranslateFile($this->confirm->translateFile);
		$form->addSubmit("yes", $this->confirm->yes)
			->setAttribute('class', 'btn btn-default' . ($this->confirm->ajax ? ' ajax' : ''))
			->onClick[] = [$this, "processAllow"];
		$form->addSubmit("not", $this->confirm->not)
			->setAttribute('class', 'btn btn-default'. ($this->confirm->ajax ? ' ajax' : ''))
			->onClick[] = [$this, "processDenied"];

		$request = $this->confirm->getRequest();
		$destination = $request->getPresenterName();
		$destination = count(explode(":", $destination)) > 1 ? ":" . $destination : $destination;
		$destination .= ":" . $request->getParameter("action");
		$action = $this->presenter->link($destination, $request->getParameters());

		$form->setAction($action);

		$builder = $form->builder();
		$row = $builder->addRow();
		$row->addColMd(6, "yes")->addClass("text-right");
		$row->addColMd(6, "not")->addClass("text-left");

		return $form;
	}

	/**
	 * @throws \Chomenko\Modal\Exceptions\ModalException
	 * @throws \Nette\Application\AbortException
	 */
	public function processAllow()
	{
		$request = clone $this->confirm->getOriginalRequest();
		$request->setMethod(Request::FORWARD);
		$request->setPost([]);
		$params = $request->getParameters();
		$params[self::PARAMETER_KEY] = $this;
		$request->setParameters($params);

		$parent = $this->getParent();
		if ($parent instanceof WrappedModal) {
			$parent->getModalFactory()->getDriver()->closeModal();
		}

		$this->presenter->forward($request);
	}

	/**
	 * @throws \Chomenko\Modal\Exceptions\ModalException
	 * @throws \Nette\Application\AbortException
	 */
	public function processDenied()
	{
		$destination = "this";
		if (($denied = $this->confirm->deniedDestination) !== NULL) {
			$destination = $denied;
		}
		if (!$this->presenter->isAjax()) {
			$this->presenter->redirect($destination);
		}

		$parent = $this->getParent();
		if ($parent instanceof WrappedModal) {
			$parent->getModalFactory()->getDriver()->closeModal();
		}
	}

	/**
	 * @param Component $component
	 * @param string $name
	 * @return string
	 */
	private function getComponentName($component, $name = "")
	{
		if ($component instanceof Presenter || !$component) {
			return $name;
		}
		$name = $component->getName() . ((!empty($name) ? "-" : "") . $name);
		return $this->getComponentName($component->getParent(), $name);
	}


	/**
	 * @param WrappedHtml $wrappedHtml
	 * @param ModalHtml $body
	 * @return mixed|void
	 */
	public function renderBody(WrappedHtml $wrappedHtml, ModalHtml $body)
	{
		$dialog = $wrappedHtml->getDialog();
		$class = $dialog->getAttribute("class");
		$class .= " confirm-modal " . $this->confirm->type;
		$dialog->setAttribute("class", $class);

		$quest = Html::el("h4", [
			"class" => "confirm-question text-center",
		])->setHtml($this->translate($this->confirm->question));

		$body->addHtml($quest);

		$form = $this->getComponent('confirmForm');
		$content = $this->renderComponent($form);
		$body->addHtml($content);
		$body->render();
	}

	public function renderFooter(WrappedHtml $wrappedHtml, ModalHtml $footer)
	{
	}

}
