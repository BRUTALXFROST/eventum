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

namespace Eventum\Test\Date;

use Date_Helper;
use Eventum\Test\TestCase;
use Prefs;

/**
 * DateHelper tests involving user (using database)
 *
 * @group date
 * @group db
 */
class DateHelperUserTest extends TestCase
{
    /**
     * timezone used for preferred user timezone tests
     */
    private const USER_TIMEZONE = 'Europe/Tallinn';
    private const ADMIN_TIMEZONE = 'UTC';

    public static function setUpBeforeClass(): void
    {
        self::setTimezone(APP_ADMIN_USER_ID, self::USER_TIMEZONE);
        self::setTimezone(APP_SYSTEM_USER_ID, self::ADMIN_TIMEZONE);
    }

    private static function setTimezone($usr_id, $timezone): void
    {
        $prefs = Prefs::get($usr_id);
        $prefs['timezone'] = $timezone;
        Prefs::set($usr_id, $prefs);
        // this will force db refetch
        Prefs::get($usr_id, true);
    }

    /**
     * @covers Date_Helper::getTimezoneShortNameByUser
     */
    public function testGetTimezoneShortNameByUser(): void
    {
        $res = Date_Helper::getTimezoneShortNameByUser(APP_SYSTEM_USER_ID);
        $this->assertEquals('UTC', $res);

        $res = Date_Helper::getTimezoneShortNameByUser(APP_ADMIN_USER_ID);
        $this->assertRegExp('/EET|EEST/', $res);
    }

    /**
     * @covers Date_Helper::getPreferredTimezone
     */
    public function testGetPreferredTimezone(): void
    {
        $res = Date_Helper::getPreferredTimezone();
        $this->assertEquals('UTC', $res);

        $res = Date_Helper::getPreferredTimezone(APP_SYSTEM_USER_ID);
        $this->assertEquals('UTC', $res);

        $res = Date_Helper::getPreferredTimezone(APP_ADMIN_USER_ID);
        $this->assertEquals(self::USER_TIMEZONE, $res);
    }
}
