<?php
namespace MyApp;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = array();
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        print_r("new subscription to " . $topic->getId() . "\n");
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    /**
     *      * @param string JSON'ified string we'll receive from ZeroMQ
     *           */
    public function onBlogEntry($entry)
    {
        $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['category'], $this->subscribedTopics))
        {
            return;
        }
        if (array_key_exists('mateStatus', $entryData))
        {
            foreach ($entryData['mateStatus'] as $key => $friend)
            {
                if (!array_key_exists($friend, $this->subscribedTopics))
                    $entryData['mateStatus'][$key] = false;
                else if ($this->subscribedTopics[$friend]->count() === 1)
                    $entryData['mateStatus'][$key] = true;
                else
                {
                    unset($this->subscibedTopics[$friend]);
                    $entryData['mateStatus'][$key] = false;
                }
            }
        }
        elseif (array_key_exists('profilStatus', $entryData))
        {
            $user = $entryData['profilStatus'];
            if (!array_key_exists($user, $this->subscribedTopics))
                $entryData['profilStatus'] = false;
            else if ($this->subscribedTopics[$user]->count() >= 1)
                $entryData['profilStatus'] = true;
            else
            {
                unset($this->subscibedTopics[$friend]);
                $entryData['profilStatus'] = false;
            }
        }
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
        $conn->close();
    }
}
