<?php 

class socialshare_plugin extends Plugin
{
	/**
	 * Variables below MUST be overriden by plugin implementations,
	 * either in the subclass declaration or in the subclass constructor.
	 */

	var $name;
	var $code = 'evo_socshr';
	var $priority = 50;
	var $version = '1.0';
	var $author = 'The b2evo Group';
	var $group = 'rendering';

	var $plugin;
	var $item_Blog;

	var $available_addons;
	var $enabled_addon;
	var $class_prefix = 'socialshare_';

	/**
	 * Init
	 */
	function PluginInit( & $params )
	{
		$this->name = T_( 'Social Share' );
		$this->short_desc = T_('Share contents to your favorite social networks.');
		$this->long_desc = T_('Share contents to your favorite social networks using different sharing services.');

		$this->available_addons = $this->get_available_addons(true); // Get the available addons and load their code
	}


	function get_available_addons( $load = false )
	{
		require_once('addons/pluginaddon.class.php');

		$available_addons = array();

		$dir = dirname(__FILE__) . '/addons';
		$dir_content = scandir($dir);
		
		foreach ( $dir_content as $element )
		{
			$subdir_name = $dir . '/' . $element;

			if ( is_dir($subdir_name) )
			{
				$filepath = $subdir_name . '/' . $element . '.class.php';
				if ( is_file($filepath) )
				{
					$available_addons[] = array($element, ucfirst($element));

					if ( $load )
					{
						require_once($filepath);
					}
				}
			}
		}

		return $available_addons;
	}

	
	function get_coll_setting_definitions( & $params )
	{
		$addons_settings = array();
		foreach ( $this->available_addons as $addon )
		{
			$classname = $this->class_prefix . $addon[0];

			$addons_settings = array_merge($classname::get_coll_setting_definitions(), $addons_settings);
		}

		$default_params = array_merge( $params, array(
				'default_comment_rendering' => 'never',
				'default_post_rendering' => 'opt-in'
			) );

		$plugin_settings = array(
							'enabled_addon' => array(
									'label' => T_('Your sharing service'),
									'type' => 'radio',
									'options' => $this->available_addons,
									'field_lines' => true,
									'note' => '',
								),
							);

		return array_merge( $plugin_settings, $addons_settings, parent::get_coll_setting_definitions( $default_params ) ); 
			
	}


	function call_method( $object, $method, & $params )
	{
		if( method_exists($object, $method) )
		{
			$object->$method( $params );
		}
	}


	function get_coll_enabled_addon()
	{
		if( $this->status != 'enabled' )
		{
			return false;
		}

		global $Blog;

		if ( isset( $Blog ) && $this->get_coll_setting( 'enabled_addon', $Blog ) )
		{
			if( $enabled_addon = $this->get_coll_setting( 'enabled_addon', $Blog ) )
			{
				$classname = $this->class_prefix . $enabled_addon;
				$this->enabled_addon = new $classname($this);

				return true;
			}
		}

		return false;
	}


	/** Plugin Hooks **/
	function SkinBeginHtmlHead( & $params )
	{
		if ( $this->get_coll_enabled_addon( $params ) )
		{ 	
			$this->call_method( $this->enabled_addon, 'SkinBeginHtmlHead', $params );
		}
	}

	function RenderItemAsHtml( & $params )
	{
		if ( $this->get_coll_enabled_addon( $params ) )
		{
			$this->call_method( $this->enabled_addon, 'RenderItemAsHtml', $params );
		}
	}

	function RenderItemAsXml ( & $params )
	{
		if ( $this->get_coll_enabled_addon( $params ) )
		{
			$this->call_method( $this->enabled_addon, 'RenderItemAsXml', $params );
		}
	}

	function ColorboxInit( & $params )
	{
		if ( $this->get_coll_enabled_addon( $params ) )
		{
			$this->call_method( $this->enabled_addon, 'ColorboxInit', $params );
		}
	}
}