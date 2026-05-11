<?php

include_once "Settings.php";
include_once "Player.php";
include_once "Snake.php";
include_once "Game.php";
include_once "Util/Graphics.php";

// Create player array
$players = [];

// Instantiate game settings
$gameSettings = new Settings();
$gameSettings->askMultiplayer();

// Ask user for gamemode
Graphics::clearScreen();
echo "Selected mode: ".($gameSettings->multiplayer ? 'Multiplayer' : 'Singleplayer').PHP_EOL;
$players[] = new Player(1);

// If game is multiplayer, add a second player.
if ($gameSettings->multiplayer) {
	$players[] = new Player(2);
}

// Ask all players for keybinds
foreach ($players as $player) $gameSettings->definePlayerKeys($player);

// Start game with players and settings
$game = new Game($players, $gameSettings);
$game->start();
