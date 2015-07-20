<?php

class socialshare_addthis extends pluginAddOn
{
	/** 
	 * Get current collection settings
	 */
	static function get_coll_setting_definitions()
	{
		return array(
			'addthis_publisher_id' => array(
				'label' => T_('Addthis PubID'),
				'size' => 70,
				'defaultvalue' => '',
				'note' => T_('The ID that you get from your social sharing service.'),
			),
		);
	}

	/** Misc methods **/

	/**
	 * Inserts required html markup
	 */
	function insert_code_block( & $params )
	{
		$content = & $params['data'];
		$item = & $params['Item'];

		//TODO: allow per post-type inclusion

		$content .=  "\n"
					.'<!-- Go to www.addthis.com/dashboard to customize your tools -->' . "\n"
					.'<div class="addthis_sharing_toolbox"></div>' . "\n";
	}

	
	/** Plugin HOOKS **/

	function SkinBeginHtmlHead( & $params )
	{
		require_js( '//s7.addthis.com/js/300/addthis_widget.js#pubid=' . $this->coll_settings['addthis_publisher_id'], 'rsc_url', true );
		return true;
	}

	function RenderItemAsHtml( & $params )
	{
		$this->insert_code_block( $params );
		return true;
	}
}