<?php

include_once "Player.php";

class Settings
{
	public bool $multiplayer = false;
	private array $keys = [];

	public function askMultiplayer(): void
	{
		$multiplayerChoice = null;
		echo "1) Singleplayer\n2) Multiplayer" . PHP_EOL;
		echo ">> ";

		fscanf(STDIN, "%d\n", $multiplayerChoice);

		if ($multiplayerChoice > 2 || empty($multiplayerChoice)) {
			echo "Invalid play option".PHP_EOL;
			
			sleep(1);
			system('clear');

			$this->askMultiplayer();
			return;
		}

		 $this->multiplayer = $multiplayerChoice == 1 ? false : true;
		 system('clear');
	}

	public function definePlayerKeys(Player $player): void
	{
		echo $player->getName().", please define MOVEMENT keys\n".PHP_EOL;

		$this->keys['player'.$player->getId()]['UP'] = $this->askKey('UP');
		$this->keys['player'.$player->getId()]['DOWN'] = $this->askKey('DOWN');
		$this->keys['player'.$player->getId()]['LEFT'] = $this->askKey('LEFT');
		$this->keys['player'.$player->getId()]['RIGHT'] = $this->askKey('RIGHT');

		system('clear');
		echo 'Keys chosen'.PHP_EOL;
		foreach($this->keys['player'.$player->getId()] as $action => $key) {
			echo $action.' = '.$key.PHP_EOL;
		}

		$input = '';

		while (empty($input) || (strtolower($input) !== 'y' && strtolower($input) !== 'n')) {
			echo 'Is this correct? (Y/N)'.PHP_EOL;
			echo '>> ';

			fscanf(STDIN, "%c\n", $input);
		}

		if (strtolower($input) !== 'y') {
			$this->definePlayerKeys($player);
			return;
		}
	}


	private function askKey(string $action): string
	{
		$key = '';

		echo "Please press a key for ".$action.": ";
		fscanf(STDIN, "%c\n", $key);

		return $key;
	}
}
