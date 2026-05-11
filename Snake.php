<?php

include_once "Enum/Direction.php";

class Snake
{
	private Direction $direction = Direction::RIGHT;
	private ?Point $location = null;
	private bool $dead = false;
	private array $body = [];

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
		$last = end($this->body);

		if ($last === false) {
			$last = $this->location;
		}

		$this->body[] = clone $last;
	}

	/**
	 * <summary>
	 * Move snake and it's body to $this->direction
	 * </summary>
	 *
	 * <algo>
	 * Loop through body parts and move each part to the location of the next
	 * Move head to desired location using $this->direction
	 * </algo>
	 */
	public function move(): void
	{
		// Shift body parts up to head
	    for ($i = count($this->body) - 1; $i > 0; $i--) $this->body[$i] = clone $this->body[$i - 1];

		// If has a body, move first body part to head location
		if (count($this->body) > 0) $this->body[0] = clone $this->location;

		// Move head up, down, left or right depending on the Direction the snake is going.
		$this->location = match ($this->direction) {
			Direction::UP => new Point($this->location->x, $this->location->y-1),
			Direction::RIGHT => new Point($this->location->x+1, $this->location->y),
			Direction::DOWN => new Point($this->location->x, $this->location->y+1),
			Direction::LEFT => new Point($this->location->x-1, $this->location->y)
		};

		Graphics::moveCursor($this->location);
		Graphics::drawSnake($this);
	}
	
	/**
	 * <summary>
	 * Get position of head
	 * </summary>
	 */
	public function getLocation(): ?Point
	{
		return $this->location;
	}

	/**
	 * <summary>
	 * Set position of head
	 * </summary>
	 */
	public function setLocation(Point $location): void
	{
		$this->location = $location;
	}

	/**
	 * <summary>
	 * Get body parts
	 * </summary>
	 */
	public function getBody(): array
	{
		return $this->body;
	}

	/**
	 * <summary> 
	 * Change snake direction. Can not reverse the current direction.
	 * </summary>
	 *
	 * <algo>
	 * Change the snakes direction to the given $direction. If the $direction is the reverse of the current $this->direction
	 * or $direction === $this->direction, don't do anything.
	 * </algo>
	 */
	public function setDirection(Direction $direction): void
	{
		if (($direction == Direction::DOWN || $direction == Direction::UP) && ($this->direction == Direction::DOWN || $this->direction == Direction::UP)) return;
		if (($direction == Direction::RIGHT || $direction == Direction::LEFT) && ($this->direction == Direction::RIGHT || $this->direction == Direction::LEFT)) return;

		$this->direction = $direction;
	}

	/**
	 * <summary>
	 * Kill the snake
	 * </summary>
	 */
	public function kill(): void
	{
		$this->dead = true;
	}

	/**
	 * <summary>
	 * Check if snake has been killed
	 * </summary>
	 */
	public function isDead(): bool
	{
		return $this->dead;
	}
}
