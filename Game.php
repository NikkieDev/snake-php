<?php

include_once "Settings.php";
include_once "Player.php";
include_once "Util/Graphics.php";
include_once "Util/Point.php";

class Game
{
	private const float TICK = 200 * 1000;
	private ?Point $fruitLocation = null; 

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

		$this->spawnFruit();

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
	 * And take any inputs. Place fruit if eaten
	 * </algo>
	 */
	private function update()
	{
		$this->handleInput();
		$this->handleCollision();

		Graphics::clearScreen();
		Graphics::drawBorder(50, 20);

		foreach ($this->players as $player) {
			if ($player->getSnake()->isDead()) return false;

			$player->getSnake()->move();
			Graphics::moveCursor($player->getSnake()->getLocation());
			Graphics::drawSnake($player->getSnake());
		}

		$this->renderFruit();

		return true;
	}

	private function renderFruit(): void
	{
		Graphics::moveCursor($this->fruitLocation);
		echo "$";
	}

	/**
	 * <summary>
	 * Choose a random spot in the playfield and place a piece of fruit
	 * </summary>
	 *
	 * <algo>
	 * Pick a random number between 2 & BorderX-1 for X, and 2 & BorderY-1 for Y.
	 * If position is snake, recalculate. Otherwise place fruit.
	 * </algo>
	 */
	private function spawnFruit(): void
	{
		$snakeLocations = [...$this->getAllPlayerBodyLocations()];
		foreach ($this->players as $player) $snakeLocations[] = $player->getSnake()->getLocation();

		$x = rand(2, 49);
		$y = rand(2, 19);
		$location = new Point($x, $y);

		if (in_array($location, $snakeLocations)) {
			$this->spawnFruit();
			return;
		}

		$this->fruitLocation = $location;
	}

	/**
	 * <summary>
	 * Handle collision. Snake's head hitting other snake kills snake.
	 * Snake's head hitting fruit, grow snake.
	 * Snake's head hits the wall? Kill snake.
	 * </summary>
	 *
	 * <algo>
	 * Check all snakes;
	 * If snake head location equal to any fruit on the map, disappear that fruit and grow snake.
	 * If snake head location equal to the wall, kill snake. Other snake wins.
	 * If snake head location equal to that of the body part of any other snake, kill snake. The snake that was hit wins.
	 * </algo>
	 */
	private function handleCollision(): void
	{
		$borderX = [1, 50];
		$borderY = [1, 20];

		foreach ($this->players as $player) {
			if (in_array($player->getSnake()->getLocation()->x, $borderX) || in_array($player->getSnake()->getLocation()->y, $borderY)) {
				$player->getSnake()->kill();
			}

			if (in_array($player->getSnake()->getLocation(), $this->getAllPlayerBodyLocations())) {
				$player->getSnake()->kill();
			}

			if ($player->getSnake()->getLocation() == $this->fruitLocation) {
				$player->getSnake()->grow();
				$this->spawnFruit();
			}
		}
	}

	private function getAllPlayerBodyLocations(): array
	{
		$locations = [];

		foreach ($this->players as $player) {
			foreach ($player->getSnake()->getBodyPartLocations() as $partLocation) {
				$locations[] = $partLocation;
			}
		}

		return $locations;
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

		foreach ($this->players as $player) {
			$playerBinds = $this->gameSettings->getKeys($player);
			switch ($key) {
				case $playerBinds['UP']:
					$player->getSnake()->setDirection(Direction::UP);
					break;
				case $playerBinds['RIGHT']:
					$player->getSnake()->setDirection(Direction::RIGHT);
					break;
				case $playerBinds['DOWN']:
					$player->getSnake()->setDirection(Direction::DOWN);
					break;
				case $playerBinds['LEFT']:
					$player->getSnake()->setDirection(Direction::LEFT);
					break;
			}
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
