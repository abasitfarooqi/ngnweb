<?php

namespace Tests\Support;

use App\Contracts\Communications\TransactionalCommunicationDefinition;
use App\Support\Communications\CommunicationDefinitionData;

class FakeTransactionalCommunicationDefinition implements TransactionalCommunicationDefinition
{
    public function definition(): CommunicationDefinitionData
    {
        return new CommunicationDefinitionData(
            key: 'test.transactional',
            name: 'Test Transactional',
            sourceClass: self::class,
            emailClass: 'App\\Mail\\ExampleMail',
            templateView: 'emails.example',
        );
    }
}
