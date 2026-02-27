<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('judopay_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('judopay_onboarding_id')->index('judopay_subscriptions_judopay_onboarding_id_foreign');
            $table->date('date')->comment('date of subscription Despite the outcome(status)');
            $table->string('subscribable_type');
            $table->unsignedBigInteger('subscribable_id');
            $table->enum('billing_frequency', ['weekly', 'monthly', 'custom'])->comment('Payment frequency');
            $table->integer('billing_day')->nullable()->comment('Day of week (1-7) for weekly, day of month (1-28) for monthly');
            $table->decimal('amount', 10)->comment('Recurring payment amount. Require for both rental and finance.');
            $table->decimal('opening_balance', 10)->default(0)->comment('Existing balance at the time of onboarding. 0 if rental.');
            $table->date('start_date')->comment('The date of the first payment. If rental. It is the date of the rental date.');
            $table->date('end_date')->nullable()->comment('At the time of onboarding, the expected end date of the contract. In case of Rental null. Rental require manual stop subscription.');
            $table->enum('status', ['pending', 'active', 'inactive', 'paused', 'completed', 'cancelled'])->default('pending')->comment('They all are more like flags. Only active is where the recurring payment got fired. It is NGN flag');
            $table->string('consumer_reference')->comment('JudoPay consumer reference format: RENTAL/FINANCE/CUSTOMER ID 
                        e.g. HIRE-BOOKINGID-CUSTOMERID or FIN-CONTRACTID-CUSTOMERID. FIN-12-201 / HIR-12-201');
            $table->text('card_token')->nullable()->comment('The Card Token to be used for MIT - ENCRYPTED PCI SENSITIVE.');
            $table->string('receipt_id')->nullable()->comment('CIT successful trnasaction gives the receipt id. preserve to use for MIT.');
            $table->string('judopay_receipt_id')->nullable()->comment('From webhook receipt.receiptId - JudoPay unique transaction identifier');
            $table->string('acquirer_transaction_id')->nullable()->comment('From webhook receipt.acquirerTransactionId - Bank transaction reference');
            $table->string('auth_code')->nullable()->comment('From webhook receipt.authCode - Bank authorization code for successful payment');
            $table->string('merchant_name')->nullable()->comment('From webhook receipt.merchantName - Merchant name as registered with JudoPay');
            $table->string('statement_descriptor')->nullable()->comment('From webhook receipt.appearsOnStatementAs - How transaction appears on customer statement');
            $table->string('card_last_four', 4)->nullable()->comment('From webhook receipt.cardDetails.cardLastfour - Last 4 digits of card (non-PCI)');
            $table->string('card_funding')->nullable()->comment('From webhook receipt.cardDetails.cardFunding - Card type: Credit/Debit');
            $table->string('card_category')->nullable()->comment('From webhook receipt.cardDetails.cardCategory - Card category: Classic/Premium/etc');
            $table->string('card_country', 2)->nullable()->comment('From webhook receipt.cardDetails.cardCountry - ISO country code of card issuer');
            $table->string('issuing_bank')->nullable()->comment('From webhook receipt.cardDetails.bank - Name of card issuing bank');
            $table->json('billing_address')->nullable()->comment('From webhook receipt.billingAddress - Customer billing address used for payment');
            $table->json('risk_assessment')->nullable()->comment('From webhook receipt.risks - Risk checks: postCodeCheck, cv2Check, merchantSuggestion');
            $table->json('three_d_secure')->nullable()->comment('From webhook receipt.threeDSecure - 3DS authentication details and challenge results');
            $table->json('audit_log')->nullable()->comment('Audit trail. Important states changes dates.');
            $table->timestamps();

            $table->index(['subscribable_type', 'subscribable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judopay_subscriptions');
    }
};
