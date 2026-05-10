<?php

include_once "Settings.php";
include_once "Player.php";
include_once "Snake.php";
include_once "Game.php";

$snakes = [];

$gameSettings = new Settings();
$gameSettings->askMultiplayer();

echo "Selected mode: ".($gameSettings->multiplayer ? 'Multiplayer' : 'Singleplayer').PHP_EOL;

$players = [$playerOne = new Player(1)];
$playerTwo = null;

$gameSettings->definePlayerKeys($playerOne);
$playerOne->setSnake(new Snake());
$snakes[] = $playerOne->getSnake();

if ($gameSettings->multiplayer) {
	$players[] = $playerTwo = new Player(2);

	$gameSettings->definePlayerKeys($playerTwo);
	$playerTwo->setSnake(new Snake());
	$snakes[] = $playerTwo->getSnake();
}

$game = new Game($players, $gameSettings);
$game->start();
