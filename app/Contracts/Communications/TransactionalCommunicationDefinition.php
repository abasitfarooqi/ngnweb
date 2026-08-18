<?php

namespace App\Contracts\Communications;

use App\Support\Communications\CommunicationDefinitionData;

interface TransactionalCommunicationDefinition
{
    public function definition(): CommunicationDefinitionData;
}
