<?php

/**
 * agregado por Ricardo Muñoz
 * para usarlo en reportes
 */
class ClassStatic
{
	public static function dec2($value)
	{
		return round($value, 2);
	}
	public static function NumberFormat($value)
	{
		return number_format(self::dec2($value), 2, ",", ".");
	}
}
