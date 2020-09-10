<?php

namespace Obblm\Core\Traits;

trait ClassNameAsKeyTrait {
    /**
     * @internal
     */
    public function getKey():string
    {
        return get_class($this);
    }
}