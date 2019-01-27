<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace wardormeur\anoncache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
/**
* @ignore
*/

/**
 * Class ext
 *
 * It is recommended to remove this file from
 * an extension if it is not going to be used.
 */
class ext extends \phpbb\extension\base
{

  /**
	* Overwrite enable_step to setup apache configuration for index rewrite 
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	* @access public
	*/
  public function enable_step($old_state)
	{
    global $phpbb_root_path;
		switch ($old_state)
		{
			case '': 
        // TODO: fix ident in htaccess
        /*$apacheConfig = "<IfModule mod_rewrite.c>
                      RewriteEngine on
                      RewriteCond %{REQUEST_URI} ^/(index\.php)?$
                      RewriteCond %{HTTP_COOKIE} !phpbb3_.+=([^;]+)
                      RewriteCond index.html -f
                      RewriteRule .* /index.html [L,R,NC,END]
                  </IfModule>";


        $fileSystem = new Filesystem();
        $fileSystem->appendToFile($phpbb_root_path.'.htaccess', $apacheConfig);
        // TODO : reload apache
        */
				return 'setup_apache';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

  /**
	* Remove apache's configuration for mod_rewrite 
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	* @access public
	*/
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
        // TODO : remove apache conf
        // TODO : clean up temp files
				return 'setup_apache';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

}
