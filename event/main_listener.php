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

use \GuzzleHttp\Psr7;
use \GuzzleHttp\Psr7\Request;
use \GuzzleHttp\Psr7\Uri;
use \GuzzleHttp\Promise;
use \GuzzleHttp\Client;
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

	/* @var \phpbb\driver\factory*/
	protected $db;

	/* @var sessionId*/
	protected $sessionId;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\auth\auth auth object
	* @param \phpbb\db\driver\factory dbal.conn object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\auth\auth $auth, \phpbb\db\driver\factory $db)
	{
		$this->helper = $helper;
		$this->template = $template;
    $this->auth = $auth;
    $this->db = $db;
    // $userdata = $this->auth->obtain_user_data(1);
    // $this->auth->acl($userdata);
    $session_rows = $this->db->sql_query('SELECT session_id FROM ' . SESSIONS_TABLE . ' WHERE session_user_id = 1 LIMIT 1');
    $this->sessionId = $session_rows->fetch_assoc()['session_id'];
    error_log('Extension constructor');
    error_log($this->sessionId);
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
    error_log('File refresh');
    $forumIndex = $event['data']['forum_id'];
    // TODO : function refreshPath(path) for index, forum and topic
    // or fn(forum, topic) and buildUrl
    $client = new Client();
    $indexUri = (new Uri('localhost:80/index.php'))->withQuery(Psr7\build_query(['sid' => $this->sessionId]));
    $requestIndex = new Request('GET', $indexUri);
    $promises = []; 
    $promises[] = $client->sendAsync($requestIndex)->then(function ($response) {
      $fileSystem = new Filesystem();
      try {
        return $fileSystem->dumpFile($phpbb_root_path.'cache/anoncache/index.html', $response->getBody());
      } catch (IOExceptionInterface $exception) {
        echo "An error occurred while creating your directory at ".$exception->getPath();
      }
    });
    // TODO : update parent forums views
    // Update corresponding viewforum
    $forumUri = (new Uri('localhost:80/viewforum.php'))->withQuery(Psr7\build_query(['f' => $forumIndex, 'sid' => $this->sessionId]));
    $requestForum = new Request('GET', $forumUri);
    $promises[] = $client->sendAsync($requestForum)->then(function ($response) use ($forumIndex) {
      $fileSystem = new Filesystem();
      try {
        return $fileSystem->dumpFile($phpbb_root_path.'cache/anoncache/viewforum/'.$forumIndex.'.html', $response->getBody());
      } catch (IOExceptionInterface $exception) {
        echo "An error occurred while creating your directory at ".$exception->getPath();
      }
    });
    Promise\all($promises)->then(function ($responses) {
      error_log(print_r($responses, true));
    })->wait();
  }
}
