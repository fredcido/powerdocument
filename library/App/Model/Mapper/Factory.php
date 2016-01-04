<?php

/** 
 * 
 */
class App_Model_Mapper_Factory
{
	/**
	 * 
	 * @var array
	 */
	protected static $_mappers = array();
	
	/**
	 * 
	 * @access 	public
	 * @param 	string 		$class
	 * @param 	string 		$module
	 * @throws 	Exception
	 */
	public static function get ( $class, $module = null )
	{
		$className 	= '';
		
		if ( !is_null($module) )
			$className .= ucfirst( $module ) . '_';
		
		$className .= 'Model_Mapper_' . implode('', array_map('ucfirst', explode('_', $class) ) );
		
		if ( !class_exists( $className ) ) 
			throw new Exception( 'class not found: ' . $class );
						
		if ( !in_array( $className, array_keys( self::$_mappers ) ) )
			self::$_mappers[$className] = new $className();
			
		return self::$_mappers[$className];
	} 
}