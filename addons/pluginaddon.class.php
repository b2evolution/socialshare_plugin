<?php

class pluginAddOn
{
	function __construct( $plugin )
	{
		$this->plugin = $plugin;
	}

	function extract_addon_name() 
	{
		$name = explode('_', get_class($this));
		return $name[1];
	}

	function get_addon_url()
	{
		return $this->plugin->get_plugin_url() . 'addons/' . $this->extract_addon_name(); 
	}

	/**
	 * To be developed
	 */
	/*function ColorboxInit( & $params )
	{
		$publisher_id = $this->plugin->Settings->get('publisher_id');

		require_css( $this->plugin->get_plugin_url() . '/socialmodal.css', true );

		require_js( '#addthis#', 'rsc_url', true );
		require_js( $this->get_addon_url() . '/addthis_colorbox.js', true );

		$content = & $params['data'];
		$content = ',
						onOpen:function(){ openCallBack("'.$publisher_id.'") }';

		return true;
	}*/
}