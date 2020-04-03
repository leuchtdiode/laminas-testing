<?php
namespace Testing;

use Doctrine\ORM\Tools\ToolsException;
use Testing\Dto\Creator;
use Testing\Module\DynamicFixturesTrait;
use Testing\Module\MockTrait;
use Testing\Module\ServiceManagerTrait;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class BaseTestCase extends AbstractHttpControllerTestCase
{
	use ServiceManagerTrait;
	use DynamicFixturesTrait;
	use MockTrait;

	/**
	 * @throws ToolsException
	 */
	public function setUp(): void
	{
		$this->reset();

		$this->setApplicationConfig(
			include __DIR__ . '/../../../../config/application.test.config.php'
		);

		Creator::setServiceManager(
			$this->getApplicationServiceLocator()
		);

		$this->createEmptyDb();
	}

	/**
	 * @param string $class
	 * @return mixed
	 */
	protected function getService($class)
	{
		return $this->getApplicationServiceLocator()->get($class);
	}
}
