<?php

namespace Aero\Low\Lerma;

use Aero\Supports\Lerma;

trait Fetches
{
	/*
		- Стиль возвращаемого результата с одной строки
		- fetch_style - Идентификатор выбираемого стиля. Default Lerma::FETCH_NUM
		- fetch_argument - атрибут для совершения действий над данными
	*/
	public function fetch( int $fetch_style = Lerma::FETCH_NUM, $fetch_argument = null )
	{
		switch ( $fetch_style )
		{
			/*
				-
			*/
			case Lerma::FETCH_NUM:
				return $this -> driver -> fetch( Lerma::FETCH_NUM );
			break;

			/*
				-
			*/
			case Lerma::FETCH_ASSOC:
				return $this -> driver -> fetch( Lerma::FETCH_ASSOC );
			break;
			
			/*
				-
			*/
			case Lerma::FETCH_FIELD:
				if ( array_key_exists ( 0, ( $info = $this -> driver -> fetch( Lerma::FETCH_FIELD ) ) ) )
				{
					return null;
				}
				
				return ( $fetch_argument === null ? $info : $info[$fetch_argument] );
			break;
			
			/*
				-
			*/
			case Lerma::FETCH_OBJ:
				return $this -> driver -> fetch( Lerma::FETCH_OBJ );
			break;
			
			/*
				-
			*/
			case Lerma::FETCH_BIND:

			/*
				-
			*/
			case Lerma::FETCH_BIND | Lerma::FETCH_COLUMN:
				if ( !$this -> bind() -> fetch( Lerma::FETCH_BIND ) )
				{
					self::instance() -> driver -> isError( $this -> statement );

					return $this -> bind_result = false;
				}

				if ( $fetch_style === ( Lerma::FETCH_BIND | Lerma::FETCH_COLUMN ) )
				{
					if ( $this -> countColumn() !== 1 )
					{
						throw new Error( 'Требуется выбрать только одну колонку' );
					}

					return $this -> bind_result[0];
				}

				return $this -> bind_result;
			break;

			/*
				-
			*/
			case Lerma::FETCH_COLUMN:
				if ( $this -> countColumn() !== 1 )
				{
					throw new Error( 'Требуется выбрать только одну колонку' );
				}

				return $this -> driver -> fetch( Lerma::FETCH_NUM )[0];
			break;

			/*
				-
			*/
			case Lerma::FETCH_KEY_PAIR: # column1 => column2
				if ( $this -> countColumn() !== 2 )
				{
					throw new Error( 'Требуется выбрать только две колонки' );
				}

				if ( ( $items = $this -> driver -> fetch( Lerma::FETCH_NUM ) ) === null )
				{
					return null;
				}

				return [ $items[0] => $items[1] ];
			break;

			/*
				-
			*/
			case Lerma::FETCH_FUNC:
				if ( !is_callable ( $fetch_argument ) )
				{
					throw new Error( 'Invalid argument2 is not type callable' );
				}

				if ( ( $items = $this -> driver -> fetch( Lerma::FETCH_NUM ) ) === null )
				{
					return null;
				}

				return $fetch_argument( ...$items );
			break;

			/*
				-
			*/
			case Lerma::FETCH_CLASS:

			/*
				-
			*/
			case Lerma::FETCH_CLASSTYPE:
				/* if ( !is_string ( $fetch_argument ) && Lerma::FETCH_CLASS === $fetch_style )
				{
					throw new Error( 'Invalid argument2 is not type string' );
				}
				elseif ( Lerma::FETCH_CLASSTYPE === $fetch_style && $this -> driver -> countColumn() < 2 )
				{
					throw new Error( 'Допустимое кол - во выбраных колонок: не менее двух' );
				}

				if ( ( $items = $this -> driver -> fetch( Lerma::FETCH_ASSOC ) ) === null )
				{
					return null;
				}

				$RefClass = ( new \ReflectionClass( ( Lerma::FETCH_CLASSTYPE === $fetch_style ?
					array_shift ( $items ) : $fetch_argument ) ) ) -> newInstanceWithoutConstructor();

				foreach ( $items AS $name => $item )
				{
					$RefClass -> $name = $item;
				}

				$RefClass -> __construct(); #&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

				return $RefClass; */
				throw new Error( 'Test...' );
			break;
			default:
				throw new Error( sprintf ( 'Invalid fetch_style %s is not switch', $fetch_style ) );
		}
	}

	/*
		- Стиль возвращаемого результата со всех строк
		- fetch_style - Идентификатор выбираемого стиля. Default Lerma::FETCH_NUM
		- fetch_argument - атрибут для совершения действий над данными
	*/
	public function fetchAll( int $fetch_style = Lerma::FETCH_NUM, $fetch_argument = null ): array
	{
		switch ( $fetch_style )
		{
			/*
				-
			*/
			case Lerma::FETCH_NUM:
				return $this -> driver -> fetchAll( Lerma::FETCH_NUM );
			break;

			/*
				-
			*/
			case Lerma::FETCH_ASSOC:
				return $this -> driver -> fetchAll( Lerma::FETCH_ASSOC );
			break;
			
			/*
				-
			*/
			case Lerma::FETCH_FIELD:
				$info = $this -> driver -> fetchAll( Lerma::FETCH_FIELD );
				
				if ( $fetch_argument === null )
				{
					return $info;
				}
				
				return array_column ( $info, $fetch_argument );
			break;
			
			/*
				-
			*/
			case Lerma::FETCH_OBJ:

			/*
				-
			*/
			case Lerma::FETCH_COLUMN:

			/*
				-
			*/
			case Lerma::FETCH_FUNC:

			/*
				-
			*/
			case Lerma::FETCH_CLASS:

			/*
				-
			*/
			case Lerma::FETCH_CLASSTYPE:
				$all = [];

				while ( ( $res = $this -> fetch( $fetch_style, $fetch_argument ) ) !== null ) { $all[] = $res; }

				return $all;
			break;

			/*
				-
			*/
			case Lerma::FETCH_KEY_PAIR:

			/*
				-
			*/
			case Lerma::FETCH_KEY_PAIR | Lerma::FETCH_NAMED:
				if ( $this -> countColumn() !== 2 )
				{
					throw new Error( 'Требуется выбрать только две колонки' );
				}

				$all = [];

				while ( $num = $this -> driver -> fetch( Lerma::FETCH_NUM ) )
				{
					if ( $fetch_style === ( Lerma::FETCH_KEY_PAIR | Lerma::FETCH_NAMED ) && isset ( $all[$num[0]] ) )
					{
						if ( is_array ( $all[$num[0]] ) )
						{
							$all[$num[0]][] = $num[1];
						}
						else
						{
							$all[$num[0]] = [ $all[$num[0]], $num[1] ];
						}
					}
					else
					{
						$all[$num[0]] = $num[1];
					}
				}

				return $all;
			break;

			/*
				-
			*/
			case Lerma::FETCH_UNIQUE:

			/*
				-
			*/
			case Lerma::FETCH_CLASSTYPE | Lerma::FETCH_UNIQUE:
				if ( $this -> countColumn() < 2 )
				{
					throw new Error( 'Допустимое кол - во выбраных колонок не менее двух' );
				}

				$all = [];

				foreach ( $this -> driver -> fetchAll( Lerma::FETCH_ASSOC ) AS $items )
				{
					if ( ( Lerma::FETCH_CLASSTYPE | Lerma::FETCH_UNIQUE ) === $fetch_style )
					{
						$class = array_shift ( $items );

						$RefClass = ( $c = new \ReflectionClass( $class ) ) -> newInstanceWithoutConstructor();

						foreach ( $items AS $name => $item )
						{
							$RefClass -> $name = $item;
						}

						$RefClass -> __construct();

						$all[( $fetch_argument === true ? $c -> getShortName() : $class )] = $RefClass;
					}
					else
					{
						$all[array_shift ( $items )] = $items;
					}
				}

				return $all;
			break;

			/*
				-
			*/
			case Lerma::FETCH_GROUP:
				if ( $this -> countColumn() < 2 )
				{
					throw new Error( 'Допустимое кол - во выбраных колонок не менее двух' );
				}

				$all = [];

				foreach ( $this -> driver -> fetchAll( Lerma::FETCH_ASSOC ) AS $s )
				{
					$all[array_shift ( $s )][] = $s;
				}

				return $all;
			break;

			/*
				-
			*/
			case Lerma::FETCH_GROUP | Lerma::FETCH_COLUMN:
				if ( $this -> driver -> countColumn() !== 2 )
				{
					throw new Error( 'Требуется выбрать только две колонки' );
				}

				$all = [];

				foreach ( $this -> driver -> fetchAll( Lerma::FETCH_NUM ) AS $s )
				{
					$all[array_shift ( $s )][] = $s[0];
				}

				return $all;
			break;
			default:
				throw new Error( sprintf ( 'Invalid fetch_style %s is not switch', $fetch_style ) );
		}
	}
}