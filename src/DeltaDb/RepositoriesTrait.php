<?php

namespace DeltaDb;

trait RepositoriesTrait
{
    protected static $repositoryClass = '\DeltaDb\Repository\Repository';
    protected static $repository;

    protected static $repositoryRenderClass = '\DeltaDb\Repository\RepositoryRender';
    protected static $repositoryRender;

    protected static $repositoryRequestClass= '\DeltaDb\Repository\RepositoryRequest';
    protected static $repositoryRequest;

    public static function repository()
    {
        if (is_null(static::$repository)) {
            $repository = static::$repositoryClass;
            static::$repository = new $repository(get_called_class(), static::$table);
        }
        return static::$repository;
    }

    public static function repositoryRender()
    {
        if (is_null(static::$repositoryRender)) {
            $repository = static::$repositoryRenderClass;
            static::$repositoryRender = new $repository(get_called_class());
        }
        return static::$repositoryRender;
    }

    public static function repositoryRequest()
    {
        if (is_null(static::$repositoryrequest)) {
            $repository = static::$repositoryRequestClass;
            static::$repositoryRequest = new $repository(get_called_class());
        }
        return static::$repositoryrequest;
    }

}