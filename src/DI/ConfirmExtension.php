<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Confirm\DI;

use Chomenko\Confirm\ApplicationListener;
use Chomenko\Confirm\Modal\ConfirmModal;
use Chomenko\Confirm\Modal\IConfirmModal;
use Chomenko\Modal\DI\ModalExtension;
use Kdyby\Events\DI\EventsExtension;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class ConfirmExtension extends CompilerExtension
{

	const CONFIRM_PARAMETER_KEY = "@confirm";

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix("modal"))
			->setFactory(ConfirmModal::class)
			->setImplement(IConfirmModal::class)
			->addTag(ModalExtension::TAG_FACTORY);

		$translator = NULL;
		if (array_key_exists("translator", $this->config)) {
			$class = $this->config["translator"];
			$translator = $builder->getDefinitionByType($class);
		}

		$builder->addDefinition($this->prefix("application.listener"))
			->setFactory(ApplicationListener::class, ['translator' => $translator])
			->addTag(EventsExtension::TAG_SUBSCRIBER);
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('Confirm', new ConfirmExtension());
		};
	}

}
