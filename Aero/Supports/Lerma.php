<?php

/*
	@ Author: MouseZver
	@ Email: mouse-zver@xaker.ru
	@ url-source: http://github.com/MouseZver/Lerma
	@ php-version 7.2
*/

namespace Aero\Supports;

use Aero\Database\Compare;
use Throwable;
use Exception AS Error;

final class Lerma extends Compare #implements Instance
{
	const
		FETCH_NUM		= 1,
		FETCH_ASSOC		= 2,
		FETCH_OBJ		= 4,
		FETCH_BIND		= 663,
		FETCH_COLUMN	= 265,
		FETCH_KEY_PAIR	= 307,
		FETCH_NAMED		= 173,
		FETCH_UNIQUE	= 333,
		FETCH_GROUP		= 428,
		FETCH_FUNC		= 586,
		FETCH_CLASS		= 977,
		FETCH_CLASSTYPE	= 473,
		FETCH_FIELD		= 343;
	
	public static function __callStatic( $method, $args )
	{
		try
		{
			if ( $method === 'prepare' )
			{
				[ $sql, $executes ] = $args;
				
				if ( empty ( $executes ) )
				{
					throw new Error( 'Данные пусты. Используйте функцию query' );
				}
				
				$instance = static::instance();
				
				return $instance -> prepare( $instance -> replaceHolders( $sql ) ) -> execute( $executes );
			}
			
			return null;
		}
		catch ( Throwable $t )
		{
			static::instance() -> driver -> rollBack();

			static::instance() -> exceptionIDriver( $t );
		}
	}
	
	protected function exceptionIDriver( Throwable $t )
	{
		exit ( sprintf ( '<pre>IDriver: %s' . PHP_EOL . '%s</pre>', $t -> getMessage(), $t -> getTraceAsString() ) );
	}
}
