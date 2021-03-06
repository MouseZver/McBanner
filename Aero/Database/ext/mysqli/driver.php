<?php

use Aero\Supports\Lerma;

return new class ( $this, $lerma -> {$lerma -> driver} ) implements Aero\Interfaces\Lerma\IDrivers
{
	private $connect, $result;
	
	/*
		- Подключение...
	*/
	public function __construct ( Lerma $lerma, array $params = [] )
	{
		if ( empty ( $params ) )
		{
			throw new Error( 'Params expects most parameter values, returned empty' );
		}
		
		$this -> lerma = $lerma;
		
		$params = array_values ( $params );
		
		$this -> connect = new mysqli( ...$params );
		
		if ( $this -> connect -> connect_error ) 
		{
			throw new Error( sprintf ( 'Error connect (%s) %s', $this -> connect -> connect_errno, $this -> connect -> connect_error ) );
		}
		
		$this -> connect -> set_charset( 'utf8' );
	}
	
	public function isError( $obj = null )
	{
		$obj = $obj ?? $this -> connect;
		
		if ( $obj -> errno )
		{
			throw new Error( $obj -> error );
		}
	}
	
	public function query( string $sql )
	{
		return $this -> lerma -> query = $this -> connect -> query( $sql );
	}
	
	public function prepare( string $sql )
	{
		return $this -> lerma -> statement = $this -> connect -> prepare( $sql );
	}
	
	public function execute()
	{
		return $this -> lerma -> statement -> execute();
	}
	
	public function binding( array ...$binding )
	{
		$count = count ( $binding[0] );
		
		$for = ( array ) implode ( '', array_map ( function ( $arg )
		{
			if ( !in_array ( $type = gettype ( $arg ), [ 'integer', 'double', 'string' ] ) )
			{
				throw new Error( 'Invalid type ' . $type );
			}
			
			return $type[0];
		}, 
		$binding[0] ) );

		for ( $i = 0; $i < $count; $i++ )
		{	
			$for[] = &${ 'bind_' . $i };
		}

		$ReflectionMethod = new ReflectionMethod( 'mysqli_stmt', 'bind_param' );

		$ReflectionMethod -> invokeArgs( $this -> lerma -> statement, $for );

		foreach ( $binding AS $items )
		{
			$items = $this -> lerma -> executeHolders( $items );
			
			extract ( $items, EXTR_PREFIX_ALL, 'bind' );
			
			$this -> execute();
		}
	}
	
	public function bindResult( $result )
	{
		return $this -> lerma -> statement -> bind_result( ...$result );
	}
	
	public function close()
	{
		( $this -> lerma -> statement ?? $this -> lerma -> query ) -> close();
		
		$this -> lerma -> statement = $this -> lerma -> query = $this -> result = null;
	}
	
	/*
		- Определение типа запроса в базу данных
	*/
	protected function result()
	{
		if ( $this -> lerma -> statement !== null )
		{
			return $this -> result ?? $this -> result = $this -> lerma -> statement -> get_result();
		}
		
		return $this -> lerma -> query;
	}
	
	public function fetch( int $int )
	{
		switch ( $int )
		{
			case Lerma::FETCH_NUM:
				return $this -> result() -> fetch_array( MYSQLI_NUM );
			break;
			case Lerma::FETCH_ASSOC:
				return $this -> result() -> fetch_array( MYSQLI_ASSOC );
			break;
			case Lerma::FETCH_OBJ:
				return $this -> result() -> fetch_object();
			break;
			case Lerma::FETCH_BIND:
				return $this -> lerma -> statement -> fetch();
			break;
			case Lerma::FETCH_FIELD:
				return (array) $this -> result() -> fetch_field();
			break;
			default:
				return null;
		}
	}
	
	public function fetchAll( int $int )
	{
		switch ( $int )
		{
			case Lerma::FETCH_NUM:
				return $this -> result() -> fetch_all( MYSQLI_NUM );
			break;
			case Lerma::FETCH_ASSOC:
				return $this -> result() -> fetch_all( MYSQLI_ASSOC );
			break;
			case Lerma::FETCH_FIELD:
				return $this -> result() -> fetch_fields();
			break;
			default:
				return null;
		}
	}
	
	public function countColumn(): int
	{
		return $this -> connect -> field_count;
	}
	
	public function rowCount(): int
	{
		return $this -> result() -> num_rows;
	}
	
	public function InsertID(): int
	{
		return $this -> connect -> insert_id;
	}
	
	public function rollBack( ...$rollback ): bool
	{
		return $this -> connect -> rollback( ...$rollback );
	}
	
	public function beginTransaction( ...$rollback ): bool
	{
		return $this -> connect -> begin_transaction( ...$rollback );
	}
	
	public function commit( ...$commit ): bool
	{
		return $this -> connect -> commit( ...$commit );
	}
};
