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
	protected $defaultPingServices;

	public function setUp()
	{
		$this->defaultPingServices = BackendModel::getModuleSetting('core', 'ping_services');
	}

	public function tearDown()
	{
		BackendModel::setModuleSetting('core', 'ping_services', $this->defaultPingServices);
	}


	// @todo deze methods ook nog alfabetisch gooien.
	// ping
	// pingService
	// getPingServices
	// refreshPingServices
}
