<?php

namespace App\Support;

use App\Models\AccessLog;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Branch;
use App\Models\AgreementAccess;
use App\Models\Calander;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use App\Models\ClubMemberSpending;
use App\Models\ClubMemberSpendingPayment;
use App\Models\CompanyVehicle;
use App\Models\ContactQuery;
use App\Models\ContractAccess;
use App\Models\ContractExtraItem;
use App\Models\Customer;
use App\Models\CustomerAppointments;
use App\Models\CustomerDocument;
use App\Models\ClaimMotorbike;
use App\Models\DsOrderItem;
use App\Models\DsOrder;
use App\Models\EcOrder;
use App\Models\EmployeeSchedule;
use App\Models\FinanceApplication;
use App\Models\FinanceApplicationItem;
use App\Models\IpRestriction;
use App\Models\JudopayMitQueue;
use App\Models\JudopaySubscription;
use App\Models\MOTBooking;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeCatB;
use App\Models\MotorbikeDeliveryOrderEnquiry;
use App\Models\MotorbikeRepair;
use App\Models\MotorbikeRepairUpdate;
use App\Models\MotorbikesSale;
use App\Models\Motorcycle;
use App\Models\NewMotorbike;
use App\Models\NgnBrand;
use App\Models\NgnCareer;
use App\Models\NgnDigitalInvoice;
use App\Models\NgnDigitalInvoiceItem;
use App\Models\NgnMitQueue;
use App\Models\NgnProduct;
use App\Models\NgnSurvey;
use App\Models\NgnSurveyAnswer;
use App\Models\NgnSurveyOption;
use App\Models\NgnSurveyQuestion;
use App\Models\NgnSurveyResponse;
use App\Models\OxfordProduct;
use App\Models\NgnPartner;
use App\Models\PcnCase;
use App\Models\PcnTolRequest;
use App\Models\PcnUpdate;
use App\Models\Permission;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RecoveredMotorbike;
use App\Models\RentingBooking;
use App\Models\RentingBookingInvoice;
use App\Models\RentingPricing;
use App\Models\RentingServiceVideo;
use App\Models\Role;
use App\Models\ServiceBooking;
use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use App\Models\SpFitment;
use App\Models\SpMake;
use App\Models\SpModel;
use App\Models\SpPart;
use App\Models\SpStockMovement;
use App\Models\StockMovement;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\UploadDocumentAccess;
use App\Models\User;
use App\Models\VehicleDeliveryOrder;
use App\Models\VehicleIssuance;
use App\Models\VehicleNotification;

class FluxAdminSearchRegistry
{
    /** @return array<int, array<string, mixed>> */
    public static function resources(): array
    {
        return [
            self::entry(Motorbike::class, 'Motorbikes', 'flux-admin.motorbikes.index', 'flux-admin.motorbikes.show', 'flux-admin.motorbikes.edit', 'motorbike'),
            self::entry(Customer::class, 'Customers', 'flux-admin.customers.index', 'flux-admin.customers.show', 'flux-admin.customers.edit', 'customer'),
            self::entry(RentingBooking::class, 'Rentals / bookings', 'flux-admin.rentals.index', 'flux-admin.rentals.show', param: 'booking'),
            self::entry(FinanceApplication::class, 'Finance applications', 'flux-admin.finance.index', 'flux-admin.finance.show', 'flux-admin.finance.edit', 'application'),
            self::entry(PcnCase::class, 'PCN cases', 'flux-admin.pcn.index', 'flux-admin.pcn.show', 'flux-admin.pcn.edit', 'pcnCase'),
            self::entry(ClubMember::class, 'Club members', 'flux-admin.club.index', 'flux-admin.club.show', 'flux-admin.club.edit', 'clubMember'),
            self::entry(User::class, 'Users', 'flux-admin.users.index', 'flux-admin.users.show', 'flux-admin.users.edit', 'user'),
            self::entry(Role::class, 'Roles', 'flux-admin.roles.index', edit: 'flux-admin.roles.edit', param: 'role'),
            self::entry(Permission::class, 'Permissions', 'flux-admin.permissions.index'),
            self::entry(IpRestriction::class, 'IP restrictions', 'flux-admin.ip-restrictions.index', edit: 'flux-admin.ip-restrictions.edit', param: 'ipRestriction'),
            self::entry(AccessLog::class, 'Access logs', 'flux-admin.access-logs.index'),
            self::entry(Branch::class, 'Branches', 'flux-admin.branches.index', 'flux-admin.branches.show', param: 'branch'),
            self::entry(MotorbikesSale::class, 'Vehicle sales', 'flux-admin.motorbike-sales.index', edit: 'flux-admin.motorbike-sales.edit', param: 'motorbikesSale'),
            self::entry(NewMotorbike::class, 'New arrivals', 'flux-admin.motorbike-new.index', edit: 'flux-admin.motorbike-new.edit', param: 'newMotorbike'),
            self::entry(Motorcycle::class, 'For-sale catalogue', 'flux-admin.motorbike-for-sale.index', edit: 'flux-admin.motorbike-for-sale.edit', param: 'motorcycle'),
            self::entry(MotorbikeRepair::class, 'Repairs', 'flux-admin.motorbike-repairs.index', edit: 'flux-admin.motorbike-repairs.edit', param: 'motorbikeRepair'),
            self::entry(MotorbikeRepairUpdate::class, 'Repair updates', 'flux-admin.motorbike-repair-updates.index'),
            self::entry(MotorbikeAnnualCompliance::class, 'MOT / TAX compliance', 'flux-admin.motorbike-compliance.index', edit: 'flux-admin.motorbike-compliance.edit', param: 'compliance'),
            self::entry(MotorbikeCatB::class, 'Category B', 'flux-admin.motorbike-cat-b.index', edit: 'flux-admin.motorbike-cat-b.edit', param: 'motorbikeCatB'),
            self::entry(MotorbikeDeliveryOrderEnquiry::class, 'Delivery enquiries', 'flux-admin.delivery-enquiries.index', edit: 'flux-admin.delivery-enquiries.edit', param: 'deliveryEnquiry'),
            self::entry(RecoveredMotorbike::class, 'Recovered motorbikes', 'flux-admin.recovered-motorbikes.index', edit: 'flux-admin.recovered-motorbikes.edit', param: 'recoveredMotorbike'),
            self::entry(FinanceApplicationItem::class, 'Application items', 'flux-admin.application-items.index', edit: 'flux-admin.application-items.edit', param: 'applicationItem'),
            self::entry(ContractExtraItem::class, 'Contract extras', 'flux-admin.contract-extra-items.index', edit: 'flux-admin.contract-extra-items.edit', param: 'contractExtraItem'),
            self::entry(ContractAccess::class, 'Contract access', 'flux-admin.contract-access.index', edit: 'flux-admin.contract-access.edit'),
            self::entry(AgreementAccess::class, 'Agreement access', 'flux-admin.agreement-access.index', edit: 'flux-admin.agreement-access.edit'),
            self::entry(RentingBookingInvoice::class, 'Booking invoices', 'flux-admin.booking-invoices.index', edit: 'flux-admin.booking-invoices.edit', param: 'bookingInvoice'),
            self::entry(PcnUpdate::class, 'PCN updates', 'flux-admin.pcn-updates.index', edit: 'flux-admin.pcn-updates.edit'),
            self::entry(PcnTolRequest::class, 'PCN TOL requests', 'flux-admin.pcn-tol-requests.index', edit: 'flux-admin.pcn-tol-requests.edit'),
            self::entry(ClubMemberPurchase::class, 'Club purchases', 'flux-admin.club-purchases.index'),
            self::entry(ClubMemberSpending::class, 'Club spendings', 'flux-admin.club-spending.index'),
            self::entry(ClubMemberSpendingPayment::class, 'Club spending payments', 'flux-admin.club-spending-payments.index'),
            self::entry(ClubMemberRedeem::class, 'Club redemptions', 'flux-admin.club-redemptions.index'),
            self::entry(CustomerAppointments::class, 'Customer appointments', 'flux-admin.customer-appointments.index', edit: 'flux-admin.customer-appointments.edit', param: 'customerAppointment'),
            self::entry(CustomerDocument::class, 'Customer documents', 'flux-admin.customer-documents.index'),
            self::entry(MOTBooking::class, 'MOT bookings', 'flux-admin.mot-bookings.index', edit: 'flux-admin.mot-bookings.edit', param: 'motBooking'),
            self::entry(ServiceBooking::class, 'Service bookings', 'flux-admin.service-bookings.index', edit: 'flux-admin.service-bookings.edit', param: 'serviceBooking'),
            self::entry(CompanyVehicle::class, 'Company vehicles', 'flux-admin.company-vehicles.index', edit: 'flux-admin.company-vehicles.edit', param: 'companyVehicle'),
            self::entry(VehicleNotification::class, 'Vehicle notifications', 'flux-admin.vehicle-notifications.index', edit: 'flux-admin.vehicle-notifications.edit', param: 'vehicleNotification'),
            self::entry(VehicleIssuance::class, 'Vehicle issuances', 'flux-admin.vehicle-issuances.index', edit: 'flux-admin.vehicle-issuances.edit', param: 'vehicleIssuance'),
            self::entry(ClaimMotorbike::class, 'Claims', 'flux-admin.motorbike-claims.index', edit: 'flux-admin.motorbike-claims.edit', param: 'claimMotorbike'),
            self::entry(VehicleDeliveryOrder::class, 'Delivery orders', 'flux-admin.vehicle-delivery-orders.index', edit: 'flux-admin.vehicle-delivery-orders.edit', param: 'vehicleDeliveryOrder'),
            self::entry(EcOrder::class, 'Online orders', 'flux-admin.ec-orders.index', edit: 'flux-admin.ec-orders.edit', param: 'ecOrder'),
            self::entry(DsOrder::class, 'DS orders', 'flux-admin.ds-orders.index', edit: 'flux-admin.ds-orders.edit', param: 'dsOrder'),
            self::entry(DsOrderItem::class, 'DS order items', 'flux-admin.ds-order-items.index', edit: 'flux-admin.ds-order-items.edit', param: 'dsOrderItem'),
            self::entry(NgnDigitalInvoice::class, 'Digital invoices', 'flux-admin.digital-invoices.index'),
            self::entry(NgnDigitalInvoiceItem::class, 'Invoice items', 'flux-admin.digital-invoice-items.index', edit: 'flux-admin.digital-invoice-items.edit', param: 'invoiceItem'),
            self::entry(NgnProduct::class, 'Inventory products', 'flux-admin.inventory-products.index', edit: 'flux-admin.inventory-products.edit', param: 'product'),
            self::entry(NgnBrand::class, 'Inventory brands', 'flux-admin.inventory-brands.index', edit: 'flux-admin.inventory-brands.edit', param: 'brand'),
            self::entry(\App\Models\NgnCategory::class, 'Inventory categories', 'flux-admin.inventory-categories.index', edit: 'flux-admin.inventory-categories.edit', param: 'category'),
            self::entry(\App\Models\NgnModel::class, 'Product models', 'flux-admin.inventory-models.index', edit: 'flux-admin.inventory-models.edit', param: 'inventoryModel'),
            self::entry(NgnPartner::class, 'B2B partners', 'flux-admin.inventory-partners.index', edit: 'flux-admin.inventory-partners.edit', param: 'partner'),
            self::entry(StockMovement::class, 'Inventory stock movements', 'flux-admin.inventory-stock-movements.index'),
            self::entry(OxfordProduct::class, 'Oxford products', 'flux-admin.oxford-products.index'),
            self::entry(PurchaseRequest::class, 'Purchase requests', 'flux-admin.purchase-requests.index', edit: 'flux-admin.purchase-requests.edit', param: 'purchaseRequest'),
            self::entry(PurchaseRequestItem::class, 'Purchase request items', 'flux-admin.purchase-request-items.index', edit: 'flux-admin.purchase-request-items.edit', param: 'purchaseRequestItem'),
            self::entry(SpPart::class, 'Spare parts', 'flux-admin.sp-parts.index', edit: 'flux-admin.sp-parts.edit', param: 'spPart'),
            self::entry(SpMake::class, 'SP makes', 'flux-admin.sp-makes.index', edit: 'flux-admin.sp-makes.edit', param: 'spMake'),
            self::entry(SpModel::class, 'SP models', 'flux-admin.sp-models.index', edit: 'flux-admin.sp-models.edit', param: 'spModel'),
            self::entry(SpFitment::class, 'SP fitments', 'flux-admin.sp-fitments.index', edit: 'flux-admin.sp-fitments.edit', param: 'spFitment'),
            self::entry(SpAssembly::class, 'SP assemblies', 'flux-admin.sp-assemblies.index', edit: 'flux-admin.sp-assemblies.edit', param: 'spAssembly'),
            self::entry(SpAssemblyPart::class, 'SP assembly parts', 'flux-admin.sp-assembly-parts.index'),
            self::entry(SpStockMovement::class, 'SP stock movements', 'flux-admin.sp-stock-movements.index'),
            self::entry(BlogPost::class, 'Blog posts', 'flux-admin.blog-posts.index', edit: 'flux-admin.blog-posts.edit', param: 'blogPost'),
            self::entry(BlogCategory::class, 'Blog categories', 'flux-admin.blog-categories.index', edit: 'flux-admin.blog-categories.edit', param: 'blogCategory'),
            self::entry(BlogTag::class, 'Blog tags', 'flux-admin.blog-tags.index', edit: 'flux-admin.blog-tags.edit', param: 'blogTag'),
            self::entry(NgnSurvey::class, 'Surveys', 'flux-admin.surveys.index', edit: 'flux-admin.surveys.edit', param: 'survey'),
            self::entry(NgnSurveyQuestion::class, 'Survey questions', 'flux-admin.survey-questions.index', edit: 'flux-admin.survey-questions.edit', param: 'surveyQuestion'),
            self::entry(NgnSurveyOption::class, 'Survey options', 'flux-admin.survey-options.index', edit: 'flux-admin.survey-options.edit', param: 'surveyOption'),
            self::entry(NgnSurveyResponse::class, 'Survey responses', 'flux-admin.survey-responses.index'),
            self::entry(NgnSurveyAnswer::class, 'Survey answers', 'flux-admin.survey-answers.index'),
            self::entry(ContactQuery::class, 'Contact queries', 'flux-admin.contact-queries.index', edit: 'flux-admin.contact-queries.edit', param: 'contactQuery'),
            self::entry(NgnCareer::class, 'Careers', 'flux-admin.careers.index', edit: 'flux-admin.careers.edit', param: 'career'),
            self::entry(EmployeeSchedule::class, 'Staff schedules', 'flux-admin.employee-schedules.index', edit: 'flux-admin.employee-schedules.edit', param: 'employeeSchedule'),
            self::entry(Calander::class, 'Calendar', 'flux-admin.calendar.index', edit: 'flux-admin.calendar.edit', param: 'calendarEvent'),
            self::entry(SupportConversation::class, 'Support conversations', 'flux-admin.support-conversations.index', edit: 'flux-admin.support-conversations.edit', param: 'supportConversation'),
            self::entry(SupportMessage::class, 'Support messages', 'flux-admin.support-messages.index', edit: 'flux-admin.support-messages.edit', param: 'supportMessage'),
            self::entry(JudopaySubscription::class, 'Judo subscriptions', 'flux-admin.judopay-subscriptions.index', edit: 'flux-admin.judopay-subscriptions.edit'),
            self::entry(JudopayMitQueue::class, 'Judo MIT queue', 'flux-admin.judopay-mit-queue.index', edit: 'flux-admin.judopay-mit-queue.edit'),
            self::entry(NgnMitQueue::class, 'NGN MIT queue', 'flux-admin.ngn-mit-queue.index', edit: 'flux-admin.ngn-mit-queue.edit'),
            self::entry(UploadDocumentAccess::class, 'Document upload links', 'flux-admin.upload-document-links.index', edit: 'flux-admin.upload-document-links.edit'),
            self::entry(RentingServiceVideo::class, 'Service videos', 'flux-admin.service-videos.index', edit: 'flux-admin.service-videos.edit', param: 'serviceVideo'),
            self::entry(RentingPricing::class, 'Renting pricing', 'flux-admin.renting-pricing.index', edit: 'flux-admin.renting-pricing.edit'),
        ];
    }

    public static function count(): int
    {
        return count(self::resources());
    }

    /** @return array<string, mixed> */
    private static function entry(
        string $model,
        string $label,
        string $index,
        ?string $show = null,
        ?string $edit = null,
        string $param = 'id',
    ): array {
        return compact('model', 'label', 'index', 'show', 'edit', 'param');
    }
}
