{{-- resources/views/frontend/ngnstore/user_panel/partials/sidebar.blade.php --}}
<div class="account-sidebar">
    <ul class="account-sidebar-ul">
        <h5 style="font-family:var(--paragraphs-font-family) !important;">My Orders</h5>
        <li class="{{ request()->routeIs('userpanel_orders') ? 'active' : '' }}">
            <a href="{{ route('userpanel_orders') }}">Orders</a>
        </li>
        <li class="{{ request()->routeIs('userpanel_payment_methods') ? 'active' : '' }}">
            <a href="{{ route('userpanel_payment_methods') }}">Payment Methods</a>
        </li>
    </ul>
    <ul class="account-sidebar-ul">
        <h5 style="font-family:var(--paragraphs-font-family) !important;">My Account</h5>
        <li class="{{ request()->routeIs('userpanel_profile') ? 'active' : '' }}">
            <a href="{{ route('userpanel_profile') }}">Profile</a>
        </li>
        <li class="{{ request()->routeIs('userpanel_change_password') ? 'active' : '' }}">
            <a href="{{ route('userpanel_change_password') }}">Change Password</a>
        </li>
        <li class="{{ request()->routeIs('userpanel_logout') ? 'active' : '' }}">
            <a href="{{ route('userpanel_logout') }}">Logout</a>
        </li>
    </ul>
</div>
