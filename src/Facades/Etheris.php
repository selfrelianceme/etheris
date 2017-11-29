<?php 
namespace Selfreliance\Etheris\Facades;  

use Illuminate\Support\Facades\Facade;  

class Etheris extends Facade 
{
	protected static function getFacadeAccessor() { 
		return 'etheris';
	}
}
