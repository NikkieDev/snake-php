<?php

include_once "Enum/Direction.php";

class Snake
{
	private Direction $direction = Direction::RIGHT;
	private ?Point $location = null;
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

	public function getBodyPartLocations(): array
	{
		$partLocations = [];

		for ($i = count($this->body)-1; $i > 1; $i--) {
			$partLocations[] = match($this->direction) {
				Direction::UP => new Point($this->location->x, $this->location->y+$i),
				Direction::RIGHT => new Point($this->location->x-$i, $this->location->y),
				Direction::DOWN => new Point($this->location->x, $this->location->y-$i),
				Direction::LEFT => new Point($this->location->x+$i, $this->location->y)
			};
		}

		return $partLocations;
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
		$this->location = match ($this->direction) {
			Direction::UP => new Point($this->location->x, $this->location->y-1),
			Direction::RIGHT => new Point($this->location->x+1, $this->location->y),
			Direction::DOWN => new Point($this->location->x, $this->location->y+1),
			Direction::LEFT => new Point($this->location->x-1, $this->location->y)
		};

		Graphics::moveCursor($this->location);
	}

	public function getLocation(): ?Point
	{
		return $this->location;
	}

	public function setLocation(Point $location): void
	{
		$this->location = $location;
	}

	public function getBody(): array
	{
		return $this->body;
	}

	public function setDirection(Direction $direction): void
	{
		$this->direction = $direction;
	}

	public function kill(): void
	{
		$this->dead = true;
	}

	public function isDead(): bool
	{
		return $this->dead;
	}
}
