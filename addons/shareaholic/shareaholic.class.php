<?php

class socialshare_shareaholic extends pluginAddOn
{
	var $addon_name = 'shareaholic';

	// Some services has been disabled because there is no fa-icon available for them
	static $custom_available_services = array(
			'delicious' 	=> array('code_ID' => 2, 	'label' => 'Delicious'),
			'digg' 			=> array('code_ID' => 3, 	'label' => 'Digg'),
			'facebook' 		=> array('code_ID' => 5, 	'label' => 'Facebook'),
			'twitter' 		=> array('code_ID' => 7, 	'label' => 'Twitter'),
			//'technorati' 	=> array('code_ID' => 10, 	'label' => 'Technorati'),
			'stumbleupon' 	=> array('code_ID' => 38, 	'label' => 'StumbleUpon'),
			'myspace' 		=> array('code_ID' => 39, 	'label' => 'MySpace'),
			//'reddit' 		=> array('code_ID' => 40, 	'label' => 'Reddit'),
			'tumblr' 		=> array('code_ID' => 78, 	'label' => 'Tumblr'),
			//'livejournal' 	=> array('code_ID' => 79, 	'label' => 'LiveJournal'),
			'linkedin' 		=> array('code_ID' => 88, 	'label' => 'LinkedIn'),
			//'evernote' 		=> array('code_ID' => 191, 	'label' => 'Evernote'),
			'google_plus' 	=> array('code_ID' => 304, 	'label' => 'Google Plus +1', 'fa-icon' => 'google-plus'),
			'pinterest' 	=> array('code_ID' => 309, 	'label' => 'Pinterest'),
			'email'			=> array('code_ID' => 313, 	'label' => 'Email This', 'fa-icon' => 'envelope'),
		);

	/** 
	 * Get current collection settings
	 */
	static function get_coll_setting_definitions()
	{
		$custom_available_services_options = array();

		foreach( self::$custom_available_services as $key => $available_service )
		{
			$custom_available_services_options[$key] = $available_service['label'];
		}

		return array(
			'shareaholic_begin_fieldset' => array(
				'label' => 'Shareaholic settings',
				'layout' => 'begin_fieldset',
			),
			'shareaholic_site_id' => array(
				'label' => T_('Shareaholic Site ID'),
				'size' => 70,
				'defaultvalue' => '',
				'note' => T_('The ID that you get from your social sharing service.'),
			),
			'shareaholic_modeofuse' => array(
				'label' => T_('Mode of use'),
				'type' => 'radio',
				'options' => array( array('default', T_('Default')), array('custom', T_('Custom')) ),
				'defaultvalue' => 'default',
				'field_lines' => true,
				'note' => T_('Default: use the sharing bar as configured on the Shareoholic\'s Dashboard. Custom: customize your sharing bar according to the paramterers below.'),
			),
			'shareaholic_custom_services' => array(
				'label' => T_('Services'),
				'type' => 'select',
				'multiple' => true,
				'options' => $custom_available_services_options,
				'defaultvalue' => 'default',
				'field_lines' => true,
				'note' => T_('Default: use the sharing bar as configured on the Shareoholic\'s Dashboard. Custom: customize your sharing bar according to the paramterers below.'),
			),

			'shareaholic_end_fieldset' => array(
				'layout' => 'end_fieldset',
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
		global $Blog;

		$content = & $params['data'];
		$item = & $params['Item'];

		//echo '<pre>'; print_r($item); die;

		$custom_services = $this->plugin->get_coll_setting( 'shareaholic_custom_services', $Blog );
		if ( is_array($custom_services) && ! empty($custom_services) )
		{
			$content .= "\n"
						.'<div class="shareaholic_container">';
			foreach( $custom_services as $service ) 
			{
				$icon = ( isset(self::$custom_available_services[$service]['fa-icon']) && ! empty(self::$custom_available_services[$service]['fa-icon']) ) ? self::$custom_available_services[$service]['fa-icon'] : $service;

				$api_url = 'http://www.shareaholic.com/api/share/?'
							.'v=1'
							.'&apitype=1'
							.'&apikey=' . $this->plugin->get_coll_setting( 'shareaholic_site_id', $Blog )
							.'&service='.self::$custom_available_services[$service]['code_ID']
							.'&title=' . urlencode($item->title)
							.'&link='. $item->get_permanent_url();
				
				$content .=  "\n" . '<div class="shareaholic_service_' . $service . '" onclick="javascript: open_window(\''.$api_url.'\');"><i class="fa fa-' . $icon . '"></i></div>' . "\n";
			}

			$content .= '</div>' . "\n";
		}
		
	}

	/** Plugin HOOKS **/

	function SkinBeginHtmlHead( & $params )
	{
		global $Blog;

		if( $this->plugin->get_coll_enabled_addon() )
		{
			if( $this->plugin->get_coll_setting( 'shareaholic_modeofuse', $Blog ) == 'default' )
			{
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
			elseif( $this->plugin->get_coll_setting( 'shareaholic_modeofuse', $Blog ) == 'custom' )
			{
				init_fontawesome_icons( 'fontawesome-glyphicons' );

				require_css( $this->get_addon_url().'/rsc/css/style.css', true );
				require_js( $this->plugin->get_plugin_url().'/rsc/js/socialshare.js', true );
				return true;
			}
		}
		else
		{ // Unknown call, Don't render this case
			return;
		}
	}

	function RenderItemAsHtml( & $params )
	{
		global $Blog;

		if( $this->plugin->get_coll_enabled_addon() )
		{
			if( $this->plugin->get_coll_setting( 'shareaholic_modeofuse', $Blog ) == 'custom' )
			{
				$this->insert_code_block( $params );
				return true;	
			}
		}
		else
		{ // Unknown call, Don't render this case
			return;
		}
		
	}
}