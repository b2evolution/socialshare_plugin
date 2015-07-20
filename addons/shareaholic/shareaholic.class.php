<?php

class socialshare_shareaholic extends pluginAddOn
{
	var $addon_name = 'shareaholic';

	/** 
	 * Get current collection settings
	 */
	static function get_coll_setting_definitions()
	{
		return array(
			'shareaholic_site_id' => array(
				'label' => T_('Site ID'),
				'size' => 70,
				'defaultvalue' => '',
				'note' => T_('The ID that you get from your social sharing service.'),
			),
			'shareaholic_applocation_app_id' => array(
				'label' => T_('Location APP ID'),
				'size' => 70,
				'defaultvalue' => '',
				'note' => T_('The Id of the location created for your site in the Shareaholic\'s Dashboard. See documentation for details.'),
			),
		);
	}

	/** Misc methods **/

	/**
	 * Inserts required html markup
	 */
	//TODO: allow per post-type inclusion
	function insert_code_block( & $params )
	{
		$content = & $params['data'];
		$item = & $params['Item'];
		
		$content .= "\n"
					.'<div class="shareaholic-canvas" data-app="share_buttons" data-app-id="' . $this->coll_settings['shareaholic_applocation_app_id'] . '"></div>'
					."\n";
	}

	/** Plugin HOOKS **/

	function SkinBeginHtmlHead( & $params )
	{
		global $Blog;

		$script = "
//<![CDATA[
  (function() {
    var shr = document.createElement('script');
    shr.setAttribute('data-cfasync', 'false');
    shr.src = '//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js';
    shr.type = 'text/javascript'; shr.async = 'true';
    shr.onload = shr.onreadystatechange = function() {
      var rs = this.readyState;
      if (rs && rs != 'complete' && rs != 'loaded') return;
      var site_id = '".$this->plugin->get_coll_setting( 'shareaholic_site_id', $Blog )."';
      try { Shareaholic.init(site_id); } catch (e) {}
    };
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(shr, s);
  })();
//]]>";

			add_js_headline($script);
	}

	function RenderItemAsHtml( & $params )
	{
		if( ! empty($this->coll_settings['shareaholic_applocation_app_id']) )
		{
			$this->insert_code_block( $params );
			return true;	
		}
		else
		{ // Unknown call, Don't render this case
			return;
		}
		
	}
}