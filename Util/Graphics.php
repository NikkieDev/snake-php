<?php

include_once "Snake.php";
include_once "Util/Point.php";

class Graphics
{
	public static function clearScreen(): void
	{
		system('clear');
	}

	public static function hideCursor(): void
	{
		echo "\033[?25l";
	}

	public static function moveCursor(Point $point): void
	{
		echo "\033[".$point->y.";".$point->x."H";
	}

	public static function drawBorder(int $w, int $h): void
	{
		for ($i = 1; $i <= $w; $i++) {
			self::moveCursor(new Point($i, 1));
			echo "#";

			self::moveCursor(new Point($i, $h));
			echo "#";
		}

		for ($i = 2; $i <= $h; $i++) {
			self::moveCursor(new Point(1, $i));
			echo "#";

			self::moveCursor(new Point($w, $i));
			echo "#";
		}
	}

	public static function drawSnake(Snake $snake): void
	{
		foreach ($snake->getBody() as $bodyPart) {
			echo $bodyPart;
		}
	}
}
