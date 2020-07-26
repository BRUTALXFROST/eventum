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

namespace Eventum\Test\Mail;

use Eventum\Mail\MailStorage;
use Eventum\ServiceContainer;
use Eventum\Test\TestCase;
use Laminas\Mail;
use Laminas\Mail\Storage;
use Setup;
use Support;

/**
 * @group mail
 * @group imap
 */
class MailStorageTest extends TestCase
{
    /** @var array */
    private $account;

    public function setUp(): void
    {
        $setup = ServiceContainer::getConfig();

        if (!isset($setup['tests.imap-account'])) {
            $this->markTestSkipped("Define 'tests.imap-account' array in setup.php for testing");
        }

        /*
         * It should be something like:
          'tests.imap-account' => [
             'ema_hostname' => 'localhost',
             'ema_port' => 143,
             'ema_type' => 'imap/notls',
             'ema_folder' => 'INBOX',
             'ema_username' => '',
             'ema_password' => '',
           ]
        */
        $this->account = $setup['tests.imap-account'];
    }

    public function testNewMails(): void
    {
        $mbox = new MailStorage($this->account);
        $flags = [
            Storage::FLAG_UNSEEN,
        ];
        $count = $mbox->countMessages($flags);
        $this->assertEquals(0, $count);
    }

    public function testProcessMessages(): void
    {
        $mbox = new MailStorage($this->account);

        $maxMessage = $mbox->countMessages();
        $this->assertGreaterThan(0, $maxMessage);

        // not sure how to iterate messages over flags
        // as no way to set flags other than countMessages
        // but iterator uses count() without flags param
        foreach ($mbox->getMails() as $mail) {
            var_dump($mail);
            die;
            var_dump($mail->messageId);
//            var_export($mail->to);
//            isset($mail->cc) and var_export($mail->cc);
//            !empty($mail->fromaddress) and var_export($mail->fromaddress);
        }
    }

    public function testSearch(): void
    {
        $mbox = new MailStorage($this->account);

//        $message_id = '81421718b55935a2f5105705f8baf571@lookout.gemius.pl';
//        $message_id = 'fb9b070b03993cb452c2959a3a35781d@unix.gem.pl';
        $message_id = '<5d36173add8b60.67944236+8@origin.com>';
        $protocol = $mbox->getProtocol();
//        $protocol->select('INBOX.support');
        $response = $protocol->requestAndResponse('TEXT', [$protocol->escapeString($message_id)]);
//        $response = $protocol->requestAndResponse('TEXT', array($message_id));
        var_dump($response);

        // $matches = @imap_search($mbox, 'TEXT "' . $row['sup_message_id'] . '"');
    }

    public function testMessage(): void
    {
        $mbox = Support::connectEmailServer($this->account);
        $message1 = $this->readImapMessage($mbox, 1);

        /** @see Support::processMailMessage */
        $storage = new MailStorage($this->account);
        $message2 = $storage->getStorage()->getMessage(1);

        var_dump($message1->hasFlag(Storage::FLAG_SEEN));
//        $a = ; if (($overview->seen) || ($overview->deleted) || ($overview->answered)) {
//                        return;
//                    }
        echo 1;
    }

    public function readImapMessage($mbox, $num): Mail\Storage\Message
    {
        // check if the current message was already seen
        list($overview) = imap_fetch_overview($mbox, $num);

        $email = imap_headerinfo($mbox, $num);
        $header = imap_fetchheader($mbox, $num);
        $content = imap_body($mbox, $num);

        // fill with "\Seen", "\Deleted", "\Answered", ... etc
        $knownFlags = [
            'recent' => Storage::FLAG_RECENT,
            'flagged' => Storage::FLAG_FLAGGED,
            'answered' => Storage::FLAG_ANSWERED,
            'deleted' => Storage::FLAG_DELETED,
            'seen' => Storage::FLAG_SEEN,
            'draft' => Storage::FLAG_DRAFT,
        ];
        $flags = [];
        foreach ($knownFlags as $flag => $value) {
            if ($overview->$flag) {
                $flags[] = $value;
            }
        }

        $message = new Mail\Storage\Message(['headers' => $header, 'content' => $content, 'flags' => $flags]);

        return $message;
    }
}
