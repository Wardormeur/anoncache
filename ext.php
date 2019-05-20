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
	* Overwrite enable_step to setup static directory 
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
        $fileSystem = new Filesystem();
        // TODO : shared variable for cache directory
        // TODO : createDirectory fn
        // Create the caching directory
        $fileSystem->mkdir($phpbb_root_path.'cache/anoncache', 0755);
        // NOTE: this might be an issue..
        $fileSystem->chown($phpbb_root_path.'cache/anoncache', 'www-data', true);
        // Create the viewforum directory
        $fileSystem->mkdir($phpbb_root_path.'cache/anoncache/viewforum', 0755);
        $fileSystem->chown($phpbb_root_path.'cache/anoncache/viewforum', 'www-data', true);
        // Set the .htaccess to read for the caching directory
        $cacheHtaccess= "<Files *>
	Order Allow,Deny
	Allow from All
</Files>";


        $fileSystem->dumpFile($phpbb_root_path.'cache/anoncache/.htaccess', $cacheHtaccess);
				return 'setup_apache';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}

  /**
	* Remove static directory 
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
        $fileSystem = new Filesystem();
        $fileSystem->remove($phpbb_root_path.'cache/anoncache');
				return 'setup_apache';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}

}
