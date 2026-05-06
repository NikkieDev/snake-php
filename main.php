<?php

include_once "Settings.php";
include_once "Player.php";

$gameSettings = new Settings();
$gameSettings->askMultiplayer();

echo "Selected mode: ".($gameSettings->multiplayer ? 'Multiplayer' : 'Singleplayer').PHP_EOL;

$playerOne = new Player(1);
$playerTwo = null;

$gameSettings->definePlayerKeys($playerOne);

if ($gameSettings->multiplayer) {
	$playerTwo = new Player(2);
	$gameSettings->definePlayerKeys($playerTwo);
}

