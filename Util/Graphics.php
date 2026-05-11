<?php

include_once "Snake.php";
include_once "Util/Point.php";

class Graphics
{
	/**
	 * <summary>
	 * Clear screen with bash command
	 * </summary>
	 */
	public static function clearScreen(): void
	{
		system('clear');
	}

	/**
	 * <summary>
	 * Hide cursor using ANSI escape characters
	 * </summary>
	 */
	public static function hideCursor(): void
	{
		echo "\033[?25l";
	}

	/**
	 * <summary>
	 * Move cursor to X & Y of $point
	 * </summary>
	 *
	 * <algo>
	 * Use ANSI characters to place cursor at X & Y of $point
	 * </algo>
	 */
	public static function moveCursor(Point $point): void
	{
		echo "\033[".$point->y.";".$point->x."H";
	}

	/**
	 * <summary>
	 * Draw border for game with $w for width and $h for height
	 * </summary>
	 *
	 * <algo>
	 * Draw border of $w width and $h height. Loop through the axes and place the border character
	 * </algo>
	 */
	public static function drawBorder(int $w, int $h): void
	{
		for ($i = 1; $i <= $w; $i++) {
			// Place top border
			self::moveCursor(new Point($i, 1));
			echo "#";

			// Place bottom border
			self::moveCursor(new Point($i, $h));
			echo "#";
		}

		for ($i = 2; $i <= $h; $i++) {
			// Place left border
			self::moveCursor(new Point(1, $i));
			echo "#";

			// Place right border
			self::moveCursor(new Point($w, $i));
			echo "#";
		}
	}

	/**
	 * <summary>
	 * Draw $snake from head to tail
	 * </summary>
	 * 
	 * <algo>
	 * Place head at cursor, then loop through body part locations and move cursor to them. Then place each body part.
	 * </algo>
	 */
	public static function drawSnake(Snake $snake): void
	{
		echo "@";
		foreach ($snake->getBody() as $bodyPart) {
			self::moveCursor($bodyPart);
			echo "0";
		}
	}

	/**
	 * <summary>
	 * Draw a fruit at $position
	 * </summary>
	 */
	public static function drawFruit(Point $position): void
	{
		self::moveCursor($position);
		echo "$";
	}
}
