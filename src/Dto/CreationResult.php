<?php
namespace Testing\Dto;

class CreationResult
{
	/**
	 * @var mixed
	 */
	private $dto;

	/**
	 * @var mixed
	 */
	private $entity;

	/**
	 * @return mixed
	 */
	public function getDto()
	{
		return $this->dto;
	}

	/**
	 * @param mixed $dto
	 */
	public function setDto($dto): void
	{
		$this->dto = $dto;
	}

	/**
	 * @return mixed
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * @param mixed $entity
	 */
	public function setEntity($entity): void
	{
		$this->entity = $entity;
	}
}