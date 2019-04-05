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

namespace Eventum\Test;

use Eventum\Extension\ExtensionManager;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Create ExtensionManager with given config
     *
     * @return ExtensionManager
     */
    protected function getExtensionManager($config): ExtensionManager
    {
        /** @var ExtensionManager $stub */
        $stub = $this->getMockBuilder(ExtensionManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getExtensionFiles'])
            ->getMock();

        $stub->method('getExtensionFiles')
            ->willReturn($config);

        // as ->getMock() calls original constructor before method mocks is setup
        // we disabled original constructor and call it out now.
        $stub->__construct();

        return $stub;
    }

    protected function getDataFile($fileName): string
    {
        $dataFile = __DIR__ . '/data/' . $fileName;
        $this->assertFileExists($dataFile);

        return $dataFile;
    }

    /**
     * Read file from tests/data directory.
     *
     * @param string $filename
     * @return string
     */
    protected function readDataFile($filename): string
    {
        return $this->readFile($this->getDataFile($filename));
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function readFile($filename): string
    {
        $this->assertFileExists($filename);
        $content = file_get_contents($filename);
        $this->assertNotEmpty($content);

        return $content;
    }
}
