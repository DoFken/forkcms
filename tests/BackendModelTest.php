<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// needed for spoon
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)) . '/library');

require_once dirname(dirname(__FILE__)) . '/default_www/backend/core/engine/model.php';
require_once dirname(dirname(__FILE__)) . '/default_www/backend/core/engine/language.php';
require_once 'globals.php';
require_once 'spoon/spoon.php';
require_once 'PHPUnit/Framework/TestCase.php';

class BackendModelTest extends PHPUnit_Framework_TestCase
{
	protected $defaultPingServices, $defaultPublicKey, $defaultPrivateKey;

	public function setUp()
	{
		$this->defaultPingServices = BackendModel::getModuleSetting('core', 'ping_services');
		$this->defaultPublicKey = BackendModel::getModuleSetting('core', 'fork_api_public_key');
		$this->defaultPrivateKey = BackendModel::getModuleSetting('core', 'fork_api_private_key');
	}

	public function tearDown()
	{
		BackendModel::setModuleSetting('core', 'ping_services', $this->defaultPingServices);
		BackendModel::setModuleSetting('core', 'fork_api_public_key', $this->defaultPublicKey);
		BackendModel::setModuleSetting('core', 'fork_api_private_key', $this->defaultPrivateKey);
	}

	public function testRefreshPingServices()
	{
		// delete current list
		BackendModel::setModuleSetting('core', 'ping_services', array());

		// refresh services
		$pingServices = BackendModel::refreshPingServices();

		// check if the moduel setting was updated correctly
		$this->assertEquals($pingServices, BackendModel::getModuleSetting('core', 'ping_services'));

		// the new list should not be empty
		$this->assertArrayHasKey('date', $pingServices);
		$this->assertArrayHasKey('services', $pingServices);
		$this->assertArrayHasKey(0, $pingServices['services']);
	}

	public function testGetPingServices()
	{
		// reset ping services (including the date)
		$pingServices = array('date' => time());
		BackendModel::setModuleSetting('core', 'ping_services', $pingServices);

		// we expect the key 'services' to be added
		$pingServices['services'] = array();

		// test if we get the same results
		$this->assertEquals($pingServices, BackendModel::getPingServices());
	}

	public function testPingService()
	{
		$service = array(
			'url' => 'http://rpc.weblogs.com/RPC2',
			'port' => 80,
			'type' => 'extended'
		);

		// if we don't provide a page or feed url extended pings should return false
		$this->assertFalse(BackendModel::pingService($service, 'title', 'http://www.fork-cms.com'));

		// attempt to ping, expecting a true return (if all went fine)
		$this->assertTrue(
			BackendModel::pingService(
				$service,
				'Fork CMS',
				'http://www.fork-cms.com/blog',
				'http://www.fork-cms.com/blog',
				'blog'
			)
		);
	}
}
