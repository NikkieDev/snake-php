<?php

include_once "Snake.php";

class Player
{
	private Snake $snake;
	private string $name = '';

	/*
	 * <summary>
	 * Create a new player with a snake and a name. Give player an ID. This is it's player number.
	 * </summary>
	 */
	public function __construct(private readonly int $id)
	{
		$this->askForName();
		$this->snake = new Snake();
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * <summary>
	 * Get player score
	 * </summary>
	 *
	 * <algo>
	 * Player score is equal to the size of their snake
	 * </algo>
	 */
	public function getScore(): int
	{
		return count($this->getSnake()->getBody());
	}

	public function getSnake(): Snake
	{
		return $this->snake;
	}

	/**
	 * <summary>
	 * Ask player for a username
	 * </summary>
	 *
	 * <algo>
	 * Ask for a valid username from the player. Keep asking until valid input is given
	 * </algo>
	 */
	private function askForName(): void
	{
		echo "Player ".$this->id.", what is your name: ";
		fscanf(STDIN, "%s\n", $this->name);

		if (empty($this->name)) {
			$this->askForName();
			return;
		}

		$this->name = $this->name;
	}
}
