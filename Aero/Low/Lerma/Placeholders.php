<?php

namespace Aero\Low\Lerma;

trait Placeholders
{
	protected $pattern = '/(\?|:[a-z]{1,})/';	# Поиск плейсхолдеров в запросе
	protected $matches;							# Placeholders
	
	/*
		- Поиск ':', замена placeholders на '?'
	*/
	public function replaceHolders( $sql ): string
	{
		$this -> matches = [];
		
		$sql = ( is_array ( $sql ) ? sprintf ( ...$sql ) : $sql );
		
		if ( strpos ( $sql, ':' ) !== false )
		{
			preg_match_all ( $this -> pattern, $sql, $matches );
			
			$this -> matches = $matches[1];
			
			$sql = strtr ( $sql, array_fill_keys ( $this -> matches, '?' ) );
		}
		
		return $sql;
	}
	
	/*
		- Реформирование данных в массиве по найденным placeholders
	*/
	public function executeHolders( array $execute ): array
	{
		$new = [];
		
		foreach ( $this -> matches as $plaseholder )
		{
			if ( $plaseholder === '?' )
			{
				$new[] = array_shift ( $execute );
			}
			else
			{
				if ( isset ( $new[$plaseholder] ) )
				{
					$new[] = $new[$plaseholder];
				}
				else
				{			
					$new[$plaseholder] = $execute[$plaseholder] ?? null;
					
					unset ( $execute[$plaseholder] );
				}
			}
		}
		
		return $new ?? $execute;
	}
}