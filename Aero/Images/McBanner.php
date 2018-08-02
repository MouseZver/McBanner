<?php

namespace Aero\Images;

final class McBanner
{
	protected 
		$size = 600,
		$fontsize = 20,
		$fontname = 'arial',
		$backgroundImage = 'logo',
		$online = 0,
		$colortext = [ 255, 255, 255 ],
		$values;
	
	/*
		- Валидация
	*/
	public function __construct ( array ...$items )
	{
		foreach ( [ 'size', 'fontsize', 'fontname', 'backgroundImage', 'online', 'colortext' ] AS $key )
		{
			if ( isset ( $items[1][$key] ) )
			{
				$this -> $key = $items[1][$key];
			}
		}
		
		if ( !isset ( $items[0] ) )
		{
			throw new \Error ( 'Укажите текстовый набор в атрибутах конструктора.' );
		}
		
		$this -> values = $items[0];
	}
	
	/*
		- Исполнение
	*/
	public function execute( string $directory = null )
	{
		$logo = imagecreatefromjpeg ( __DIR__ . DIRECTORY_SEPARATOR . $this -> backgroundImage . '.jpg' );
		
		if ( ( $imagesx = imagesx ( $logo ) ) !== $this -> size )
		{
			$y = ceil ( ( $imagesy = imagesy ( $logo ) ) / ( $imagesx / $this -> size ) );
			
			$this -> im = imagecreatetruecolor ( $this -> size, $y );
			
			imagecopyresampled ( $this -> im, $logo, 0,0,0,0, $this -> size, $y, $imagesx, $imagesy );
		}
		else
		{
			$this -> im = $logo;
		}
		
		$string = strtr ( $this ->values[$this -> online] ?? $this -> values['others'], [ '{online}' => $this -> online ] );
		
		$font = __DIR__ . '/fonts/' . $this -> fontname . '.ttf';
		
		while ( true )
		{
			$box = imagettfbbox ( $this -> fontsize, 0, $font, $string );
			
			if ( ( $width = abs ( $box[4] - $box[0] ) ) < $this -> size )
			{
				break;
			}
			
			$this -> fontsize--;
		}
		
		imagettftext ( $this -> im, $this -> fontsize, 0, 
			( $this -> size / 2 - $width / 2 ), ( $y / 2 + abs ( $box[5] - $box[1] ) / 2 ), 
			imagecolorallocate ( $this -> im, ...$this -> colortext ), $font, $string );
		
		imagejpeg ( $this -> im, $directory ?? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'banner.jpg' );
		
		imagedestroy ( $this -> im );
	}
}
