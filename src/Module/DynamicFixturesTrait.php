<?php
namespace Testing\Module;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Laminas\Mvc\Application;
use Testing\Dto\CreationResult;
use Throwable;

trait DynamicFixturesTrait
{
	private string $db      = __DIR__ . '/../../../../../data/testing/test.sqlite';
	private string $emptyDb = __DIR__ . '/../../../../../data/testing/test-empty.sqlite';
	private string $dbHash  = __DIR__ . '/../../../../../data/testing/test.sqlite.hash';

	private static ?string $schemaHash = null;

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

		if (!self::$schemaHash)
		{
			$metaData = $em
				->getMetadataFactory()
				->getAllMetadata();

			$schema = new SchemaTool($em);

			self::$schemaHash = md5(json_encode($schema->getCreateSchemaSql($metaData)));
		}

		if (
			!file_exists($this->dbHash)
			|| file_get_contents($this->dbHash) !== self::$schemaHash
		)
		{
			$schema->createSchema($metaData);
		}

		copy($this->db, $this->emptyDb);

		file_put_contents($this->dbHash, self::$schemaHash);
	}

	protected function restoreEmptyDb(): void
	{
		if (file_exists($this->emptyDb))
		{
			copy($this->emptyDb, $this->db);
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
