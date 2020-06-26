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

use Eventum\Markdown\Markdown;
use Generator;

/**
 * @group db
 */
class MarkdownTest extends TestCase
{
    /** @var Markdown */
    private $renderer;

    private function getRenderer(): Markdown
    {
        static $renderer;

        return $renderer ?: $renderer = new Markdown();
    }

    public function setUp(): void
    {
        $this->renderer = $this->getRenderer();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMarkdown(string $input, string $expected): void
    {
        $rendered = $this->renderer->render($input);

        // XXX: strip newlines, somewhy tests on travis produce different newline placements
        // https://travis-ci.org/glensc/eventum/jobs/521628232
        if (getenv('TRAVIS')) {
            $expected = str_replace("\n", '', $expected);
            $rendered = str_replace("\n", '', $rendered);
        }

        $this->assertEquals($expected, $rendered);
    }

    public function dataProvider(): Generator
    {
        $testNames = [
            'autolink',
            'h5-details',
            'headers',
            'inline',
            'linkrefs',
            'script',
            'table',
            'tasklist',
            'userhandle',
        ];

        foreach ($testNames as $testName) {
            yield $testName => [
                $this->readDataFile("markdown/$testName.md"),
                $this->readDataFile("markdown/$testName.html"),
            ];
        }
    }
}
