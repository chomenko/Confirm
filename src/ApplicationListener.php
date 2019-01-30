<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm;

use Chomenko\Confirm\DI\ConfirmExtension;
use Chomenko\Confirm\Modals\ConfirmModal;
use Chomenko\Confirm\Modals\IConfirmModal;
use Chomenko\Modal\ModalController;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Kdyby\Events\Subscriber;
use Nette\Application\Application;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

class ApplicationListener implements Subscriber
{

	/**
	 * @var ModalController
	 */
	protected $modalController;

	/**
	 * @var AnnotationReader
	 */
	protected $annotReader;

	/**
	 * @var ITranslator|NULL
	 */
	protected $translator;

	/**
	 * @param ModalController $controller
	 * @param ITranslator|NULL $translator
	 * @throws AnnotationException
	 */
	public function __construct(ModalController $controller, ITranslator $translator = NULL)
	{
		$this->modalController = $controller;
		$this->annotReader = new AnnotationReader();
		$this->translator = $translator;
	}

	/**
	 * @return array|string[]
	 */
	public function getSubscribedEvents()
	{
		return [
			Application::class . '::onPresenter' => "request",
		];
	}

	/**
	 * @param Application $application
	 * @param Presenter $presenter
	 * @throws \ReflectionException
	 */
	public function request(Application $application, Presenter $presenter)
	{
		$requests = $application->getRequests();

		$request = end($requests);

		$do = $request->getParameter("do");
		$modal = $request->getParameter(ConfirmModal::PARAMETER_KEY);

		if ($do && !$modal instanceof ConfirmModal) {
			$confirm = $this->getConfirm($do, $presenter);

			if (!$confirm) {
				return;
			}

			$modal = $this->modalController->getByInterface(IConfirmModal::class);
			$modalUrl = $modal->getUrl();
			$confirm->setOriginalRequest(reset($requests));
			$confirm->setRequest($request);
			$confirm->setPresenter($presenter);

			$parameters = $request->getParameters();
			$parameters[ConfirmExtension::CONFIRM_PARAMETER_KEY] = $confirm;
			$parameters = array_replace_recursive($parameters, $modalUrl->getQueryParameters());

			$nextRequest = clone $request;
			$nextRequest->setMethod(Request::FORWARD);
			$nextRequest->setParameters($parameters);

			if ($request->isMethod("post")) {
				$nextRequest->setFlag("post", TRUE);
			}

			$presenter->onStartup[] = function () use ($nextRequest, $presenter) {
				$presenter->forward($nextRequest);
			};
		}
	}

	/**
	 * @param string $do
	 * @param Presenter $presenter
	 * @return Confirm|null
	 * @throws \ReflectionException
	 */
	private function getConfirm(string $do, Presenter $presenter): ?Confirm
	{
		$control = $presenter;
		$exp = explode("-", $do);
		foreach ($exp as $i => $name) {
			$child = $control->getComponent($name, FALSE);

			if (!$child && count($exp) - 1 === $i) {
				$method = "handle" . $name;
				if (method_exists($control, $method)) {
					$reflection = new \ReflectionMethod($control, $method);
					$confirm = $this->annotReader->getMethodAnnotation($reflection, ConfirmAnnotation::class);
					if ($confirm instanceof Confirm) {
						$confirm->setControl($control);
						$confirm->setHandleAllow($name);
						$confirm->setTranslator($this->translator);
						return $confirm;
					}
				}
				continue;
			}
			$control = $child;
		}
		return NULL;
	}

}
