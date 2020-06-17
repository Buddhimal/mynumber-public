<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class MN_Loader extends CI_Loader
{

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Entity Loader
	 *
	 * Loads and instantiates entities.
	 * Designed to be called from application controllers.
	 *
	 * @param	mixed	$entityname	Library name
	 * @param	array	$params		Optional parameters to pass to the library class constructor
	 * @return	object
	 */
	public function entity($entityname, $params = NULL , $name = '')
	{
		if (empty($name))
		{
			$name = $model;
		}

		$CI =& get_instance();
		if (isset($CI->$name))
		{
			throw new RuntimeException('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		$entityname = ucfirst($entityname);
		include(APPPATH. 'entities' . DIRECTORY_SEPARATOR . $entityname . '.php');

		$entityname = new $entityname($params);
		$CI->$name = $entityname;
		log_message('info', 'Entity "'.get_class($entityname).'" initialized');
		return $this;
	}

}