<?php

namespace Obblm\Core\Contracts;

interface TeamCreationOptionsInterface
{
    public function getTeamCreationOptions():array;
    public function getTeamCreationForm():string;
    public function getTeamCreationResolver():array;
}
