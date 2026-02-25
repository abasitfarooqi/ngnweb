// File: resources/js/router/routes.js
import ShopHome from '@/views/Shop/ShopHome.vue';
import ProductDetail from '@/views/Shop/ProductDetail.vue';
import CategoryList from '@/views/Shop/CategoryList.vue';
import BrandList from '@/views/Shop/BrandList.vue';
import ProductPage from '@/views/Shop/ProductPage.vue';
import BasketPage from '@/views/Shop/BasketPage.vue';
import DetailsPage from '@/views/Shop/DetailsPage.vue';
import CheckoutPage from '@/views/Shop/CheckoutPage.vue';
import PaymentPage from '@/views/Shop/PaymentPage.vue';
import Login from '@/components/account/Login.vue';
import Register from '@/components/account/Register.vue';
import NotFound from '@/components/common/NotFound.vue';
// Import accountinformation components
import PaymentMethodManage from '@/views/accountinformation/PaymentMethodManage.vue';
import PaymentMethodsIndex from '@/views/accountinformation/PaymentMethodsIndex.vue';
import AddressIndex from '@/views/accountinformation/AddressIndex.vue';
import ChangePassword from '@/views/accountinformation/ChangePassword.vue';
import OrderDetails from '@/views/accountinformation/OrderDetails.vue';
import OrdersIndex from '@/views/accountinformation/OrdersIndex.vue';
import ProfileIndex from '@/views/accountinformation/ProfileIndex.vue';
import ForgotPassword from '@/views/accountinformation/ForgotPassword.vue';
import ResetPassword from '@/views/accountinformation/ResetPassword.vue';
import Dashboard from '@/views/accountinformation/Dashbaord.vue';

// Legal and policies routes
import ReturnPolicy from '@/views/Legals/ReturnPolicy.vue';
import RefundPolicy from '@/views/Legals/RefundPolicy.vue';
import WarrantyClaimPolicy from '@/views/Legals/WarrantyClaimPolicy.vue';
import DamagedGoodsPolicy from '@/views/Legals/DamagedGoodsPolicy.vue';
import PrivacyPolicy from '@/views/Legals/PrivacyPolicy.vue';
import CookiePolicy from '@/views/Legals/CookiePolicy.vue';
import ShippingPolicy from '@/views/Legals/ShippingPolicy.vue';
import CancellationPolicy from '@/views/Legals/CancellationPolicy.vue';
import TermsConditions from '@/views/Legals/TermsConditions.vue';
import PaymentPolicy from '@/views/Legals/PaymentPolicy.vue';
import UserAgreement from '@/views/Legals/UserAgreement.vue';
import AccessibilityStatement from '@/views/Legals/AccessibilityStatement.vue';
import DataProtectionAgreement from '@/views/Legals/DataProtectionAgreement.vue';
import ClickCollect from '@/views/Legals/ClickCollect.vue';
import FrequentlyAskedQuestions from '@/views/Legals/FrequentlyAskedQuestions.vue';

// Blog routes
import BlogPostList from '@/views/Blog/BlogPostList.vue';
import BlogPostDetail from '@/views/Blog/BlogPostDetail.vue';

// Service Enquiry Form
import ServiceEnquiryForm from '@/views/Blog/ServiceEnquiryForm.vue';

const routes = [
  {
    path: '/shop',
    component: { template: '<router-view></router-view>' },
    children: [
      {
        path: '',
        name: 'ShopHome',
        component: ShopHome,
      },
      // id (NO SLUG)
      {
        path: 'p/:id',
        name: 'ProductDetail',
        component: ProductDetail,
        props: true,
      },
      {
        path: 'categories',
        name: 'CategoryList',
        component: CategoryList,
      },
      {
        path: 'brands',
        name: 'BrandList',
        component: BrandList,
      },
      // SLUG
      {
        path: 'product/:slug',
        name: 'ProductPage',
        component: ProductPage,
        props: true,
      },
      {
        path: 'basket',
        name: 'BasketPage',
        component: BasketPage,
      },
      {
        path: 'checkout',
        name: 'CheckoutPage',
        component: CheckoutPage,
        meta: {
          requiresAuth: true,
          restoreCheckoutData: true
        },
      },
      {
        path: 'details',
        name: 'DetailsPage',
        component: DetailsPage,
        meta: { requiresAuth: true },
      },
      {
        path: 'payment',
        name: 'PaymentPage',
        component: PaymentPage,
      },
      // Legal Pages Routes
      {
        path: '/legals/return-policy',
        name: 'ReturnPolicy',
        component: ReturnPolicy,
      },
      {
        path: '/legals/refund-policy',
        name: 'RefundPolicy',
        component: RefundPolicy,
      },
      {
        path: '/legals/warranty-claim-policy',
        name: 'WarrantyClaimPolicy',
        component: WarrantyClaimPolicy,
      },
      {
        path: '/legals/damaged-goods-policy',
        name: 'DamagedGoodsPolicy',
        component: DamagedGoodsPolicy,
      },
      {
        path: '/legals/privacy-policy',
        name: 'PrivacyPolicy',
        component: PrivacyPolicy,
      },
      {
        path: '/legals/cookie-policy',
        name: 'CookiePolicy',
        component: CookiePolicy,
      },
      {
        path: '/legals/shipping-policy',
        name: 'ShippingPolicy',
        component: ShippingPolicy,
      },
      {
        path: '/legals/cancellation-policy',
        name: 'CancellationPolicy',
        component: CancellationPolicy,
      },
      {
        path: '/legals/terms-conditions',
        name: 'TermsConditions',
        component: TermsConditions,
      },
      {
        path: '/legals/payment-policy',
        name: 'PaymentPolicy',
        component: PaymentPolicy,
      },
      {
        path: '/legals/user-agreement',
        name: 'UserAgreement',
        component: UserAgreement,
      },
      {
        path: '/legals/accessibility-statement',
        name: 'AccessibilityStatement',
        component: AccessibilityStatement,
      },
      {
        path: '/legals/data-protection-agreement',
        name: 'DataProtectionAgreement',
        component: DataProtectionAgreement,
      },
      {
        path: '/legals/click-collect',
        name: 'ClickCollect',
        component: ClickCollect,
      },
      {
        path: '/legals/frequently-asked-questions',
        name: 'FrequentlyAskedQuestions',
        component: FrequentlyAskedQuestions,
      },
      // Blog routes
      {
        path: 'blog',
        name: 'BlogPostList',
        component: BlogPostList,
      },
      {
        path: 'blog/:slug',
        name: 'BlogPostDetail',
        component: BlogPostDetail,
        props: true,
      },
      // Service Enquiry Form
      {
        path: 'blog/book-service',
        name: 'ServiceEnquiryForm',
        component: ServiceEnquiryForm,
      },
      // Shop 404
      {
        path: ':pathMatch(.*)*',
        name: 'ShopNotFound',
        component: NotFound,
      },
    ]
  },
  {
    path: '/accountinformation',
    component: { template: '<router-view></router-view>' },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/profile',
        name: 'ProfileIndex',
        component: ProfileIndex,
        meta: { requiresAuth: true },
      },
      // Account Information Routes
      {
        path: '/accountinformation/orders',
        name: 'OrdersIndex',
        component: OrdersIndex,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/orders/:id',
        name: 'OrderDetails',
        component: OrderDetails,
        props: true,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/addresses',
        name: 'AddressIndex',
        component: AddressIndex,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/payment-methods',
        name: 'PaymentMethodsIndex',
        component: PaymentMethodsIndex,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/payment-methods/:id',
        name: 'PaymentMethodManage',
        component: PaymentMethodManage,
        props: true,
        meta: { requiresAuth: true },
      },
      {
        path: '/accountinformation/change-password',
        name: 'ChangePassword',
        component: ChangePassword,
        meta: { requiresAuth: true },
      },
      {
        path: 'login',
        name: 'Login',
        component: Login,
        meta: { requiresGuest: true },
      },
      {
        path: 'register',
        name: 'Register',
        component: Register,
        meta: { requiresGuest: true },
      },
      {
        path: 'forgotpassword',
        name: 'ForgotPassword',
        component: ForgotPassword,
        meta: { requiresGuest: true },
      },
      {
        path: 'reset-password/:token',
        name: 'ResetPassword',
        component: ResetPassword,
        meta: { requiresGuest: true },
      },
      {
        path: ':pathMatch(.*)*',
        name: 'AccountNotFound',
        component: NotFound,
      },
    ]
  },
  // General 404 Route (should be the last route)
  {
    path: '/:pathMatch(.*)*',
    name: 'GeneralNotFound',
    component: NotFound,
  },
];

export default routes;
