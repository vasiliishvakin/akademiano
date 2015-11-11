<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 30.10.2015
 * Time: 16:41
 */

namespace User\Twig;

use User\Model\UserManager;

class UserExtension extends \Twig_Extension
{
    /** @var  UserManager */
    protected $userManager;

    public function getName()
    {
        return 'delta_user';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'current_user',
                [$this, 'currentUser']
            ),
            new \Twig_SimpleFunction(
                'is_user_auth',
                [$this, 'isAuth']
            ),
        ];
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function currentUser()
    {
        return $this->getUserManager()->getCurrentUser();
    }

    public function isAuth()
    {
        return $this->getUserManager()->isAuth();
    }
}
