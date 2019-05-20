<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace wardormeur\anoncache\tests\functional;

/**
* @group functional
*/
class demo_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('wardormeur/anoncache');
	}

	public function test_page_cached()
	{
		$crawler = self::request('GET', 'app.php');
		$this->assertContains('My Board', $crawler->filter('h1')->text());

		/*$this->add_lang_ext('wardormeur/anoncache', 'common');
		$this->assertContains($this->lang('DEMO_HELLO', 'acme'), $crawler->filter('h2')->text());
		$this->assertNotContains($this->lang('DEMO_GOODBYE', 'acme'), $crawler->filter('h2')->text());

    $this->assertNotContainsLang('ACP_DEMO', $crawler->filter('h2')->text());*/
	}

	public function test_demo_world()
	{
		$crawler = self::request('GET', 'app.php/demo/world');
		$this->assertNotContains('acme', $crawler->filter('h2')->text());
		$this->assertContains('world', $crawler->filter('h2')->text());
	}
}
