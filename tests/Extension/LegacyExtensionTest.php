<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Test\Extension;

use Eventum\Extension\ExtensionManager;
use Eventum\Test\TestCase;
use Group;
use ProjectSeeder;
use Workflow;

/**
 * @group db
 */
class LegacyExtensionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // ExtensionManager needs to be booted
        // for the getBackend methods to succeed
        ExtensionManager::getManager();
    }

    public function testGetWorkflow(): void
    {
        $prj_id = ProjectSeeder::DEFAULT_PROJECT_ID;
        $backend = Workflow::_getBackend($prj_id);
        $this->assertNotNull($backend);
    }
}
