<?php

namespace Obblm\Core\Contracts\Rule;

/**********************
 * APPLICATION METHODS
 *********************/
interface RuleApplicativeInterface
{
    public function getActionsFormClass():string;
    public function getInjuriesFormClass():string;
    public function getTemplateKey():string;
}
