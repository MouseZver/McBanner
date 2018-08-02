<?php

namespace Aero\Low\Lerma;

use Aero;

trait Migrate
{
	public $query;
	
	/*
		- Определение запроса на форматирование строки
	*/
	public static function query( $sql ): Aero\Database\Compare
	{
		self::instance() -> dead() -> driver -> query( is_array ( $sql ) ? sprintf ( ...$sql ) : $sql );
		
		self::instance() -> driver -> isError();

		return self::instance();
	}
	
	/*
		- Кол-во затронутых строк
	*/
	public function rowCount(): int
	{
		return $this -> driver -> rowCount();
	}
	
	/*
		- Кол-во затронутых колонок
	*/
	public function countColumn(): int
	{
		return $this -> driver -> countColumn();
	}
}