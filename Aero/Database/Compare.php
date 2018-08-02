<?php

namespace Aero\Database;

use Aero;
use Throwable;
use Exception AS Error;
use Aero\Low\Lerma\
{
	Migrate,
	Fetches,
	Placeholders
};

class Compare extends MigrateStmt
{
	# Объект среды Lerma
	private static $instance;
	
	protected 
		$config, # Конфиг
		$drivers = []; # Объект подключенного драйвера
	
	use Migrate, Fetches, Placeholders;
	
	/*
		- Запуск ядра
	*/
	protected static function instance(): Compare
	{
		try
		{
			return self::$instance ?? ( self::$instance = ( new static ) -> IDrivers( new Aero\Configures\Lerma ) );
		}
		catch ( Throwable $t )
		{
			( new static ) -> exceptionIDriver( $t );
		}
	}
	
	/*
		- Подключение драйвера
	*/
	public static function app( callable $call )
	{
		$instance = static::instance();
		
		$driver = $instance -> dead() -> config -> driver;
		
		$call( $instance -> config );
		
		if ( $instance -> config -> {$instance -> config -> driver} === null )
		{
			throw new Error( 'None: ' . $instance -> config -> driver );
		}
		
		if ( !array_key_exists ( $instance -> config -> driver, $instance -> drivers ) )
		{
			return $instance -> IDrivers( $instance -> config );
		}
		
		return self::instance();
	}
	/*
		- Моем посуду
	*/
	protected function dead(): Compare
	{
		if ( $this -> statement !== null || $this -> query !== null )
		{
			$this -> driver -> close();
		}

		return $this;
	}
	
	/*
		- Выбор и загрузка драйвера для работы с базой данных
	*/
	protected function IDrivers( Aero\Configures\Lerma $lerma ): Compare
	{
		$this -> config = $lerma;
		
		$this -> drivers[$lerma -> driver] = require sprintf ( strtr ( __DIR__ . $lerma::EXT_DRIVER_PATH, '/', DIRECTORY_SEPARATOR ), $lerma -> driver, 'driver' );

		if ( !is_a ( $this -> driver, Aero\Interfaces\Lerma\IDrivers::class ) )
		{
			throw new Error( 'Загруженный драйвер не соответсвует требованиям интерфейсу IDrivers' );
		}
		
		return $this;
	}
	
	/*
		- output driver obj
	*/
	public function __get( $name )
	{
		if ( $name === 'driver' )
		{
			return $this -> drivers[$this -> config -> driver];
		}
		
		return null;
	}
}