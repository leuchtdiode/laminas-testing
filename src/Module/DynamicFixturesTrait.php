<?php
namespace Testing\Module;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Testing\Dto\CreationResult;
use Laminas\Mvc\Application;
use Throwable;

trait DynamicFixturesTrait
{
	private static bool $createdEmptyDb = false;

	/**
	 * @throws Throwable
	 */
	private function createEmptyDb(): void
	{
		/** @var Application $app */
		$app = $this->getApplication();

		/** @var EntityManager $em */
		$em = $app
			->getServiceManager()
			->get(EntityManager::class);

		$db      = __DIR__ . '/../../../../../data/testing/test.sqlite';
		$emptyDb = __DIR__ . '/../../../../../data/testing/test-empty.sqlite';

		if (!self::$createdEmptyDb)
		{
			if (file_exists($db))
			{
				unlink($db);
			}

			if (file_exists($emptyDb))
			{
				unlink($emptyDb);
			}

			$metaData = $em
				->getMetadataFactory()
				->getAllMetadata();
			$schema   = new SchemaTool($em);
			$schema->createSchema($metaData);

			copy($db, $emptyDb);

			self::$createdEmptyDb = true;
		}
		else
		{
			copy($emptyDb, $db);
		}
	}

	/**
	 * @throws Throwable
	 */
	protected function fillDb(array $entities, bool $clearUnitOfWork = false): void
	{
		/** @var EntityManager $entityManager */
		$entityManager = $this->getInstance(EntityManager::class);

		foreach ($entities as $entity)
		{
			if ($entity instanceof CreationResult)
			{
				$entity = $entity->getEntity();
			}

			$entityManager->persist($entity);
		}

		$entityManager->flush();

		/*
		 * reset the unit of work to have the same conditions as if nothing ever happened
		 */
		if ($clearUnitOfWork)
		{
			$entityManager->clear();
		}
	}
}
