<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace wardormeur\anoncache\acp;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $request, $template, $user;

		$user->add_lang('acp/common');
		$this->tpl_name = 'demo_body';
		$this->page_title = $user->lang('ACP_DEMO_TITLE');
		add_form_key('wardormeur/anoncache');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('wardormeur/anoncache'))
			{
				trigger_error('FORM_INVALID');
			}

      $fileSystem = new Filesystem();

      try {
          $fileSystem->appendToFile($this->get('kernel')->getProjectDir().'/.htaccess', '#test');
      } catch (IOExceptionInterface $exception) {
          echo "An error occurred while creating your directory at ".$exception->getPath();
      }

			$config->set('acme_demo_goodbye', $request->variable('acme_demo_goodbye', 0));

			trigger_error($user->lang('ACP_DEMO_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'ACME_DEMO_GOODBYE'		=> $config['acme_demo_goodbye'],
		));
	}
}
