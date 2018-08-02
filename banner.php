<?php

error_reporting ( E_ALL );

use Aero\Supports\Lerma;

require __DIR__ . '/autoload.php';

$file = '/banner.jpg';

if ( !is_file ( __DIR__ . $file ) || stat ( __DIR__ . $file )['mtime'] < strtotime ( '-3min' ) )
{
	$values = [
		0 => 'В данный момент на сервере нет лучших игроков',
		1 => 'На сервере играет {online} лучший игрок',
		2 => 'На сервере играют {online} лучших игрока',
		3 => 'На сервере играют {online} лучших игрока',
		4 => 'На сервере играют {online} лучших игрока',
		'others' => 'На сервере играют {online} лучших игроков',
	];
	
	( new Aero\Images\McBanner( $values, [
		'fontname' => 'lobster',
		'online' => Lerma :: query( 'SELECT COUNT(id) FROM authme WHERE isLogged > 0' ) -> fetch( Lerma :: FETCH_COLUMN ),
	] ) ) -> execute();
}

printf ( '<img src="%s">', $file );
