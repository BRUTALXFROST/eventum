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

namespace Eventum\Controller\Helper;

use Eventum\Db\Doctrine;
use Eventum\Model\Entity\UserPreference;
use Eventum\Model\Repository\CustomFieldRepository;

class RepositoryHelper
{
    /** @var int */
    private $usr_id;

    public function getUserPreferences(): UserPreference
    {
        return Doctrine::getUserPreferenceRepository()->findOrCreate($this->usr_id);
    }

    public function getCustomFieldRepository(): CustomFieldRepository
    {
        return Doctrine::getCustomFieldRepository();
    }
}
