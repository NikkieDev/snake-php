<?php

include_once "Player.php";
include_once "Util/Graphics.php";

class Settings
{
	public bool $multiplayer = false;
	private array $keys = [];

	/**
	 * <summary>
	 * Ask user wanting to play multiplayer
	 * </summary>
	 *
	 * <algo>
	 * Ask for a single digit input, keep asking until the value is either 1 or 2.
	 * If value is 2, multiplayer is enabled.
	 * </algo>
	 */
	public function askMultiplayer(): void
	{
		$gamemode = null;
		echo "1) Singleplayer\n2) Multiplayer" . PHP_EOL;
		echo ">> ";

		// Take single digit number input
		fscanf(STDIN, "%d\n", $gamemode);
		
		// Check if gamemode is valid
		if ($gamemode > 2 || empty($gamemode)) {
			echo "Invalid play option".PHP_EOL;
			
			usleep(500000);
			Graphics::clearScreen();

			$this->askMultiplayer();
			return;
		}

		// set multipler if selected
		$this->multiplayer = $gamemode == 2;
		Graphics::clearScreen();
	}

	/**
	 * <summary>
	 * Ask $player for keybinds to move snake
	 * </summary>
	 *
	 * <algo>
	 * Ask $player to define UP, DOWN, LEFT and RIGHT keybinds.
	 * </algo>
	 */
	public function definePlayerKeys(Player $player): void
	{
		echo $player->getName().", please define MOVEMENT keys\n".PHP_EOL;

		// Store keybinds per player.
		$this->keys['player'.$player->getId()]['UP'] = $this->askKey('UP');
		$this->keys['player'.$player->getId()]['DOWN'] = $this->askKey('DOWN');
		$this->keys['player'.$player->getId()]['LEFT'] = $this->askKey('LEFT');
		$this->keys['player'.$player->getId()]['RIGHT'] = $this->askKey('RIGHT');

		Graphics::clearScreen();

		// Display the chosen keys, ex:
		// UP => k
		// DOWN => j
		// LEFT => h
		// RIGHT => l
		echo 'Keys chosen'.PHP_EOL;
		foreach($this->keys['player'.$player->getId()] as $action => $key) {
			echo $action.' = '.$key.PHP_EOL;
		}

		$input = '';

		// Ask for a y/n input. Keep asking until valid input.
		while (empty($input) || (strtolower($input) !== 'y' && strtolower($input) !== 'n')) {
			echo 'Is this correct? (Y/N)'.PHP_EOL;
			echo '>> ';

			fscanf(STDIN, "%c\n", $input);
		}

		// If the keys are not satisfactory, redefine them.
		if (strtolower($input) !== 'y') {
			$this->definePlayerKeys($player);
			return;
		}
	}

	/**
	 * <summary>
	 * Get keybinds for a Player
	 * </summary>
	 */
	public function getKeys(Player $player): array
	{
		return $this->keys['player'.$player->getId()];
	}

	/**
	 * <summary>
	 * Ask for a key
	 * </summary>
	 */
	private function askKey(string $action): string
	{
		$key = '';

		echo "Please press a key for ".$action.": ";
		fscanf(STDIN, "%c\n", $key);

		// Keep asking for a key until given
		while (empty($key)) {
			return $this->askKey($action);
		}

		// Return key in lowercase;
		return strtolower($key);
	}
}
