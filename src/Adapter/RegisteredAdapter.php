<?php

namespace Akademiano\Acl\Adapter;


use Akademiano\User\GuestGroupInterface;
use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;


class RegisteredAdapter extends XAclAdapterInterface implements AdapterInterface
{
    protected $patches;

    /**
     * @return mixed
     */
    public function getPatches()
    {
        if (null === $this->patches) {
            $patches = file($this->getAclFile(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $patchesTree = [];
            foreach ($patches as $path) {
                if (false !== mb_strpos($path, "[")) {
                    continue;
                }
                if (false !== $pos = mb_strpos($path, "=")) {
                    $path = mb_strcut($path, 0, $pos);
                }
                $path = $this->prepareResource($path);
                $char = mb_strcut($path, 0, 1);
                $patchesTree[$char][] = $path;
            }
            $this->patches = $patchesTree;
        }

        return $this->patches;
    }

    public function accessCheck($resource, UserInterface $owner = null, GroupInterface $group, UserInterface $user = null)
    {
        $resource = $this->prepareResource($resource);
        $char = mb_strcut($resource, 0, 1);
        $resource = explode(":", $resource);
        $patches = $this->getPatches();
        if (isset($patches[$char])) {
            foreach ($patches[$char] as &$patch) {
                if (!is_array($patch)) {
                    $patch = explode(":", $patch);
                }
                $patchCount = count($patch);
                $resourceCount = count($resource);
                if ($resourceCount < $patchCount) {
                    continue;
                }
                $latsPatchPart = $patch[$patchCount - 1];
                if ($resourceCount > $patchCount && $latsPatchPart !== "*") {
                    continue;
                }

                $controlled = false;
                for ($i = 0; $i < $patchCount; $i++) {
                    if ($resource[$i] === $patch[$i] || ($patch[$i] === "*")) {
                        $controlled = true;
                    } else {
                        $controlled = false;
                        break;
                    }
                }

                if ($controlled) {
                    return (!empty($group) && (!$group instanceof GuestGroupInterface));
                }
            }
        }

        return true;
    }
}
