<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 29.11.2018 10:20
 */

namespace Chomenko\Confirm;

use Doctrine\Common\Annotations\Annotation;
use Nette\Application\IResponse;
use Nette\Application\UI\Control;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Localization\ITranslator;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Confirm implements ConfirmAnnotation
{

	const TYPE_DANGER = "modal-danger";
	const TYPE_SUCCESS = "modal-success";
	const TYPE_WARNING = "modal-warning";
	const TYPE_INFO = "modal-info";
	const TYPE_DEFAULT = "modal-default";

	/**
	 * @var string
	 */
	public $label = NULL;

	/**
	 * @var string
	 */
	public $question = "Are you sure?";

	/**
	 * @var string
	 */
	public $yes = "yes";

	/**
	 * @var string
	 */
	public $not = "not";

	/**
	 * @var string
	 */
	public $type = self::TYPE_WARNING;

	/**
	 * @var bool
	 */
	public $ajax = FALSE;

	/**
	 * @var bool
	 */
	public $translate = TRUE;

	/**
	 * @var string
	 */
	public $deniedDestination = NULL;

	/**
	 * @var string
	 */
	public $translateFile = NULL;

	/**
	 * @var Control
	 */
	private $control;

	/**
	 * @var string
	 */
	private $handleAllow;

	/**
	 * @var ITranslator
	 */
	private $translator;

	/**
	 * @var Request
	 */
	private $originalRequest;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Presenter
	 */
	private $presenter;

	/**
	 * @var bool
	 */
	private $skip = FALSE;

	/**
	 * @return Control
	 */
	public function getControl(): Control
	{
		return $this->control;
	}

	/**
	 * @param Control $control
	 * @return $this
	 */
	public function setControl($control)
	{
		$this->control = $control;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHandleAllow(): string
	{
		return $this->handleAllow;
	}

	/**
	 * @param string $handleAllow
	 */
	public function setHandleAllow(string $handleAllow): void
	{
		$this->handleAllow = $handleAllow;
	}

	/**
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return ITranslator
	 */
	public function getTranslator(): ?ITranslator
	{
		return $this->translator;
	}

	/**
	 * @return Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * @param Request $request
	 * @return $this
	 */
	public function setRequest($request)
	{
		$this->request = $request;
		return $this;
	}

	/**
	 * @return Presenter
	 */
	public function getPresenter(): Presenter
	{
		return $this->presenter;
	}

	/**
	 * @param Presenter $presenter
	 * @return $this
	 */
	public function setPresenter($presenter)
	{
		$this->presenter = $presenter;
		return $this;
	}

	/**
	 * @return Request
	 */
	public function getOriginalRequest(): Request
	{
		return $this->originalRequest;
	}

	/**
	 * @param Request $originalRequest
	 * @return $this
	 */
	public function setOriginalRequest($originalRequest)
	{
		$this->originalRequest = $originalRequest;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSkip(): bool
	{
		return $this->skip;
	}

	/**
	 * @param bool $skip
	 */
	public function setSkip(bool $skip)
	{
		$this->skip = $skip;
	}

}
