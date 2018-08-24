<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        print_r("new subscription!\n");
        print_r($topic->getId());
        print_r("\n");
        $this->subscribedTopics[$topic->getId()] = $topic;
        $conn->send(json_encode(array("msg"=>"lol")));
    }

    /**
     *      * @param string JSON'ified string we'll receive from ZeroMQ
     *           */
    public function onBlogEntry($entry)
    {
        $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        print_r($entryData);
        if (!array_key_exists($entryData['category'], $this->subscribedTopics))
        {
            print_r("out\n");
            return;
        }
        if (array_key_exists('status', $entryData))
        {
            print_r("status\n");
            foreach ($entryData['status'] as $key => $friend)
            {
                if (!array_key_exists($friend, $this->subscribedTopics))
                {
                    unset($entryData['status'][$key]);
                }
            }
        }
        print_r($entryData);
        $topic = $this->subscribedTopics[$entryData['category']];
        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        $conn->close();
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
        $conn->close();
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
   
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
}
