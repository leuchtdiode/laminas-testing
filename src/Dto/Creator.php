<?php
namespace Testing\Dto;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

abstract class Creator
{
	/**
	 * @var ContainerInterface
	 */
	protected static $serviceManager;

	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * @param mixed $serviceManager
	 */
	public static function setServiceManager($serviceManager): void
	{
		self::$serviceManager = $serviceManager;
	}

	/**
	 */
	abstract public static function getInstance();

	/**
	 * @param array $data
	 *
	 * @return mixed
	 */
	abstract protected function getDto($data);

	/**
	 * @return string
	 */
	abstract protected function getEntityClass();

	/**
	 * @return array
	 */
	abstract protected function getDefaultData();

	/**
	 * @param $entity
	 * @return mixed
	 */
	abstract protected function createDto($entity);

	/**
	 * @param array $data
	 */
	public function setData(array $data): void
	{
		$this->data = $data;
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return static
	 */
	public function __call($name, $arguments)
	{
		$property = lcfirst(
			str_replace(
				'set',
				'',
				$name
			)
		);

		$this->data[$property] = $arguments[0];

		return $this;
	}

	/**
	 * @return CreationResult
	 */
	public function create()
	{
		$result = new CreationResult();

		$data = array_replace_recursive(
			$this->getDefaultData(),
			$this->data
		);

		$dto = $this->getDto($data);

		if ($dto)
		{
			$result->setDto($dto);
			$result->setEntity($dto->getEntity());

			return $result;
		}

		$entityClass = $this->getEntityClass();

		$entity = new $entityClass;

		foreach ($data as $property => $value)
		{
			$setter = 'set' . ucfirst($property);

			if (method_exists($entity, $setter))
			{
				$entity->{$setter}($value);
			}
		}

		self::$serviceManager
			->get(EntityManager::class)
			->persist($entity);

		$result->setDto(
			$this->createDto($entity)
		);
		$result->setEntity($entity);

		return $result;
	}
}