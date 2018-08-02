<?php

namespace Aero\Database;

use Exception AS Error;

class MigrateStmt
{
	public $statement;
	
	/*
		- Определение подготовленного запроса на форматирование строки
	*/
	protected function prepare( $sql ): MigrateStmt
	{
		if ( strpos ( $sql, '?:' ) === false )
		{
			throw new Error( 'Метки параметров запроса отсутствуют. Используйте функцию query' );
		}
		
		$this -> dead() -> driver -> prepare( $sql );
		
		$this -> driver -> isError();
		
		return $this;
	}
	
	/*
		- Посылаем данные в астрал
	*/
	protected function execute( array $executes )
	{
		if ( $this -> statement === null )
		{
			throw new Error( 'This is not statement' );
		}
		
		$executes = ( is_array ( $executes[0] ) ? $executes : [ $executes ] );
		
		$this -> driver -> binding( ...$executes );
		
		$this -> driver -> isError( $this -> statement );
		
		return ( $this -> countColumn() === 0 ?: $this );
	}
}