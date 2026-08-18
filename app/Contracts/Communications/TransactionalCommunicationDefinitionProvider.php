<?php

namespace App\Contracts\Communications;

use App\Support\Communications\CommunicationDefinitionData;

interface TransactionalCommunicationDefinitionProvider
{
    /**
     * @return list<CommunicationDefinitionData>
     */
    public function definitions(): array;
}
