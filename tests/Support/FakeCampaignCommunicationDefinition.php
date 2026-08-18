<?php

namespace Tests\Support;

use App\Contracts\Communications\TransactionalCommunicationDefinition;
use App\Support\Communications\CommunicationDefinitionData;

class FakeCampaignCommunicationDefinition implements TransactionalCommunicationDefinition
{
    public function definition(): CommunicationDefinitionData
    {
        return new CommunicationDefinitionData(
            key: 'test.campaign',
            name: 'Test Campaign',
            classification: 'campaign',
            emailDefault: true,
        );
    }
}
