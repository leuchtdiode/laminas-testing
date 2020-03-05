<?php
namespace Testing\Module;

trait ServiceManagerTrait
{
	/**
	 * @param string $className
	 * @return mixed
	 */
	protected function getInstance($className)
	{
		return $this
			->getApplicationServiceLocator()
			->get($className);
	}
}