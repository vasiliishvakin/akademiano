<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Relations;

use DeltaUtils\Parts\InnerCache;

class relationsFactory
{
    use InnerCache;

    protected $managersParams  = [];

    public function getManager($managerName, $params = null)
    {
        if (!$manager = $this->getInnerCache($managerName)) {
            if ($params) {
                $this->setManagerParams($managerName, $params[0], $params[1]);
            } else {
                $params = $this->getManagerParams($managerName);
            }
            if (!$params) {
                return null;
            }
            $manager = new mnRelationsManager();
            $manager->setName($managerName);
            $firstInnerManager = is_callable($params[0]) ? call_user_func($params[0]) : $params[0];
            $secondInnerManager = is_callable($params[1]) ? call_user_func($params[1]) : $params[1];
            $manager->setFirstManager($firstInnerManager);
            $manager->setSecondManager($secondInnerManager);
            $this->setInnerCache($managerName, $manager);
        }
        return $manager;
    }

    public function setManagerParams($managerName, $firstManager, $secondManager)
    {
        $this->managersParams[$managerName] = [$firstManager, $secondManager];
    }

    public function getManagerParams($managerName)
    {
        return isset($this->managersParams[$managerName]) ? $this->managersParams[$managerName] : null;
    }

} 