<?php

include_once "Settings.php";
include_once "Player.php";
include_once "Util/Graphics.php";

class Game
{
	private const float TICK = 15.6 * 1000;

	public function __construct(private readonly array $players, private readonly Settings $gameSettings)
	{
		
	}

	public function start()
	{
		Graphics::hideCursor();
		Graphics::clearScreen();
		Graphics::drawBorder(50, 20);

		

		while (true) {
			usleep(self::TICK);
			if (!$this->update()) {
				$this->decideWinner();
				break;
			}
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
		
	}

	private function decideWinner(): void
	{

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
