<?php

include_once "Settings.php";
include_once "Player.php";
include_once "Util/Graphics.php";
include_once "Util/Point.php";

class Game
{
	private const float TICK = 15.6 * 1000;

	public function __construct(private readonly array $players, private readonly Settings $gameSettings)
	{
		
	}

	public function start()
	{
		$this->init();

		while (true) {
			usleep(self::TICK);
			if (!$this->update()) {
				$winner = $this->decideWinner();
				Graphics::clearScreen();
				echo $winner->getName().' Wins!';
				break;
			}
		}
	}

	private function init()
	{
		system('stty -icanon -echo');
		stream_set_blocking(STDIN, false);

		Graphics::hideCursor();
		Graphics::clearScreen();
		Graphics::drawBorder(50, 20);

		$snakeOnePos = new Point(10, 5);
		$snakeTwoPos = new Point(40, 5);

		Graphics::moveCursor($snakeOnePos);
		Graphics::drawSnake($this->getPlayerOne()->getSnake());
		$this->getPlayerOne()->getSnake()->setLocation($snakeOnePos);

		if ($this->getPlayerTwo()) {
			Graphics::moveCursor($snakeTwoPos);
			Graphics::drawSnake($this->getPlayerTwo()->getSnake());
			$this->getPlayerTwo()->getSnake()->setLocation($snakeTwoPos);
		}
	}

	/**
	 * <summary>
	 * Update the game state, 'tick'.
	 * </summary>
	 *
	 * <algo>
	 * Update all graphical aspects, snake positions, snake sizes
	 * And take any inputs
	 * </algo>
	 */
	private function update()
	{
		$this->handleInput();

		Graphics::clearScreen();
		Graphics::drawBorder(50, 20);

		Graphics::moveCursor($this->getPlayerOne()->getSnake()->getLocation());
		Graphics::drawSnake($this->getPlayerOne()->getSnake());

		if ($this->getPlayerTwo()) {
			Graphics::moveCursor($this->getPlayerTwo()->getSnake()->getLocation());
			Graphics::drawSnake($this->getPlayerTwo()->getSnake());
		}
		return true;
	}

	/**
	 * <summary>
	 * Listen for input and handle actions for keybinds
	 * </summary>
	 *
	 * <algo>
	 * Listen to standard input stream and execute any action defined by player keybinds
	 * </algo>
	 */
	private function handleInput()
	{
		$key = stream_get_contents(STDIN, 1);

		if (!$key) {
			return;
		}

		// Figure out getting the keys from settings and listening to them
	}

	private function decideWinner(): Player 
	{
		return array_find($this->players, fn(Player $player) => !$player->getSnake()->isDead());
	}

	private function getPlayerOne(): Player
	{
		return $this->players[0];
	}

	private function getPlayerTwo(): ?Player
	{
		if (!$this->gameSettings->multiplayer || 1 === count($this->players)) return null;

		return $this->players[1];
	}
}
