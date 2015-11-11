<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Controller;


use Acl\Model\Parts\AclController;
use Articles\Model\Article;
use Articles\Model\Parts\GetArticlesManager;
use DeltaCore\AbstractController;
use Elastic\Parts\ElasticManagerPart;
use Organizations\Controller\Parts\GetOrganizationsManager;
use Organizations\Model\Organization;
use Places\Controller\Parts\PlacesManagerGetter;
use Places\Model\Place;

class AdminController extends AbstractController
{
    use AclController;

    public function indexAction()
    {

    }



} 