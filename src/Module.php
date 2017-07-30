<?php

namespace Akademiano\EntityOperator;


use Akademiano\Core\ModuleInterface;
use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Core\ModuleManager;
use Pimple\Container;

class Module implements ModuleInterface
{
    public function init(ModuleManager $moduleManager, Container $container)
    {
        $container->extend("operator", function (EntityOperator $operator, Container $c) use ($moduleManager) {
            $workers = $moduleManager->getListArrayConfigs("workers");
            foreach ($workers as $workerName => $data) {
                $workerParams = [];
                if (is_callable($data)) {
                    $function = $data;
                } elseif (is_array($data)) {
                    foreach ($data as $key => $row) {
                        if (is_callable($row)) {
                            $function = $row;
                        } else {
                            if (is_string($key)) {
                                switch ($key) {
                                    case WorkerInterface::PARAM_TABLEID: {
                                        $operator->setWorkerTable($row, $workerName);
                                        $workerParams[WorkerInterface::PARAM_TABLEID] = $row;
                                        break;
                                    }
                                    case WorkerInterface::PARAM_ACTIONS_MAP: {
                                        if (is_array($row)) {
                                            foreach ($row as $action => $actionParam) {
                                                if (!is_array($actionParam)) {
                                                    $operator->addAction($action, $workerName, $actionParam);
                                                } else {
                                                    foreach ($actionParam as $class => $order) {
                                                        $operator->addAction($action, $workerName, $class, $order);
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                    }
                                    default: {
                                        $workerParams[$key] = $row;
                                        break;
                                    }
                                }
                            } elseif (is_integer($key)) {
                                if (is_string($row) && class_exists($row)) {
                                    if (method_exists($row, "getMetadata")) {
                                        $metadata = call_user_func([$row, "getMetadata"]);
                                        if (isset($metadata[WorkerInterface::PARAM_TABLEID])) {
                                            $tableId = $metadata[WorkerInterface::PARAM_TABLEID];
                                            $operator->setWorkerTable($tableId, $workerName);
                                            $workerParams[WorkerInterface::PARAM_TABLEID] = $tableId;
                                        }
                                    }
                                    if (method_exists($row, "getMapping")) {
                                        if (isset($data[WorkerInterface::PARAM_ACTIONS_MAP])) {
                                            $newMapping  = $data[WorkerInterface::PARAM_ACTIONS_MAP];
                                            $mapping = call_user_func_array([$row, "getMapping"],  [$newMapping]);
                                        } else {
                                            $mapping = call_user_func([$row, "getMapping"]);
                                        }
                                        foreach ($mapping as $action => $actionParam) {
                                            if (!is_array($actionParam)) {
                                                $operator->addAction($action, $workerName, $actionParam);
                                            } else {
                                                foreach ($actionParam as $class => $order) {
                                                    $operator->addAction($action, $workerName, $class, $order);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $operator->addWorker($workerName, $function);
                $operator->setWorkerParams($workerName, $workerParams);
            }
            return $operator;
        });
    }
}
