<?php
namespace Testing;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Testing\Dto\Creator;
use Testing\Module\DynamicFixturesTrait;
use Testing\Module\MockTrait;
use Testing\Module\ServiceManagerTrait;
use Throwable;

class BaseTestCase extends AbstractHttpControllerTestCase
{
	use ServiceManagerTrait;
	use DynamicFixturesTrait;
	use MockTrait;

	/**
	 * @throws Throwable
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
	 * @throws Throwable
	 */
	protected function getService(string $class): mixed
	{
		return $this
			->getApplicationServiceLocator()
			->get($class);
	}
}
