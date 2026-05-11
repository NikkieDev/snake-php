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

	/**
	 * <summary>
	 * Start the game
	 * </summary>
	 *
	 * <algo>
	 * Starts the game. Call a frame every TICK milliseconds while no one has lost.
	 * If someone loses, clear the screen and display the 'Game over' screen based on gamemode
	 * </algo>
	 */
	public function start()
	{
		$this->init();

		while ($this->update()) {
			usleep(self::TICK);
		}

		Graphics::clearScreen();

		if (!$this->gameSettings->multiplayer) {
			echo "GAME OVER".PHP_EOL;;
			echo "Score: ". $this->getPlayerOne()->getScore().PHP_EOL;
		} else {
			$alive = array_find($this->players, fn(Player $player) => !$player->getSnake()->isDead());
			echo $alive->getName().' Won!'.PHP_EOL;
		}
	}

	/**
	 * <summary>
	 * Initialize the game
	 * </summary>
	 *
	 * <algo>
	 * Set terminal to accept input without requiring buffer flush
	 * Make input stream non blocking.
	 * Hide the cursor in the terminal, clear the screen and create a playing field.
	 * Spawn a fruit that can be rendered and picked up by snake.
	 * Spawn both snakes
	 * </algo>
	 */
	private function init()
	{
		system('stty -icanon -echo');
		stream_set_blocking(STDIN, false);

		Graphics::hideCursor();
		Graphics::clearScreen();
		Graphics::drawBorder(50, 20);

		$this->spawnFruit();

		$snakeOnePos = new Point(10, 5);
		$snakeTwoPos = new Point(40, 5);

		// Set initial snake location and move snake
		$this->getPlayerOne()->getSnake()->setLocation($snakeOnePos);
		$this->getPlayerOne()->getSnake()->move();

		// If multiple players, set second players' snake location and move their snake
		if ($this->gameSettings->multiplayer) {
			$this->getPlayerTwo()->getSnake()->setLocation($snakeTwoPos);
			$this->getPlayerTwo()->getSnake()->move();
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

		// Move all snakes, unless one has died.
		foreach ($this->players as $player) {
			if ($player->getSnake()->isDead()) return false;
			$player->getSnake()->move();
		}

		// Render spawned fruit.
		Graphics::drawFruit($this->fruitLocation);

		return true;
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
			// Check if snake is touching border.
			if (in_array($player->getSnake()->getLocation()->x, $borderX) || in_array($player->getSnake()->getLocation()->y, $borderY)) {
				$player->getSnake()->kill();
			}

			// Check if snake is touching a snake body part
			if (in_array($player->getSnake()->getLocation(), $this->getAllPlayerBodyLocations())) {
				$player->getSnake()->kill();
			}

			// Check if snake has found a fruit
			// 'Eat' the fruit, grow the snake
			// Calculate a new fruit.
			if ($player->getSnake()->getLocation() == $this->fruitLocation) {
				$player->getSnake()->grow();
				$this->spawnFruit();
			}
		}
	}

	/**
	 * <summary>
	 * Get all snake body part locations
	 * </summary>
	 *
	 * <algo>
	 * Create a list by looping through all players and getting the body part locations of those snakes.
	 * </algo>
	 */
	private function getAllPlayerBodyLocations(): array
	{
		$locations = [];

		foreach ($this->players as $player)
			foreach ($player->getSnake()->getBody() as $partLocation)
				$locations[] = $partLocation;

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
		// Get input from stream
		$key = stream_get_contents(STDIN, 1);

		// If no key was pressed just continue
		if (!$key) {
			return;
		}

		// Check the key against every players' keybinds
		foreach ($this->players as $player) {
			// Get the keybinds for the current player
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
	}

	private function getPlayerOne(): Player
	{
		return $this->players[0];
	}

	/**
	 * <summary>
	 * Get second player, only available in multiplayer
	 * </summary>
	 */
	private function getPlayerTwo(): ?Player
	{
		if (!$this->gameSettings->multiplayer || 1 === count($this->players)) return null;

		return $this->players[1];
	}
}
