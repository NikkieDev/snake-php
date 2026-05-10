<?php

include_once "Enum/Direction.php";

class Snake
{
	private Direction $direction = Direction::RIGHT;
	private bool $dead = false;
	private array $body = ['#'];

	public function __construct()
	{
	}

	/**
	 * <summary>
	 * Grow snake tail
	 * </summary>
	 *
	 * <algo>
	 * Add a node to snake's body array
	 * Move it and attach it to snake
	 * </algo>
	 */
	public function grow(): void
	{
		$this->body[] = '#';
	}

	/**
	 * <summary>
	 * Move snake and it's body to $this->direction
	 * </summary>
	 *
	 * <algo>
	 * Loop through body parts and move each part to the position of the next
	 * Move head to desired position using $this->direction
	 * </algo>
	 */
	public function move(): void
	{
	}

	public function getBody(): array
	{
		return $this->body;
	}

	public function setDirection(Direction $direction): void
	{
		$this->direction = $direction;
	}

	public function isDead(): bool
	{
		return $this->dead;
	}
}
