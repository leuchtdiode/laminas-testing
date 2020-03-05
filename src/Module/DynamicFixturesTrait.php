<?php
namespace Testing\Module;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Testing\Dto\CreationResult;
use Laminas\Mvc\Application;

trait DynamicFixturesTrait
{
	/**
	 * @throws ToolsException
	 */
	private function createEmptyDb(): void
	{
		/** @var Application $app */
		$app = $this->getApplication();

		/** @var EntityManager $em */
		$em = $app->getServiceManager()->get(EntityManager::class);

		$db      = __DIR__ . '/../../../../../data/testing/test.sqlite';
		$emptyDb = __DIR__ . '/../../../../../data/testing/test-empty.sqlite';

		if (!file_exists($emptyDb)) // TODO always create if something changed?
		{
			$metaData = $em->getMetadataFactory()->getAllMetadata();
			$schema   = new SchemaTool($em);
			$schema->createSchema($metaData);

			copy($db, $emptyDb);
		}
		else
		{
			copy($emptyDb, $db);
		}
	}

	/**
	 * @param array $entities
	 * @param bool $clearUnitOfWork
	 * @throws \Doctrine\Common\Persistence\Mapping\MappingException
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	protected function fillDb(array $entities, bool $clearUnitOfWork = false)
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
