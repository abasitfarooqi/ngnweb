<?php

namespace App\Support\Communications;

use App\Contracts\Communications\TransactionalCommunicationDefinitionProvider;

class DiscoveredTransactionalCommunicationCatalog implements TransactionalCommunicationDefinitionProvider
{
    /**
     * @return list<CommunicationDefinitionData>
     */
    public function definitions(): array
    {
        return [
            $this->mail('service.booking.confirmation', 'Service Booking Confirmation', 'Service booking/enquiry confirmation sent to the customer.', 'bookings', \App\Mail\BookingConfirmation::class, 'emails.templates.agreement-controller-universal', 'Customer email on the booking record', ['booking']),
            $this->mail('customer.appointment.notification', 'Customer Appointment Notification', 'Appointment update sent to a customer from admin or the portal.', 'appointments', \App\Mail\CustomerAppointmentNotification::class, 'emails.templates.agreement-controller-universal', 'Customer email plus configured service recipients', ['customer', 'appointment', 'date', 'time']),
            $this->mail('customer.document.request', 'Customer Document Request', 'Request for a customer to upload or review required documents.', 'documents', \App\Mail\CustomerDocumentRequest::class, 'emails.templates.agreement-controller-universal', 'Customer email and customer-service copy where currently configured', ['customer', 'documents', 'upload_link'], mandatory: true),
            $this->mail('invoice.due.customer_reminder', 'Due Invoice Customer Reminder', 'Reminder sent to a customer for a due rental invoice.', 'invoices', \App\Mail\DueInvoiceCustomerMail::class, 'emails.templates.agreement-controller-universal', 'Customer email from due invoice data', ['booking_no', 'customer_name', 'customer_email', 'invoice_date']),
            $this->mail('finance.contract.review', 'Finance Contract Review', 'Finance contract review email sent to the customer.', 'finance', \App\Mail\FinanceContractReview::class, 'emails.templates.agreement-controller-universal', 'Finance applicant/customer email', ['customer', 'contract', 'documents'], mandatory: true),
            $this->mail('contract.hire.issued', 'Hire Contract Issued', 'Hire contract email with generated contract attachments.', 'contracts', \App\Mail\HireContract::class, 'emails.templates.agreement-controller-universal', 'Customer email and existing configured copies', ['customer', 'contract', 'pdf'], mandatory: true),
            $this->mail('rental.instalment.notification', 'Rental Instalment Notification', 'Rental instalment notification sent to the customer.', 'rentals', \App\Mail\InstalmentNotificationMail::class, 'emails.templates.agreement-controller-universal', 'Customer email from rental instalment schedule', ['customer', 'registration_number', 'motorbike_id']),
            $this->mail('vehicle.logbook.transfer', 'Logbook Transfer Notification', 'Vehicle logbook transfer status email sent to a customer.', 'vehicles', \App\Mail\LogBookTransferMail::class, 'emails.templates.agreement-controller-universal', 'Finance/customer email from the related vehicle or application', ['customer', 'vehicle', 'registration_number']),
            $this->mail('loyalty.policy.issued', 'Loyalty Scheme Policy Issued', 'Loyalty scheme policy document sent to the customer.', 'club', \App\Mail\LoyaltySchemePolicy::class, 'emails.templates.agreement-controller-universal', 'Customer email', ['customer', 'policy', 'pdf']),
            $this->mail('mot.booking.created', 'MOT Booking Notification', 'MOT booking confirmation/update sent to a customer.', 'mot', \App\Mail\MOTBookingNotification::class, 'emails.templates.agreement-controller-universal', 'MOT booking customer email', ['customer', 'booking', 'date', 'time']),
            $this->mail('mot.booking.cancelled', 'MOT Booking Cancelled', 'MOT cancellation email sent to a customer.', 'mot', \App\Mail\MOTCancelledNotification::class, 'emails.templates.agreement-controller-universal', 'MOT booking customer email', ['customer', 'booking', 'date']),
            $this->mail('mot.booking.completed', 'MOT Booking Completed', 'MOT completion email sent to a customer.', 'mot', \App\Mail\MOTCompletedNotification::class, 'emails.templates.agreement-controller-universal', 'MOT booking customer email', ['customer', 'booking', 'vehicle']),
            $this->mail('mot.reminder', 'MOT Reminder', 'MOT reminder email sent before expiry or appointment follow-up.', 'mot', \App\Mail\MOTReminderEmail::class, 'emails.templates.agreement-controller-universal', 'Customer email from MOT reminder data', ['customer', 'vehicle', 'mot_date']),
            $this->raw('mot.status.result_email', 'MOT Status Result Email', 'MOT checker result email sent to a customer from the website.', 'mot', \App\Livewire\Site\Mot\Checker::class, 'Website MOT checker customer email', ['registration', 'mot_status', 'mot_expiry', 'tax_status']),
            $this->mail('mot.expiry.ten_day_reminder', 'MOT 10 Day Reminder', 'MOT expiry reminder sent 10 days before expiry.', 'mot', \App\Mail\MOT10DaysReminder::class, 'emails.templates.agreement-controller-universal', 'Customer email from MOT reminder data', ['customer', 'vehicle', 'expiry_date']),
            $this->mail('mot.tax.notification', 'MOT Tax Notification', 'MOT/tax notification sent to a customer subscriber.', 'mot', \App\Mail\MOTTaxNotificationMail::class, 'emails.templates.agreement-controller-universal', 'Subscriber/customer email', ['customer', 'vehicle', 'mot', 'tax']),
            $this->mail('delivery.order.confirmed', 'Delivery Order Confirmed', 'Motorbike delivery/transport order confirmation sent to the customer.', 'delivery', \App\Mail\MotorbikeTransportDeliveryOrderConfirmed::class, 'emails.templates.agreement-controller-universal', 'Delivery order customer email', ['customer', 'order', 'from_address', 'to_address']),
            $this->mail('delivery.order.cancelled', 'Delivery Order Cancelled', 'Motorbike delivery/transport order cancellation sent to the customer.', 'delivery', \App\Mail\MotorbikeTransportDeliveryOrderCancelled::class, 'emails.templates.agreement-controller-universal', 'Delivery order customer email', ['customer', 'order']),
            $this->mail('delivery.order.enquiry', 'Delivery Order Enquiry', 'Delivery or recovery enquiry email sent to the customer and/or current recipients.', 'delivery', \App\Mail\MotorbikeTransportDeliveryOrderEnquiry::class, 'emails.templates.agreement-controller-universal', 'Customer email and existing support copies where configured', ['customer', 'delivery_order', 'from_address', 'to_address']),
            $this->mail('motorcycle.recovery.request', 'Motorcycle Recovery Request', 'Motorcycle recovery request confirmation sent to the customer and configured support copy.', 'recovery', \App\Mail\MotorcycleRecoveryMail::class, 'emails.templates.agreement-controller-universal', 'Customer email plus existing support copies', ['customer', 'from_address', 'to_address', 'distance']),
            $this->mail('club.member.welcome', 'NGN Club Member Welcome', 'Welcome/login credential email sent to a club member.', 'club', \App\Mail\NewSubscriberNotification::class, 'emails.templates.agreement-controller-universal', 'Club member email', ['club_member', 'passkey']),
            $this->mail('club.batch.credentials', 'NGN Club Batch Credentials', 'Batch-generated club login credentials sent to a member.', 'club', \App\Mail\NgnClubBatchUserCredentialsNotification::class, 'emails.templates.agreement-controller-universal', 'Club member email', ['club_member', 'credentials']),
            $this->mail('order.shipped', 'Order Shipped', 'Order shipment notification sent to a customer.', 'orders', \App\Mail\OrderShipped::class, 'emails.orders.shipped', 'Order customer email', ['order', 'tracking']),
            $this->mail('rental.other_charge.receipt', 'Rental Other Charge Receipt', 'Receipt for paid rental other charges.', 'rentals', \App\Mail\OtherChargesReceipt::class, 'emails.templates.agreement-controller-universal', 'Customer email and existing customer-service copy', ['customer', 'charge', 'amount']),
            $this->mail('pcn.payment_reminder', 'PCN Payment Reminder', 'PCN payment reminder sent to a customer or responsible recipient.', 'pcn', \App\Mail\PCNNotify::class, 'emails.templates.agreement-controller-universal', 'PCN customer/responsible-party email', ['customer', 'pcn', 'amount', 'due_date'], mandatory: true),
            $this->mail('pcn.police.payment_reminder', 'PCN Police Payment Reminder', 'PCN reminder variant sent where police notification path is used.', 'pcn', \App\Mail\PCNPoliceNotify::class, 'emails.templates.agreement-controller-universal', 'PCN customer/responsible-party email', ['customer', 'pcn', 'amount', 'due_date'], mandatory: true),
            $this->mail('pcn.job.email', 'PCN Job Email', 'Scheduled PCN email job sent to the customer.', 'pcn', \App\Mail\PcnJobEmail::class, 'emails.templates.agreement-controller-universal', 'PCN job customer email', ['customer', 'pcn', 'template']),
            $this->mail('mot.payment.reminder', 'MOT Payment Reminder', 'Payment reminder for an MOT booking.', 'mot', \App\Mail\PaymentReminderNotification::class, 'emails.templates.agreement-controller-universal', 'MOT booking customer email', ['customer', 'booking', 'amount']),
            $this->mail('purchase.invoice.issued', 'Purchase Invoice Issued', 'Purchase invoice email sent to a customer with generated PDF.', 'purchases', \App\Mail\PurchaseInvoice::class, 'emails.purchase-invoice', 'Customer email', ['customer', 'purchase', 'pdf'], mandatory: true),
            $this->mail('purchase.invoice.review', 'Purchase Invoice Review', 'Purchase invoice review email sent from admin.', 'purchases', \App\Mail\PurchaseInvoiceReview::class, 'emails.templates.agreement-controller-universal', 'Customer email from purchase invoice data', ['customer', 'purchase']),
            $this->mail('quote.request', 'Quote Request', 'Quote request email with generated PDF where used.', 'quotes', \App\Mail\QuoteRequest::class, 'emails.quote-request', 'Customer/requester email', ['customer', 'quote', 'pdf']),
            $this->mail('rental.invoice.reminder', 'Rental Invoice Reminder', 'Weekly rent invoice reminder sent to a customer.', 'rentals', \App\Mail\RentInvoiceReminderMail::class, 'emails.templates.agreement-controller-universal', 'Customer email from active rental booking', ['customer', 'registration_number', 'weekly_rent']),
            $this->mail('rental.agreement.issued', 'Rental Agreement Issued', 'Rental agreement email with generated agreement attachments.', 'rentals', \App\Mail\RentalAgreement::class, 'emails.templates.agreement-controller-universal', 'Customer email plus existing customer-service copy', ['customer', 'rental', 'pdf'], mandatory: true),
            $this->mail('rental.agreement.review', 'Rental Agreement Review', 'Rental agreement review email sent to a customer.', 'rentals', \App\Mail\RentalAgreementReview::class, 'emails.templates.agreement-controller-universal', 'Customer email', ['customer', 'rental', 'pdf']),
            $this->mail('rental.due', 'Rental Due', 'Rental due notification sent to a customer.', 'rentals', \App\Mail\RentalDue::class, 'emails.rental-due', 'Customer email', ['customer', 'rental', 'amount']),
            $this->mail('rental.ended_with_pendings', 'Rental Ended With Pending Items', 'Rental ended notification where pending items remain.', 'rentals', \App\Mail\RentalEndedWithPendingsMail::class, 'emails.templates.agreement-controller-universal', 'Customer email and existing configured recipients', ['customer', 'rental', 'pending_items']),
            $this->mail('rental.other_charge.reminder', 'Rental Other Charge Reminder', 'Reminder for unpaid rental other charges.', 'rentals', \App\Mail\RentalOtherChargeReminderMail::class, 'emails.templates.agreement-controller-universal', 'Customer email plus existing customer-service copy', ['customer', 'charge', 'amount']),
            $this->mail('rental.payment.receipt', 'Rental Payment Receipt', 'Receipt sent after a rental payment is received.', 'rentals', \App\Mail\RentalPaymentReceipt::class, 'emails.templates.agreement-controller-universal', 'Customer email', ['customer', 'payment', 'amount'], mandatory: true),
            $this->mail('rental.deposit.return', 'Rental Deposit Return', 'Confirmation sent when a rental deposit is returned after closing.', 'rentals', \App\Mail\RentalDepositReturnMail::class, 'emails.templates.agreement-controller-universal', 'Customer email plus existing customer-service copy', ['customer', 'booking', 'deposit', 'amount_returned'], mandatory: true),
            $this->mail('rental.payment.reversed', 'Rental Payment Reversed', 'Notice sent when a rental payment is reversed.', 'rentals', \App\Mail\RentalPaymentReversedNotice::class, 'emails.templates.agreement-controller-universal', 'Customer email plus existing customer-service copy', ['customer', 'payment', 'amount'], mandatory: true),
            $this->mail('rental.terminated', 'Rental Contract Terminated', 'Rental termination email with generated termination PDF.', 'rentals', \App\Mail\RentalTerminateEmail::class, 'emails.rental-terminate', 'Customer email', ['customer', 'rental', 'pdf'], mandatory: true),
            $this->mail('delivery.vehicle.pickup_estimate', 'Vehicle Delivery Pickup Estimate', 'Vehicle delivery order pickup estimate sent to the customer.', 'delivery', \App\Mail\VehicleDeliveryOrderConfirmation::class, 'emails.templates.agreement-controller-universal', 'Delivery order customer email plus existing customer-service copy', ['customer', 'delivery_order', 'from_address', 'to_branch']),
            $this->mail('ecommerce.customer.registered', 'Shop Customer Registered', 'Shop/customer account registration email.', 'ecommerce', \App\Mail\Ecommerce\CustomerRegisterMailer::class, 'emails.templates.agreement-controller-universal', 'Shop customer email', ['customer']),
            $this->mail('ecommerce.order.confirmed', 'Shop Order Confirmed', 'Shop order confirmation sent to customer.', 'ecommerce', \App\Mail\Ecommerce\OrderConfirmedMailer::class, 'emails.templates.agreement-controller-universal', 'Shop order customer email', ['order', 'customer', 'items']),
            $this->mail('ecommerce.order.processing', 'Shop Order Processing', 'Shop order processing update sent to customer.', 'ecommerce', \App\Mail\Ecommerce\OrderProcessMailer::class, 'emails.templates.agreement-controller-universal', 'Shop order customer email', ['order', 'customer', 'items']),
            $this->mail('ecommerce.order.ready_to_collect', 'Shop Order Ready To Collect', 'Shop order ready-to-collect update sent to customer.', 'ecommerce', \App\Mail\Ecommerce\OrderReadyToCollectMailer::class, 'emails.templates.agreement-controller-universal', 'Shop order customer email', ['order', 'customer', 'branch']),
            $this->mail('ecommerce.order.refunded', 'Shop Order Refunded', 'Shop order refund email sent to customer.', 'ecommerce', \App\Mail\Ecommerce\OrderRefundMailer::class, 'emails.templates.agreement-controller-universal', 'Shop order customer email', ['order', 'customer', 'refund']),
            $this->mail('rental.referral.invitation', 'Referral invitation to a friend', 'Sent to the person being referred, with a link to view bikes. Not a bulk mail.', 'rentals', \App\Mail\RentingReferralInvitationMail::class, 'emails.templates.agreement-controller-universal', 'The friend’s email on the referral', ['referral', 'referrer', 'share_url']),
            $this->mail('rental.referral.under_review', 'We have your referral', 'Sent to the customer who referred a friend, once we have that referral.', 'rentals', \App\Mail\RentingReferralUnderReviewMail::class, 'emails.templates.agreement-controller-universal', 'The referrer’s email', ['referral', 'referrer']),
            $this->mail('rental.referral.reward_available', 'Referral free week is ready', 'Sent to the referrer when their free week can be applied.', 'rentals', \App\Mail\RentingReferralRewardAvailableMail::class, 'emails.templates.agreement-controller-universal', 'The referrer’s email', ['referral', 'points']),
            $this->mail('rental.referral.approval_report', 'Referral approved (director copy)', 'Internal copy to the director after a referral is approved.', 'rentals', \App\Mail\RentingReferralApprovalReportMail::class, 'emails.templates.agreement-controller-universal', 'Director', ['referral', 'approver', 'checks', 'logs']),
            $this->mail('rental.referral.staff_invoice_notice', 'Referral free week applied (director copy)', 'Internal copy to the director when a programme free week is applied, or when points are ready.', 'rentals', \App\Mail\RentingReferralStaffInvoiceMail::class, 'emails.templates.agreement-controller-universal', 'Director', ['referral', 'handler', 'booking', 'invoice', 'transaction']),
            $this->mail('rental.direct.free_week', 'Staff free week, not a referral (director copy)', 'Internal copy when staff give a free week without a programme referral.', 'rentals', \App\Mail\RentingDirectFreeWeekMail::class, 'emails.templates.agreement-controller-universal', 'Director', ['booking', 'invoice', 'hirer', 'selectedCustomer', 'handler', 'proof']),
            $this->mail('rental.invoice.update_reminder', 'Invoice chase note to the customer', 'Sent when staff add an invoice update and confirm. Asks the customer to pay. Customer service is copied.', 'rentals', \App\Mail\RentingInvoiceUpdateReminderMail::class, 'emails.templates.agreement-controller-universal', 'Customer, with customer service copied', ['customer', 'booking', 'invoice', 'update']),
        ];
    }

    /**
     * @param  list<string>  $variables
     */
    private function mail(string $key, string $name, string $description, string $category, string $emailClass, ?string $templateView, string $recipientSummary, array $variables = [], bool $mandatory = false): CommunicationDefinitionData
    {
        return new CommunicationDefinitionData(
            key: $key,
            name: $name,
            description: $description,
            category: $category,
            priority: $mandatory ? 'important' : 'normal',
            emailClass: $emailClass,
            templateView: $templateView,
            recipientSummary: $recipientSummary,
            supportedChannels: ['email', 'internal_inbox', 'web_push', 'mobile_push'],
            variables: $variables,
            metadata: [
                'discovered_from' => 'repository_mail_audit',
                'classification_note' => 'TRANSACTIONAL - IN SCOPE',
                'initial_policy' => 'Email ON, Internal Inbox OFF, Web Push OFF, Mobile Push OFF',
            ],
            mandatoryDefault: $mandatory,
        );
    }

    /**
     * @param  list<string>  $variables
     */
    private function raw(string $key, string $name, string $description, string $category, string $sourceClass, string $recipientSummary, array $variables = [], bool $mandatory = false): CommunicationDefinitionData
    {
        return new CommunicationDefinitionData(
            key: $key,
            name: $name,
            description: $description,
            category: $category,
            priority: $mandatory ? 'important' : 'normal',
            sourceClass: $sourceClass,
            sourceTrigger: 'Raw Mail::send path guarded by communication key',
            recipientSummary: $recipientSummary,
            supportedChannels: ['email', 'internal_inbox', 'web_push', 'mobile_push'],
            variables: $variables,
            metadata: [
                'discovered_from' => 'repository_raw_mail_audit',
                'classification_note' => 'TRANSACTIONAL - IN SCOPE',
                'initial_policy' => 'Email ON, Internal Inbox OFF, Web Push OFF, Mobile Push OFF',
            ],
            mandatoryDefault: $mandatory,
        );
    }
}
