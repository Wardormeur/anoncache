<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace wardormeur\anoncache\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
			'core.page_header'	=> 'add_page_header_link',
      'core.submit_post_end' => 'refresh_static',
		);
	}

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\auth\auth*/
	protected $auth;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\auth\auth auth object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\auth\auth $auth)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->auth = $auth;
    $userdata = $this->auth->obtain_user_data(1);
    // $this->auth->acl($userdata);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'wardormeur/anoncache',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_page_header_link($event)
	{
		$this->template->assign_vars(array(
			'U_DEMO_PAGE'	=> $this->helper->route('acme_demo_controller', array('name' => 'world')),
		));
  }

  public function refresh_static($event)
  {
    global $phpbb_root_path;
    error_log(print_r($event['data']['forum_id'], true));
    $forumIndex = $event['data']['forum_id'];
    // TODO : function refreshPath(path) for index, forum and topic
    // or fn(forum, topic) and buildUrl
    $client = new \GuzzleHttp\Client();
    $request = new \GuzzleHttp\Psr7\Request('GET', 'localhost:80/index.php');
    $promises = []; 
    $promises[] = $client->sendAsync($request)->then(function ($response) {
      $fileSystem = new Filesystem();
      try {
        return $fileSystem->dumpFile($phpbb_root_path.'cache/anoncache/index.html', $response->getBody());
      } catch (IOExceptionInterface $exception) {
        echo "An error occurred while creating your directory at ".$exception->getPath();
      }
    });
    // TODO : update parent forums views
    // Update corresponding viewforum
    $promises[] = $client->sendAsync($request)->then(function ($response) use ($forumIndex) {
      $fileSystem = new Filesystem();
      try {
        return $fileSystem->dumpFile($phpbb_root_path.'cache/anoncache/viewforum/'.$forumIndex.'.html', $response->getBody());
      } catch (IOExceptionInterface $exception) {
        echo "An error occurred while creating your directory at ".$exception->getPath();
      }
    });
    \GuzzleHttp\Promise\all($promises)->then(function ($responses) {
      error_log(print_r($responses, true));
    })->wait();
    //$promise->wait();
  }
}
