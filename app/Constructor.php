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

    /** @var Container $container */
    //protected $container;

    /** @var FriendsModel $friends */

    /** @var BlacklistModel $blacklist */

    /** @var MessageModel $msg */

    /** @var NotificationModel $notification */

    /** @var TagModel $tag */

    /** @var UserModel $user */

    /** @var \PDO $db */

    /** @var Twig $view */

    /** @var FlashMessage $flash */

    /** @var CustomError $notFoundHandler */

    /** @var CustomError $notAllowedHandler */

    /* @var FormChecker $form */

    /** @var ft_geoIP $ft_geoIP */

    /** @var MailSender $mail */

    /** @var MyZmq $MyZmq */

    /** @var Validator $validator */

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
