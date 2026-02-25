{{-- DASHBOARD --}}
<x-backpack::menu-item title="Dashboard" icon="la la-home" :link="backpack_url('dashboard')" />

@can('see-menu-ecommerce')
<x-backpack::menu-item title="ONLINE STORE" icon="la la-shopping-cart" :link="backpack_url('ec-order')" />
@endcan

{{-- FINANCE --}}
@can('see-menu-finance')
<x-backpack::menu-dropdown title="FINANCE" icon="las la-chevron-circle-down">
    <x-backpack::menu-dropdown-item title="Create New / Edit" icon="la la-user" :link="backpack_url('finance-application')" />
    <x-backpack::menu-item title="Signature Expire Date" icon="la la-external-link-alt" :link="backpack_url('contract-access')" />
</x-backpack::menu-dropdown>
@endcan

{{-- RENTALS --}}
@can('see-menu-rentals')
<x-backpack::menu-dropdown title="RENTALS" icon="las la-chevron-circle-down">
    <x-backpack::menu-dropdown-item title="Add New Contract" icon="la la-plus" :link="backpack_url('../admin/renting/bookings/new')" />
    <x-backpack::menu-dropdown-item title="Rental Management" icon="la" :link="backpack_url('../admin/renting/bookings')" />
    <x-backpack::menu-item title="Add New Vehicle" icon="las la-money-bill" :link="backpack_url('renting-pricing')" />
    <x-backpack::menu-item title="Document Expire Date" icon="las la-external-link-alt" :link="backpack_url('upload-document-access')" />
    <x-backpack::menu-item title="Signature Expire Date" icon="la la-puzzle-piece" :link="backpack_url('agreement-access')" />
    <x-backpack::menu-item title="Terminate / Generate Link" icon="las la-external-link-alt" :link="backpack_url('rental-terminate-access')" />
    <x-backpack::menu-item title="Overview" icon="la la-motorcycle" :link="backpack_url('active_renting')" />
    <x-backpack::menu-item title="Renting service videos" icon="la la-question" :link="backpack_url('renting-service-video')" />
    <!-- <x-backpack::menu-item title="Rental Due Payments" icon="la la-question" :link="backpack_url('rental_due_payments')" /> -->
    <x-backpack::menu-item title="Adjust Booking Weekday" icon="la la-question" :link="backpack_url('adjust_booking_weekday')" />
    @role('Admin')
    <x-backpack::menu-item title="Active Bookings Summary" icon="la la-question" :link="backpack_url('active_renting_summary')"  />
    @endrole
</x-backpack::menu-dropdown>
@endcan

{{-- PCNs --}}
@can('see-menu-pcns')
<x-backpack::menu-dropdown title="PCNs" icon="las la-chevron-circle-down">
    <x-backpack::menu-dropdown-item title="Add / Edit" icon="las la-clipboard-list" :link="backpack_url('pcn-case')" />
    <x-backpack::menu-item title="Overview" icon="la la-chart-line" :link="backpack_url('pcn_page')" />
    <!-- <x-backpack::menu-dropdown-item title="PCN Cases Updates" icon="las la-clipboard-list" :link="backpack_url('pcn-case-update')" /> -->
</x-backpack::menu-dropdown>
@endcan

{{-- WORKSHOP --}}
@can('see-menu-services-and-repairs-and-report')
<x-backpack::menu-dropdown title="BOOK SERVICES / REPAIRS / REPORT" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit - SERVICES/REPAIRS BOOKING" icon="las la-calendar-check" :link="backpack_url('customer-appointments')" />
    <x-backpack::menu-item title="Add / Edit - REPAIRS REPORT" icon="la la-motorcycle" :link="backpack_url('motorbike-repair')" />
</x-backpack::menu-dropdown>
@endcan

{{-- MOT --}}
@can('see-menu-mot-bookings')
<x-backpack::menu-dropdown title="MOT" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit" icon="las la-radiation-alt" :link="backpack_url('mot-booking')" />
</x-backpack::menu-dropdown>
@endcan

{{-- CUSTOMERS --}}
@can('see-menu-commons')
<x-backpack::menu-dropdown title="CUSTOMERS" icon="las la-chevron-circle-down">
    <x-backpack::menu-dropdown-item title="Customer List" icon="la la-users" :link="backpack_url('customer')" />
    <x-backpack::menu-item title="Documents" icon="la la-file" :link="backpack_url('customer-document')" />
</x-backpack::menu-dropdown>
@endcan

{{-- B2B --}}
@can('see-menu-b2b')
<x-backpack::menu-dropdown title="B2B" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit" icon="las la-user-friends" :link="backpack_url('ngn-partner')" />
</x-backpack::menu-dropdown>
@endcan


{{-- INVENTORY --}}
@can('see-menu-inventory')
<x-backpack::menu-dropdown title="INVENTORY" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit" icon="la la-box-open" :link="backpack_url('ngn-product-management')" />
    <x-backpack::menu-item title="Stock Management" icon="la la-boxes" :link="backpack_url('ngn-inventory-management')" />
    
    <!-- additional -->
    <!-- <x-backpack::menu-dropdown-item title="Add / Edit" icon="la la-box" :link="backpack_url('ngn-product')" /> -->
    <!-- <x-backpack::menu-item title="Categories" icon="la la-tags" :link="backpack_url('ngn-category')" /> -->
    <!-- <x-backpack::menu-item title="Brands" icon="la la-industry" :link="backpack_url('ngn-brand')" /> -->
    <!-- <x-backpack::menu-item title="Products Stock Handlers" icon="la la-external-link-alt" :link="backpack_url('ngn-stock-handler')" />  -->
</x-backpack::menu-dropdown>
@endcan

{{-- Vehicles --}}
@can('see-menu-vehicles')
<x-backpack::menu-dropdown title="VEHICLES" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="DVLA Add / Edit" icon="la la-motorcycle" :link="backpack_url('../admin/renting/motorbikes/create')" />
    <x-backpack::menu-item title="Manual Add / Edit" icon="la la-motorcycle" :link="backpack_url('motorbikes')" /> 
    <x-backpack::menu-item title="MOT / TAX Override" icon="las la-exclamation-circle" :link="backpack_url('motorbike-annual-compliance-m')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="WEBSITE" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Vehicle Sale Add / Edit" icon="la la-motorcycle"
        :link="backpack_url('motorbikes-sale')" />
</x-backpack::menu-dropdown>


@endcan

@can('see-menu-claims')
<x-backpack::menu-dropdown title="CLAIMS" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit" icon="la la-motorcycle" :link="backpack_url('claim-motorbike')" />
</x-backpack::menu-dropdown>
@endcan


@can('see-menu-commons')
<x-backpack::menu-item title="Category B" icon="la la-motorcycle" :link="backpack_url('motorbike-cat-b')" />
<x-backpack::menu-item title="Club Member Vehicles Details" icon="la la-motorcycle" :link="backpack_url('clubmembers-details')" />
<!-- <x-backpack::menu-item title="Vehicle Database" icon="la la-motorcycle" :link="backpack_url('vehicle-database')" /> -->
<x-backpack::menu-item title="Vehicle History" icon="la-database" :link="backpack_url('motorbike-record-view')" />
<x-backpack::menu-item title="Company Vehicles" icon="la la-motorcycle" :link="backpack_url('company-vehicle')" />
<!-- <x-backpack::menu-item title="Recover" icon="las la-undo-alt" :link="backpack_url('recovered-motorbike')" /> -->


<!-- // purchase -->
<x-backpack::menu-dropdown title="PURCHASE" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Add / Edit" icon="la la-motorcycle" :link="backpack_url('used-vehicle-seller')" />
</x-backpack::menu-dropdown>



<x-backpack::menu-item title="Contact queries" icon="la la-question" :link="backpack_url('contact-query')" />
<x-backpack::menu-item title="M O T Stats Page" icon="la la-question" :link="backpack_url('mot_stats_page')" />
<x-backpack::menu-item title="Motorbike delivery order enquiries" icon="la la-question" :link="backpack_url('motorbike-delivery-order-enquiries')" />

<x-backpack::menu-item title="Careers" icon="la la-user" :link="backpack_url('ngn-career')" />

<x-backpack::menu-dropdown title="Blog Management" icon="las la-blog">
    <x-backpack::menu-item title="Blog posts" icon="las la-file-alt" :link="backpack_url('blog-post')" />
    <x-backpack::menu-item title="Blog categories" icon="las la-tags" :link="backpack_url('blog-category')" />
    <x-backpack::menu-item title="Blog tags" icon="las la-tag" :link="backpack_url('blog-tag')" />
</x-backpack::menu-dropdown>

<x-backpack::menu-dropdown title="Motorbike Preference Survey" icon="la la-question">
    <x-backpack::menu-item title="Survey Whatsapp Notifier" icon="la la-question" :link="backpack_url('ngn_survey_campaign/1')" />
    <x-backpack::menu-item title="Survey Results" icon="la la-question" :link="backpack_url('survey_responses/1')" />
    <x-backpack::menu-item title="Survey Campaign" icon="la la-question" :link="backpack_url('survey_index')" />
</x-backpack::menu-dropdown>
@endcan

<!-- // misc -->
@role('Admin')
<x-backpack::menu-separator title="MISC/Experiments" />

<x-backpack::menu-dropdown title="..." icon="las la-blog">

    <x-backpack::menu-dropdown title="Club Members" icon="la la-external-link-alt">
        <x-backpack::menu-dropdown-item title="Club members" :link="backpack_url('club-member')" />
        <x-backpack::menu-dropdown-item title="Club member purchases" :link="backpack_url('club-member-purchase')" />
        <x-backpack::menu-dropdown-item title="Club member redeems" :link="backpack_url('club-member-redeem')" />
        <x-backpack::menu-dropdown-item title="0% Spendings" :link="backpack_url('club-member-spending')" />

    </x-backpack::menu-dropdown>
    
    {{-- <x-backpack::menu-item title="Purchase requests" icon="las la-boxes" :link="backpack_url('purchase-request')" /> --}}
    <x-backpack::menu-item title="Purchase request items" icon="la la-external-link-alt" :link="backpack_url('purchase-request-item')" />

    <li class="nav-item"><a class="nav-link" href="/admin"><i class="la la-cog nav-icon"></i> Old Admin Panel </a></li>
    {{-- <x-backpack::menu-item title="List" icon="la la-motorcycle" :link="backpack_url('motorbike-list')" /> --}}

    <x-backpack::menu-item title="Vehicles Database" icon="la la-motorcycle" :link="backpack_url('motorbike-annual-compliance')" />
    <x-backpack::menu-item title="Customer documents" icon="la la-external-link-alt" :link="backpack_url('customer-document')" />
    <x-backpack::menu-item title="Vehicle issuances" icon="la la-motorcycle" :link="backpack_url('vehicle-issuance')" />
    <!-- <x-backpack::menu-item title="New motorbikes for sales" icon="la la-question" :link="backpack_url('new-motorbikes-for-sale')" /> -->

    <x-backpack::menu-item title="Vehicle delivery orders" icon="la la-question" :link="backpack_url('vehicle-delivery-order')" />    
</x-backpack::menu-dropdown>
@endrole



{{-- IP RESTRICTIONS --}}
@can('see-menu-security')
<x-backpack::menu-separator title="Security" />
<x-backpack::menu-dropdown title="..." icon="la la-exclamation-circle">
    <x-backpack::menu-item title="Ip restrictions" icon="la la-question" :link="backpack_url('ip-restriction')" />
    <x-backpack::menu-item title="Access logs" icon="la la-question" :link="backpack_url('access-log')" />
    <!-- <x-backpack::menu-item title="AI Chat Agent" icon="la la-robot" :link="backpack_url('agent-settings')" /> -->
</x-backpack::menu-dropdown>
@endcan

@can('see-menu-permissions')
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user-shield" :link="backpack_url('user')" />
    <x-backpack::menu-item title="Roles" icon="la la-user-tag" :link="backpack_url('role')" />
    <x-backpack::menu-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
@endcan




<!-- Staff -->
@canany(['see-judopay-home', 'see-judopay'])
<x-backpack::menu-dropdown title="JUDO PAY" icon="las la-chevron-circle-down">
    <x-backpack::menu-item title="Judo Pay" icon="la la-question" :link="backpack_url('judopay')" />

    <x-backpack::menu-item title="MIT Dashboard" icon="la la-credit-card" :link="backpack_url('judopay/mit-dashboard')" />
 
    <x-backpack::menu-item title="Weekly Schedule" icon="la la-credit-card" :link="backpack_url('judopay/weekly-mit-queue')" />
 

</x-backpack::menu-dropdown>
@endcan




{{-- MOTORBIKES AVAILABLE --}}
<!-- @can('see-menu-motorbikes-available') -->
<!-- <x-backpack::menu-dropdown title="MOTORBIKES AVAILABLE" icon="las la-chevron-circle-down"> -->
    <!-- <x-backpack::menu-item title="Add / Edit" icon="la la-motorcycle" :link="backpack_url('motorbike-available')" /> -->
<!-- </x-backpack::menu-dropdown> -->
<!-- @endcan -->


{{-- REPORTS --}}
<!-- <x-backpack::menu-dropdown title="REPORTS" icon="las la-chevron-circle-down"> -->
<!--     <x-backpack::menu-item title="NGN Club dashboard" icon="la la-user-friends" :link="backpack_url('ngn_club')" /> -->
<!--     <x-backpack::menu-item title="Employee schedules" icon="las la-user-check" :link="backpack_url('employee-schedule')" /> -->
<!-- <x-backpack::menu-item title="Vehicle Database" icon="la la-motorcycle" :link="backpack_url('motorbike-annual-compliance')" /> -->
<!--     {{-- <x-backpack::menu-item title="Calanders" icon="las la-address-book" :link="backpack_url('calander')" /> --}} -->
<!-- <x-backpack::menu-item title="Vehicle notifications" icon="las la-mail-bulk" :link="backpack_url('vehicle-notification')" /> -->
<!-- </x-backpack::menu-dropdown> -->
<!-- <x-backpack::menu-item title="Mot checkers" icon="la la-external-link-alt" :link="backpack_url('mot-checker')" /> -->
<!-- <x-backpack::menu-item title="Ngn renting bookings" icon="la la-external-link-alt" :link="backpack_url('ngn-renting-booking')" />  -->

<!-- <x-backpack::menu-item title="branches" icon="la la-external-link-alt" :link="backpack_url('branch')" />  -->
<!-- <x-backpack::menu-item title="New motorbikes for sales" icon="la la-question" :link="backpack_url('new-motorbikes-for-sale')" /> -->


<x-backpack::menu-item title="Ebike Manager" icon="la la-question" :link="backpack_url('ebike_manager')" />
<!-- <x-backpack::menu-item title="Club member spending payments" icon="la la-question" :link="backpack_url('club-member-spending-payment')" /> -->