<?php

class socialshare_shareaholic extends pluginAddOn
{
	/** 
	 * Get current collection settings
	 */
	static function get_coll_setting_definitions()
	{
		return array(
			'shareaholic_site_id' => array(
				'label' => T_('Shareaholic Site ID'),
				'size' => 70,
				'defaultvalue' => '',
				'note' => T_('The ID that you get from your social sharing service.'),
			),
		);
	}

	/** Plugin HOOKS **/

	function SkinBeginHtmlHead( & $params )
	{
		if( $this->plugin->get_coll_enabled_addon() )
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

			return true;
		}
		else
		{ // Unknown call, Don't render this case
			return;
		}
	}
}