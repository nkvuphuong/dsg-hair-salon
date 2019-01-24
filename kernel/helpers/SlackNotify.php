<?php
/**
 * Created by PhpStorm.
 * User: PhuongNKV
 * Date: 11/19/2018
 * Time: 1:56 PM
 */

namespace Kernel\Helpers;


use Maknz\Slack\Client;

class SlackNotify
{
    protected $client;

    public function __construct($endpoint)
    {
        if(!empty($endpoint)) {
            $this->client = new Client($endpoint);
            $this->client->setDefaultIcon('https://avatars.slack-edge.com/2017-06-23/202491876610_9f36995cb75b95a86d80_36.png');
//            $this->client->setDefaultChannel('@nkvuphuong');
        }
    }

    /**
     * @param array $attaches
     * @param string $message
     */
    public function send($attaches = [], $message = "")
    {
        if (!empty($this->client)) {
            $this->client->createMessage()
                ->setAttachments($attaches)
                ->send($message);
        }
    }
}