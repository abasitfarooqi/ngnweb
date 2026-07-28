<?php

namespace App\Support;

use App\Models\FinanceApplication;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class FinanceApplicationDeletion
{
    /** @return list<array{count: int, what: string, fix: string}> */
    public static function blockerDetails(int $applicationId): array
    {
        $blockers = [];

        foreach (self::blockerDefinitions($applicationId) as $definition) {
            $count = (int) $definition['query']();
            if ($count > 0) {
                $blockers[] = [
                    'count' => $count,
                    'what' => $definition['what'],
                    'fix' => $definition['fix'],
                ];
            }
        }

        return $blockers;
    }

    public static function failureReason(FinanceApplication $application): ?string
    {
        $blockers = self::blockerDetails((int) $application->id);
        if ($blockers === []) {
            return null;
        }

        $lines = [
            'Cannot delete payment plan application #'.$application->id.'. It is still linked to other records:',
        ];

        foreach ($blockers as $blocker) {
            $lines[] = sprintf(
                '• %d %s%s — %s',
                $blocker['count'],
                $blocker['what'],
                $blocker['count'] === 1 ? '' : 's',
                $blocker['fix']
            );
        }

        return implode("\n", $lines);
    }

    public static function delete(FinanceApplication $application): void
    {
        $reason = self::failureReason($application);
        if ($reason !== null) {
            throw new RuntimeException($reason);
        }

        try {
            $application->delete();
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                throw new RuntimeException(
                    self::friendlyConstraintMessage($application, $e),
                    previous: $e
                );
            }

            throw $e;
        }
    }

    /** @return list<array{what: string, fix: string, query: callable(): int}> */
    private static function blockerDefinitions(int $applicationId): array
    {
        return [
            [
                'what' => 'linked motorbike item',
                'fix' => 'edit this application and remove the motorbike from Application items, then try again',
                'query' => fn () => DB::table('application_items')
                    ->where('application_id', $applicationId)
                    ->orWhere('app_id', $applicationId)
                    ->count(),
            ],
            [
                'what' => 'contract extra item',
                'fix' => 'delete them under Finance → Contract extra items first',
                'query' => fn () => DB::table('contract_extra_items')->where('application_id', $applicationId)->count(),
            ],
            [
                'what' => 'customer contract',
                'fix' => 'remove the signed contract record first',
                'query' => fn () => DB::table('customer_contracts')->where('application_id', $applicationId)->count(),
            ],
            [
                'what' => 'contract access link',
                'fix' => 'remove them under Finance → Contract access first',
                'query' => fn () => DB::table('contract_access')->where('application_id', $applicationId)->count(),
            ],
        ];
    }

    private static function friendlyConstraintMessage(FinanceApplication $application, QueryException $e): string
    {
        $label = 'payment plan application #'.$application->id;

        if (preg_match('/CONSTRAINT `([^`]+)` FOREIGN KEY/', $e->getMessage(), $matches)) {
            $hint = self::constraintHints()[$matches[1]] ?? null;
            if ($hint !== null) {
                return "Cannot delete {$label}.\n• Cause: {$hint['cause']}.\n• Fix: {$hint['fix']}.";
            }
        }

        if (preg_match('/`([^`]+)`\.`([^`]+)`/', $e->getMessage(), $matches)) {
            $table = str_replace('_', ' ', $matches[2]);

            return "Cannot delete {$label}.\n• Cause: linked {$table} records still exist.\n• Fix: remove those records first, then try again.";
        }

        return "Cannot delete {$label}.\n• Cause: it is still referenced by other records.\n• Fix: remove the linked records first, then try again.";
    }

    /** @return array<string, array{cause: string, fix: string}> */
    private static function constraintHints(): array
    {
        return [
            'application_items_application_id_foreign' => [
                'cause' => 'one or more motorbikes are still linked to this application',
                'fix' => 'edit the application, remove Application items, then delete again',
            ],
            'contract_extra_items_application_id_foreign' => [
                'cause' => 'contract extra items are linked',
                'fix' => 'delete them under Finance → Contract extra items first',
            ],
            'customer_contracts_application_id_foreign' => [
                'cause' => 'a signed customer contract exists',
                'fix' => 'remove the customer contract record first',
            ],
            'contract_access_application_id_foreign' => [
                'cause' => 'contract access links exist',
                'fix' => 'remove them under Finance → Contract access first',
            ],
        ];
    }
}
