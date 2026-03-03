<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubMemberSpendingPaymentRequest;
use App\Models\ClubMember;
use App\Models\ClubMemberSpending;
use App\Models\ClubMemberSpendingPayment;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\DB;

class ClubMemberSpendingPaymentCrudController extends BaseCrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {
        destroy as traitDestroy;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(ClubMemberSpendingPayment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/club-member-spending-payment');
        CRUD::setEntityNameStrings('club member spending payment', 'club member spending payments');
    }

    protected function setupListOperation()
    {
        CRUD::enableExportButtons();
        CRUD::column('date')->label('Payment Date');
        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Club Member',
            'type' => 'select2',
            'entity' => 'clubMember',
            'attribute' => 'full_name',
            'model' => ClubMember::class,
        ]);
        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Phone',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'phone',
            'model' => ClubMember::class,
        ]);
        CRUD::addColumn([
            'name' => 'club_member_id',
            'label' => 'Email',
            'type' => 'text',
            'entity' => 'clubMember',
            'attribute' => 'email',
            'model' => ClubMember::class,
        ]);
        CRUD::column('received_total')->label('Payment Amount')->type('number')->attributes(['step' => 'any']);
        CRUD::addColumn([
            'name' => 'spending_id',
            'label' => 'Spending Record',
            'type' => 'select',
            'entity' => 'spending',
            'attribute' => 'pos_invoice',
            'model' => ClubMemberSpending::class,
        ]);
        CRUD::column('pos_invoice')->label('Payment Invoice');
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => 'User',
            'type' => 'select2',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class,
        ]);
        CRUD::column('branch_id')->label('Branch ID');
        CRUD::column('note')->label('Note')->type('textarea');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClubMemberSpendingPaymentRequest::class);

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('date')->label('Payment Date')->type('datetime');
        CRUD::field('club_member_id')->label('Club Member')->type('select2')
            ->entity('clubMember')
            ->model(ClubMember::class)
            ->attribute('detail');

        CRUD::addField([
            'name' => 'include_today',
            'type' => 'hidden',
            'default' => 0,
        ]);

        CRUD::addField([
            'name' => 'total_spending',
            'label' => 'Total Spending Amount',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
            'hint' => 'This is calculated automatically based on all spending records.',
        ]);

        CRUD::addField([
            'name' => 'total_unpaid',
            'label' => 'Total Unpaid Amount',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
            'hint' => 'This is calculated automatically based on unpaid spending records.',
        ]);

        CRUD::field('received_total')->label('Payment Amount')->type('number')->hint('Enter the payment amount (e.g., 50.00)')->attributes(['step' => 'any']);

        CRUD::addField([
            'name' => 'spending_id',
            'label' => 'Spending Record (Optional)',
            'type' => 'select2',
            'entity' => 'spending',
            'model' => ClubMemberSpending::class,
            'attribute' => 'pos_invoice',
            'hint' => 'Leave empty to apply payment using FIFO to oldest unpaid spendings',
            'allows_null' => true,
            'default' => null,
            'attributes' => [
                'style' => 'display:none', // Hide the field with CSS
            ],
            'wrapper' => [
                'style' => 'display: none;', // Double ensure the field is hidden
            ],
        ]);

        CRUD::field('pos_invoice')->label('Payment Invoice')->hint('Enter the payment invoice number');

        CRUD::field('branch_id')->label('Branch ID')->hint('e.g., (CATFORD, SUTTON, TOOTING)');
        CRUD::field('note')->label('Note')->type('textarea');

        Widget::add()->type('script')->inline()->content('assets/js/admin/forms/spending-totals-check.js');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        CRUD::addField(['name' => 'user_id', 'type' => 'hidden', 'default' => backpack_user()->id]);

        CRUD::field('date')->label('Payment Date')->type('datetime');
    }

    public function store()
    {
        DB::beginTransaction();
        try {
            // Ensure spending_id is always null for FIFO (field is hidden, user can't set it)
            request()->merge(['spending_id' => null]);
            
            $response = $this->traitStore(); // run Backpack's normal save
            
            // Ensure the entry has spending_id as null after save
            if ($this->crud->entry) {
                $this->crud->entry->spending_id = null;
                $this->crud->entry->save();
            }
            
            $this->updateSpendings($this->crud->entry, false);
            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing payment: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }

    public function update()
    {
        // Get original payment values before update
        $originalPayment = ClubMemberSpendingPayment::findOrFail($this->crud->getCurrentEntryId());
        $originalAmount = round($originalPayment->received_total ?? 0, 2);
        $originalSpendingId = $originalPayment->spending_id;
        $originalClubMemberId = $originalPayment->club_member_id;

        // Get new values from request
        $newClubMemberId = request('club_member_id', $originalClubMemberId);
        $newAmount = round(request('received_total', $originalAmount), 2);

        DB::beginTransaction();
        try {
            // First, revert the original payment from the original club member
            if ($originalAmount > 0) {
                $this->revertPayment($originalPayment, $originalAmount, $originalSpendingId, $originalClubMemberId);
            }

            // Ensure spending_id is always null for FIFO (field is hidden, user can't set it)
            request()->merge(['spending_id' => null]);

            // Then run Backpack's normal update
            $response = $this->traitUpdate();

            // Ensure the entry has spending_id as null after update
            if ($this->crud->entry) {
                $this->crud->entry->spending_id = null;
                $this->crud->entry->save();
            }

            // Finally, apply the new payment to the new club member (if amount > 0)
            if ($newAmount > 0 && $this->crud->entry) {
                $this->updateSpendings($this->crud->entry, true);
            }

            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating payment: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }

    protected function updateSpendings($entry, $isUpdate = false)
    {
        if (! $entry) {
            return;
        }

        $clubMemberId = $entry->club_member_id;
        $amountPaid = round($entry->received_total ?? 0, 2);

        if ($amountPaid <= 0) {
            return;
        }

        // If spending_id is provided, try to apply to that specific spending first
        $remainingPayment = $amountPaid;
        $affectedSpendings = [];
        $specificSpendingApplied = false;
        $specificSpendingWasFullyPaid = false;
        $specificSpendingId = null;

        if ($entry->spending_id) {
            $specificSpendingId = $entry->spending_id;
            $spending = ClubMemberSpending::find($specificSpendingId);
            if ($spending && $spending->club_member_id == $clubMemberId) {
                // Refresh to ensure we have latest data
                $spending->refresh();
                $paidAmount = round($spending->paid_amount ?? 0, 2);
                $unpaidAmount = round($spending->total - $paidAmount, 2);

                // Only apply if there's an unpaid amount
                if ($unpaidAmount > 0) {
                    $appliedAmount = min($remainingPayment, $unpaidAmount);
                    $appliedAmount = round($appliedAmount, 2);

                    $newPaidAmount = round($paidAmount + $appliedAmount, 2);
                    $spending->paid_amount = $newPaidAmount;
                    $spending->is_paid = (round($spending->total - $newPaidAmount, 2) <= 0.01);
                    $spending->save();

                    $affectedSpendings[] = $spending->id;
                    $remainingPayment = round($remainingPayment - $appliedAmount, 2);
                    $specificSpendingApplied = true;
                } else {
                    // Selected spending is already fully paid - clear spending_id and use FIFO
                    $specificSpendingWasFullyPaid = true;
                    $entry->spending_id = null;
                    $entry->save();
                }
            } else {
                // Spending not found or doesn't belong to club member - clear spending_id
                $entry->spending_id = null;
                $entry->save();
            }

            // If all payment was applied to specific spending, update note and return
            if ($remainingPayment <= 0 && $specificSpendingApplied) {
                if (!$isUpdate) {
                    $noteLine = "Applied £".number_format($amountPaid, 2, '.', '')." to spending ID {$specificSpendingId}";
                    $entry->note = trim(($entry->note ?? '')."\n".$noteLine);
                    $entry->save();
                }
                return;
            }
        }

        // FIFO logic: apply remaining payment to oldest unpaid spendings
        $today = now()->toDateString();

        $query = ClubMemberSpending::where('club_member_id', $clubMemberId)
            ->where(function ($q) {
                // Only get spendings that are not fully paid
                // Check both is_paid flag and actual unpaid amount (with tolerance for rounding)
                $q->where('is_paid', false)
                    ->orWhere(function ($subQ) {
                        // If is_paid is true but there's still unpaid amount (data inconsistency), include it
                        $subQ->where('is_paid', true)
                            ->whereRaw('ROUND(total - COALESCE(paid_amount, 0), 2) > 0.01');
                    });
            });

        // Exclude the specific spending if it was already processed or fully paid
        if ($specificSpendingId && ($specificSpendingApplied || $specificSpendingWasFullyPaid)) {
            $query->where('id', '<>', $specificSpendingId);
        }

        // For FIFO: include all unpaid spendings regardless of date
        // Only exclude today's spendings if a specific spending was selected and include_today is explicitly 0
        // When using FIFO (no specific spending or selected spending was fully paid), include all unpaid
        if ($specificSpendingId && !$specificSpendingWasFullyPaid && request('include_today') == 0) {
            $query->whereDate('date', '<>', $today);
        }
        // If no specific spending or selected spending was fully paid, include all unpaid (no date filter)

        $unpaidSpendings = $query->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($unpaidSpendings->isEmpty()) {
            // No more unpaid spendings
            if (!$isUpdate) {
                if ($specificSpendingApplied && $remainingPayment > 0 && $specificSpendingId) {
                    // Payment exceeds what could be applied to specific spending
                    $noteLine = "Applied £".number_format($amountPaid - $remainingPayment, 2, '.', '')." to spending ID {$specificSpendingId}. Remaining £".number_format($remainingPayment, 2, '.', '')." could not be applied (no unpaid spendings).";
                    $entry->note = trim(($entry->note ?? '')."\n".$noteLine);
                    $entry->save();
                } elseif ($specificSpendingWasFullyPaid) {
                    // Selected spending was already fully paid, applied using FIFO but no unpaid spendings found
                    $noteLine = "Selected spending was already fully paid. No unpaid spendings found to apply payment to.";
                    $entry->note = trim(($entry->note ?? '')."\n".$noteLine);
                    $entry->save();
                }
            }
            return;
        }

        try {
            foreach ($unpaidSpendings as $spending) {
                if ($remainingPayment <= 0) {
                    break;
                }

                // Safety check: skip if this is the excluded spending (shouldn't happen, but just in case)
                if ($specificSpendingId && ($specificSpendingApplied || $specificSpendingWasFullyPaid) && $spending->id == $specificSpendingId) {
                    continue;
                }

                $paidAmount = round($spending->paid_amount ?? 0, 2);
                $unpaidAmount = round($spending->total - $paidAmount, 2);

                // Skip if already fully paid (with tolerance for rounding)
                if ($unpaidAmount <= 0.01) {
                    continue;
                }

                // Calculate how much to apply to this spending
                $appliedAmount = min($remainingPayment, $unpaidAmount);
                $appliedAmount = round($appliedAmount, 2);

                // Update spending record
                $newPaidAmount = round($paidAmount + $appliedAmount, 2);
                $spending->paid_amount = $newPaidAmount;
                $spending->is_paid = (round($spending->total - $newPaidAmount, 2) <= 0.01);
                $spending->save();

                // Do NOT auto-set spending_id - keep it null for FIFO to always work properly
                // spending_id should only be set if user explicitly selects a spending

                $affectedSpendings[] = $spending->id;
                $remainingPayment = round($remainingPayment - $appliedAmount, 2);
            }

            // Update payment entry note (only if not update to avoid duplicate notes)
            if (!$isUpdate && !empty($affectedSpendings)) {
                $totalApplied = $amountPaid - $remainingPayment;
                
                if ($specificSpendingWasFullyPaid) {
                    // Selected spending was already fully paid, applied using FIFO
                    $noteLine = "Selected spending was already fully paid. Applied £".number_format($totalApplied, 2, '.', '')." using FIFO to spending IDs: ".implode(', ', $affectedSpendings);
                } elseif ($specificSpendingApplied && count($affectedSpendings) > 1) {
                    // Applied to specific spending + others via FIFO
                    $noteLine = "Applied £".number_format($totalApplied, 2, '.', '')." to spending IDs: ".implode(', ', $affectedSpendings);
                } elseif ($specificSpendingApplied) {
                    // Only applied to specific spending (shouldn't reach here, but just in case)
                    $noteLine = "Applied £".number_format($totalApplied, 2, '.', '')." to spending ID ".implode(', ', $affectedSpendings);
                } else {
                    // Only FIFO (no specific spending)
                    $noteLine = "Applied £".number_format($totalApplied, 2, '.', '')." using FIFO to spending IDs: ".implode(', ', $affectedSpendings);
                }

                if ($remainingPayment > 0) {
                    $noteLine .= ". Remaining £".number_format($remainingPayment, 2, '.', '')." could not be applied (no unpaid spendings).";
                }

                $entry->note = trim(($entry->note ?? '')."\n".$noteLine);
                $entry->save();
            }

        } catch (\Exception $e) {
            \Log::error('Error updating spendings: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());
            throw $e;
        }
    }

    protected function revertPayment($payment, $amountToRevert, $spendingId, $clubMemberId)
    {
        if ($amountToRevert <= 0) {
            return;
        }

        // If spending_id is set, revert payment from that specific spending
        if ($spendingId) {
            $spending = ClubMemberSpending::find($spendingId);
            if ($spending && $spending->club_member_id == $clubMemberId) {
                $currentPaidAmount = round($spending->paid_amount ?? 0, 2);
                $newPaidAmount = max(0, round($currentPaidAmount - $amountToRevert, 2));
                
                $spending->paid_amount = $newPaidAmount;
                $spending->is_paid = (round($spending->total - $newPaidAmount, 2) <= 0.01);
                $spending->save();
            }
        } else {
            // If no spending_id, find spendings by club_member_id that have payments
            // Reverse FIFO: revert from newest to oldest
            $spendings = ClubMemberSpending::where('club_member_id', $clubMemberId)
                ->where('paid_amount', '>', 0)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $remainingRevert = $amountToRevert;

            foreach ($spendings as $spending) {
                if ($remainingRevert <= 0) {
                    break;
                }

                $currentPaidAmount = round($spending->paid_amount ?? 0, 2);
                if ($currentPaidAmount <= 0) {
                    continue;
                }

                $revertAmount = min($remainingRevert, $currentPaidAmount);
                $revertAmount = round($revertAmount, 2);

                $newPaidAmount = max(0, round($currentPaidAmount - $revertAmount, 2));
                $spending->paid_amount = $newPaidAmount;
                $spending->is_paid = (round($spending->total - $newPaidAmount, 2) <= 0.01);
                $spending->save();

                $remainingRevert = round($remainingRevert - $revertAmount, 2);
            }
        }
    }

    public function destroy($id)
    {
        // Get the payment record before deletion
        $payment = ClubMemberSpendingPayment::find($id);

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment record not found.'], 404);
        }

        $clubMemberId = $payment->club_member_id;
        $spendingId = $payment->spending_id;
        $amountToRevert = round($payment->received_total ?? 0, 2);

        DB::beginTransaction();
        try {
            // Revert the payment using the shared method
            $this->revertPayment($payment, $amountToRevert, $spendingId, $clubMemberId);

            DB::commit();

            // Now delete the payment record using the trait method
            return $this->traitDestroy($id);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reverting payment: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while reverting the payment.',
            ], 500);
        }
    }
}
