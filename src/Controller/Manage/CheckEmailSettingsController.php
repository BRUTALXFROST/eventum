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

namespace Eventum\Controller\Manage;

use Eventum\Mail\Imap\ImapConnection;

class CheckEmailSettingsController extends ManageBaseController
{
    /** @var string */
    protected $tpl_name = 'get_emails_ajax.tpl.html';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultAction(): void
    {
        $post = $this->getRequest()->request;
        $hostname = $post->get('hostname');

        if (!$this->resolveAddress($hostname)) {
            $this->tpl->assign('error', 'hostname_resolv_error');

            return;
        }

        $account = [
            'ema_hostname' => $hostname,
            'ema_port' => $post->get('port'),
            'ema_type' => $post->get('type'),
            'ema_folder' => $post->get('folder'),
            'ema_username' => $post->get('username'),
            'ema_password' => $post->get('password'),
        ];

        $connection = new ImapConnection($account);
        if (!$connection->isConnected()) {
            $this->tpl->assign('error', 'could_not_connect');

            return;
        }

        $this->tpl->assign('error', 'no_error');
    }

    /**
     * check if the hostname is just an IP based one
     *
     * @param $hostname
     * @return bool
     */
    private function resolveAddress($hostname)
    {
        $regex = "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/";

        return !(!preg_match($regex, $hostname) && gethostbyname($hostname) == $hostname);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplate(): void
    {
    }
}
