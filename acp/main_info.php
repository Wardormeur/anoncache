<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace wardormeur\anoncache\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\wardormeur\anoncache\acp\main_module',
			'title'		=> 'ACP_DEMO_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_DEMO',
					'auth'	=> 'ext_wardormeur/anoncache && acl_a_board',
					'cat'	=> array('ACP_DEMO_TITLE')
				),
			),
		);
	}
}
