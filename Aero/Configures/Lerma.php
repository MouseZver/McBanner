<?php

namespace Aero\Configures;

class Lerma
{
	const USER = 'root';
	const PASSWORD = '';
	const EXT_DRIVER_PATH = '/ext/%s/%s.php';
	
	# Назначение драйвера для подключения базы данных
	public $driver = 'mysqli';
	
	private $parameters = [
		# Параметры для драйвера mysqli
		'mysqli' => [
			'host' => '127.0.0.1',
			'user' => self::USER,
			'password' => self::PASSWORD,
			'dbname' => 'git',
			'port' => 3306
		],
	];
	
	public function __get( $name )
	{
		return $this -> parameters[$name] ?? null;
	}
}