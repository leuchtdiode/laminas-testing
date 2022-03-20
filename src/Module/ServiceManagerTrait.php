<?php
namespace Testing\Module;

trait ServiceManagerTrait
{
	protected function getInstance(string $className): mixed
	{
		return $this
			->getApplicationServiceLocator()
			->get($className);
	}
}