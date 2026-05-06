<?php

include_once "Snake.php";

class Player
{
	private ?Snake $snake = null;
	private string $name = '';
	private int $score = 0;

	public function __construct(private readonly int $id)
	{
		$this->askForName();
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getScore(): int
	{
		return $this->score;
	}

	public function scoreIncrement(): void
	{
		$this->score++;
	}

	public function getSnake(): Snake
	{
		return $this->snake;
	}

	public function setSnake(Snake $snake): void
	{
		$this->snake = $snake;
	}

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
