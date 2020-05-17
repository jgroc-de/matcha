<?php

namespace App;

use App\Lib\CustomError;
use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\ft_geoIP;
use App\Lib\MailSender;
use App\Lib\MyZmq;
use App\Lib\Validator;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\MessageModel;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Slim\Container;
use Slim\Views\Twig;

/**
 * constructor for each route.
 */
abstract class Constructor
{
    /** @var array of all kind available */
    protected $characters = ['Rick', 'Morty', 'Beth', 'Jerry', 'Summer'];

    /** @var array all orientation available */
    protected $sexualPattern = ['bi', 'homo', 'hetero'];

    /** @var Container for $container */
    protected $container;

    /** @var FriendsModel */
    protected $friends;

    /** @var BlacklistModel */
    protected $blacklist;

    /** @var MessageModel */
    protected $msg;

    /** @var NotificationModel */
    protected $notification;

    /** @var TagModel */
    protected $tag;

    /** @var UserModel */
    protected $user;

    /** @var \PDO */
    protected $db;

    /** @var Twig */
    protected $view;

    /** @var FlashMessage */
    protected $flash;

    /** @var CustomError */
    protected $notFoundHandler;

    /** @var CustomError */
    protected $notAllowedHandler;

    /* @var FormChecker */
    protected $form;

    /** @var ft_geoIP */
    protected $ft_geoIP;

    /** @var MailSender */
    protected $mail;

    /** @var MyZmq */
    protected $MyZmq;

    /** @var Validator */
    protected $validator;





    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name shortcut to access dependencies in $container
     *
     * @return $container['$name'] : matching class from container if any
     */
    public function __get(string $name)
    {
        if (isset($this->container[$name])) {
            return $this->container->get($name);
        }

        return Null;
    }
}
