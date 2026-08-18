<?php

namespace App\Services\Communications;

use App\Contracts\Communications\TransactionalCommunicationDefinition;
use App\Contracts\Communications\TransactionalCommunicationDefinitionProvider;
use App\Support\Communications\CommunicationDefinitionData;
use InvalidArgumentException;

class CommunicationDefinitionRegistry
{
    /**
     * @return list<CommunicationDefinitionData>
     */
    public function all(): array
    {
        $definitions = [];

        foreach ((array) config('communications.definitions', []) as $class) {
            if (! is_string($class) || $class === '') {
                continue;
            }

            $instance = app($class);
            if ($instance instanceof TransactionalCommunicationDefinitionProvider) {
                foreach ($instance->definitions() as $definition) {
                    if (! $definition instanceof CommunicationDefinitionData) {
                        throw new InvalidArgumentException($class.' returned an invalid communication definition.');
                    }

                    if ($definition->classification === 'transactional') {
                        $definitions[] = $definition;
                    }
                }

                continue;
            }

            if (! $instance instanceof TransactionalCommunicationDefinition) {
                throw new InvalidArgumentException($class.' must implement '.TransactionalCommunicationDefinition::class.' or '.TransactionalCommunicationDefinitionProvider::class);
            }

            $definition = $instance->definition();
            if ($definition->classification !== 'transactional') {
                continue;
            }

            $definitions[] = $definition;
        }

        return $definitions;
    }
}
