<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Legal;
use App\Models\Motorbike;
use App\Models\NgnCareer;
use App\Models\NgnPartner;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MobileExperienceController extends Controller
{
    public function pageManifest(): JsonResponse
    {
        return response()->json([
            'site_templates' => $this->collectTemplates(resource_path('views/livewire/site')),
            'portal_templates' => $this->collectTemplates(resource_path('views/livewire/portal')),
            'shop_templates' => $this->collectTemplates(resource_path('views/livewire/shop')),
            'auth_templates' => $this->collectTemplates(resource_path('views/livewire/auth')),
            'agreements_templates' => $this->collectTemplates(resource_path('views/livewire/agreements')),
            'components_templates' => $this->collectTemplates(resource_path('views/livewire/components')),
            'v2_templates' => $this->collectTemplates(resource_path('views/livewire/v2')),
            'recommended_api_blocks' => [
                'catalogue' => ['/api/v1/mobile/bikes', '/api/v1/mobile/rentals', '/api/v1/mobile/shop/products'],
                'spareparts_full_flow' => [
                    '/api/v1/mobile/spare-parts/manufacturers',
                    '/api/v1/mobile/spare-parts/models/{manufacturer}',
                    '/api/v1/mobile/spare-parts/years/{manufacturer}/{model}',
                    '/api/v1/mobile/spare-parts/countries/{manufacturer}/{model}/{year}',
                    '/api/v1/mobile/spare-parts/colours/{manufacturer}/{model}/{year}/{country}',
                    '/api/v1/mobile/spare-parts/assemblies/{manufacturer}/{model}/{year}/{country}/{colour}',
                    '/api/v1/mobile/spare-parts/parts/{manufacturer}/{model}/{year}/{country}/{colour}/{assembly}',
                    '/api/v1/mobile/spare-parts/part/{partNumber}',
                ],
                'portal' => [
                    '/api/v1/mobile/portal/overview',
                    '/api/v1/mobile/portal/orders',
                    '/api/v1/mobile/portal/rentals',
                    '/api/v1/mobile/portal/mot-bookings',
                    '/api/v1/mobile/portal/recovery-requests',
                    '/api/v1/mobile/enquiries',
                    '/api/v1/customer/support/conversations',
                ],
            ],
        ]);
    }

    public function experienceBlueprint(): JsonResponse
    {
        return response()->json([
            'presentation_rules' => [
                [
                    'key' => 'shop_vs_spareparts',
                    'title' => 'Shop and spare parts must stay separate',
                    'details' => [
                        'shop_ui' => 'Retail merchandising grid and category hubs.',
                        'spareparts_ui' => 'Technical parts finder with fitment path and assembly breakdown.',
                        'shared_checkout' => 'Both feed shared checkout/order backend.',
                    ],
                ],
                [
                    'key' => 'bikes_and_ebikes',
                    'title' => 'Bikes and e-bikes have different content depth',
                    'details' => [
                        'bikes' => 'Listing-first experience with used/new split and detail pages.',
                        'ebikes' => 'Editorial + FAQ + specification table + enquiry section.',
                    ],
                ],
                [
                    'key' => 'services_modules',
                    'title' => 'Service modules require distinct UX blocks',
                    'details' => [
                        'mot' => 'Checker + alerts + booking CTA.',
                        'repairs' => 'Basic/full/comparison + enquiry.',
                        'recovery' => 'Request form with branch destination data.',
                        'rentals' => 'Quote-led enquiry flow.',
                    ],
                ],
            ],
            'form_schemas' => [
                [
                    'key' => 'service_booking_universal',
                    'origin' => 'site.contact.service-booking',
                    'fields' => [
                        ['name' => 'serviceType', 'required' => true, 'type' => 'enum'],
                        ['name' => 'selectedBranch', 'required' => false, 'type' => 'integer'],
                        ['name' => 'regNo', 'required' => false, 'type' => 'string'],
                        ['name' => 'make', 'required' => false, 'type' => 'string'],
                        ['name' => 'model', 'required' => false, 'type' => 'string'],
                        ['name' => 'name', 'required' => true, 'type' => 'string'],
                        ['name' => 'phone', 'required' => true, 'type' => 'string'],
                        ['name' => 'email', 'required' => false, 'type' => 'email'],
                        ['name' => 'preferredDate', 'required' => 'conditional', 'type' => 'date'],
                        ['name' => 'preferredTime', 'required' => 'conditional', 'type' => 'HH:MM'],
                        ['name' => 'message', 'required' => false, 'type' => 'string'],
                    ],
                ],
                [
                    'key' => 'v2_service_booking_form',
                    'origin' => 'v2.service-booking-form',
                    'fields' => [
                        ['name' => 'full_name', 'required' => true, 'type' => 'string', 'max' => 100],
                        ['name' => 'phone', 'required' => true, 'type' => 'string', 'max' => 20],
                        ['name' => 'email', 'required' => true, 'type' => 'email', 'max' => 150],
                        ['name' => 'reg_no', 'required' => true, 'type' => 'string', 'max' => 20],
                        ['name' => 'make_model', 'required' => true, 'type' => 'string', 'max' => 100],
                        ['name' => 'service_type', 'required' => true, 'type' => 'enum', 'values' => ['basic', 'full', 'repairs', 'other']],
                        ['name' => 'preferred_date', 'required' => true, 'type' => 'date', 'rule' => 'after:today'],
                        ['name' => 'notes', 'required' => false, 'type' => 'string', 'max' => 500],
                    ],
                ],
            ],
        ]);
    }

    public function fullAppMap(): JsonResponse
    {
        return response()->json([
            'website' => [
                'bikes' => ['/api/v1/mobile/bikes', '/api/v1/mobile/bikes/new/{id}', '/api/v1/mobile/bikes/used/{id}'],
                'ebikes' => ['/api/v1/mobile/ebikes/experience'],
                'rentals' => ['/api/v1/mobile/rentals'],
                'services' => ['/api/v1/mobile/services'],
                'shop' => ['/api/v1/mobile/shop/products', '/api/v1/mobile/shop/filters'],
                'spareparts' => [
                    '/api/v1/mobile/spare-parts',
                    '/api/v1/mobile/spare-parts/manufacturers',
                    '/api/v1/mobile/spare-parts/models/{manufacturer}',
                    '/api/v1/mobile/spare-parts/years/{manufacturer}/{model}',
                    '/api/v1/mobile/spare-parts/countries/{manufacturer}/{model}/{year}',
                    '/api/v1/mobile/spare-parts/colours/{manufacturer}/{model}/{year}/{country}',
                    '/api/v1/mobile/spare-parts/assemblies/{manufacturer}/{model}/{year}/{country}/{colour}',
                    '/api/v1/mobile/spare-parts/parts/{manufacturer}/{model}/{year}/{country}/{colour}/{assembly}',
                    '/api/v1/mobile/spare-parts/part/{partNumber}',
                ],
                'careers' => ['/api/v1/mobile/careers', '/api/v1/mobile/careers/{id}'],
                'partners' => ['/api/v1/mobile/partners'],
                'legal' => ['/api/v1/mobile/legal/pages', '/api/v1/mobile/legal/pages/{slug}'],
                'blog' => ['/api/v1/mobile/blog/posts', '/api/v1/mobile/blog/posts/{slug}'],
                'reviews' => ['/api/v1/mobile/reviews'],
                'auth' => ['/api/v1/mobile/auth/blueprint'],
            ],
            'portal' => [
                'dashboard' => ['/api/v1/mobile/portal/overview'],
                'orders' => ['/api/v1/mobile/portal/orders'],
                'bookings' => ['/api/v1/mobile/portal/rentals', '/api/v1/mobile/portal/mot-bookings', '/api/v1/mobile/portal/recovery-requests'],
                'enquiries' => ['/api/v1/mobile/enquiries', '/api/v1/mobile/enquiries/{id}', '/api/v1/mobile/enquiries (POST)'],
                'addresses' => ['/api/v1/mobile/portal/addresses*'],
                'documents' => ['/api/v1/mobile/portal/documents', '/api/v1/mobile/portal/documents/upload'],
                'payments' => ['/api/v1/mobile/portal/payments/recurring'],
                'repairs' => ['/api/v1/mobile/portal/repairs/appointment/options', '/api/v1/mobile/portal/repairs/appointments'],
                'rentals_create' => [
                    '/api/v1/mobile/portal/rentals/browse/options',
                    '/api/v1/mobile/portal/rentals/available',
                    '/api/v1/mobile/portal/rentals/create/{motorbikeId}/blueprint',
                    '/api/v1/mobile/portal/rentals/create/{motorbikeId}',
                ],
                'support' => ['/api/v1/customer/support/*', '/api/v1/staff/support/*'],
            ],
            'contracts_and_agreements' => [
                'signed_documents_available_via' => '/api/v1/mobile/portal/documents',
                'tables' => ['customer_agreements', 'customer_contracts', 'customer_documents'],
            ],
        ]);
    }

    public function dbLinkMap(): JsonResponse
    {
        return response()->json([
            'commerce' => [
                'chains' => [
                    'ngn_products -> ec_order_items -> ec_orders',
                    'sp_makes -> sp_models -> sp_fitments -> sp_assemblies -> sp_assembly_parts -> sp_parts',
                    'sp_parts checkout still writes into ec_order_items/ec_orders',
                ],
            ],
            'enquiry_and_chat' => [
                'chains' => [
                    'service_bookings.customer_auth_id -> customer_auths.id',
                    'service_bookings.conversation_id -> support_conversations.id',
                    'support_conversations.id -> support_messages.conversation_id',
                    'support_messages.id -> support_attachments.message_id',
                ],
            ],
            'portal' => [
                'chains' => [
                    'customer_auths.customer_id -> customers.id',
                    'customer_addresses.customer_id -> customers.id',
                    'customer_documents.customer_id -> customers.id',
                    'customer_agreements.customer_id -> customers.id',
                    'customer_contracts.customer_id -> customers.id',
                    'renting_bookings.customer_id -> customers.id',
                    'renting_booking_items.booking_id -> renting_bookings.id',
                    'mot_bookings matched by customer email',
                    'motorbike_delivery_order_enquiries matched by customer email/phone',
                ],
            ],
            'content' => [
                'chains' => [
                    'blog_posts.category_id -> blog_categories.id',
                    'blog_images.blog_post_id -> blog_posts.id',
                    'legals table used for legal content pages',
                    'ngn_careers table used for careers pages',
                    'ngn_partners table used for partner listings',
                    'reviews table used for social proof feeds',
                ],
            ],
        ]);
    }

    public function ebikesExperience(): JsonResponse
    {
        $items = Motorbike::query()
            ->with('images:id,motorbike_id,image_path')
            ->where('is_ebike', true)
            ->orderByDesc('id')
            ->limit(60)
            ->get()
            ->map(function (Motorbike $row) {
                $firstImage = $row->images->first();
                $imagePath = $firstImage?->image_path;

                return [
                    'id' => $row->id,
                    'name' => trim((string) $row->make.' '.$row->model),
                    'make' => $row->make,
                    'model' => $row->model,
                    'year' => $row->year,
                    'engine' => $row->engine,
                    'price_hint' => null,
                    'reg_hint' => $row->reg_no ? '****'.substr((string) $row->reg_no, -3) : null,
                    'image_url' => $imagePath ? Storage::url($imagePath) : null,
                ];
            });

        return response()->json([
            'items' => $items,
            'editorial_blocks' => [
                ['key' => 'intro', 'title' => 'Electric mobility', 'body' => 'Dedicated e-bike section with editorial content and technical details.'],
                ['key' => 'why_ebike', 'title' => 'Why e-bike', 'body' => 'Low running cost, city friendly, practical commuting focus.'],
            ],
            'specifications_table_rows' => [
                ['field' => 'Motor', 'value' => 'Model specific'],
                ['field' => 'Range', 'value' => 'Model specific'],
                ['field' => 'Charge time', 'value' => 'Model specific'],
                ['field' => 'Top speed', 'value' => 'Model specific'],
            ],
            'faq' => [
                ['q' => 'Can I finance an e-bike?', 'a' => 'Yes, use finance enquiry flow with bike context.'],
                ['q' => 'Do you support test ride enquiries?', 'a' => 'Yes, use the enquiry module with `module=ebike`.'],
            ],
            'cta' => [
                'label' => 'Send e-bike enquiry',
                'mobile_enquiry_endpoint' => '/api/v1/mobile/enquiries',
                'module' => 'ebike',
            ],
            'ui_contract' => [
                'has_editorial_blocks' => true,
                'has_specification_table' => true,
                'has_enquiry_form' => true,
                'recommended_enquiry_module' => 'ebike',
            ],
        ]);
    }

    public function careers(): JsonResponse
    {
        $rows = NgnCareer::query()
            ->orderByDesc('job_posted')
            ->get(['id', 'job_title', 'employment_type', 'location', 'salary', 'contact_email', 'job_posted', 'expire_date']);

        return response()->json(['data' => $rows]);
    }

    public function careerDetail(int $id): JsonResponse
    {
        $row = NgnCareer::query()->findOrFail($id);

        return response()->json(['data' => $row]);
    }

    public function partners(): JsonResponse
    {
        $rows = NgnPartner::query()
            ->where('is_approved', true)
            ->orderByDesc('id')
            ->get(['id', 'companyname', 'company_logo', 'company_address', 'company_number', 'website', 'fleet_size', 'operating_since']);

        return response()->json(['data' => $rows]);
    }

    public function legalPages(): JsonResponse
    {
        $rows = Legal::query()
            ->where('is_enabled', true)
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'updated_at']);

        return response()->json(['data' => $rows]);
    }

    public function legalPageDetail(string $slug): JsonResponse
    {
        $row = Legal::query()
            ->where('is_enabled', true)
            ->where('slug', $slug)
            ->firstOrFail(['id', 'title', 'slug', 'content', 'updated_at']);

        return response()->json(['data' => $row]);
    }

    public function blogPosts(): JsonResponse
    {
        $rows = BlogPost::query()
            ->with(['category:id,name,slug', 'images:id,path,blog_post_id'])
            ->orderByDesc('id')
            ->paginate(20)
            ->through(fn (BlogPost $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'seo_title' => $post->seo_title,
                'seo_description' => $post->seo_description,
                'category' => $post->category ? [
                    'name' => $post->category->name,
                    'slug' => $post->category->slug,
                ] : null,
                'cover_image' => $post->images->first()?->path,
                'created_at' => optional($post->created_at)->toDateTimeString(),
            ]);

        return response()->json($rows);
    }

    public function blogPostDetail(string $slug): JsonResponse
    {
        $post = BlogPost::query()
            ->with(['category:id,name,slug', 'images:id,path,blog_post_id'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'seo_title' => $post->seo_title,
                'seo_description' => $post->seo_description,
                'category' => $post->category ? [
                    'name' => $post->category->name,
                    'slug' => $post->category->slug,
                ] : null,
                'images' => $post->images->pluck('path')->values(),
                'created_at' => optional($post->created_at)->toDateTimeString(),
            ],
        ]);
    }

    public function reviews(): JsonResponse
    {
        $rows = Review::query()
            ->where('approved', true)
            ->orderByDesc('id')
            ->limit(120)
            ->get(['id', 'rating', 'title', 'content', 'is_recommended', 'created_at']);

        return response()->json(['data' => $rows]);
    }

    public function authBlueprint(): JsonResponse
    {
        return response()->json([
            'customer_auth' => [
                'login' => [
                    'endpoint' => '/api/v1/customer/login',
                    'fields' => ['email', 'password'],
                ],
                'register' => [
                    'endpoint' => '/api/v1/customer/register',
                    'fields' => ['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'],
                ],
                'forgot_password' => [
                    'endpoint' => '/api/v1/customer/forgot-password',
                    'fields' => ['email'],
                ],
                'reset_password' => [
                    'endpoint' => '/api/v1/customer/reset-password',
                    'fields' => ['token', 'email', 'password', 'password_confirmation'],
                ],
            ],
            'staff_auth' => [
                'login' => '/api/v1/staff/login',
                'me' => '/api/v1/staff/me',
                'logout' => '/api/v1/staff/logout',
            ],
        ]);
    }

    private function collectTemplates(string $directory): array
    {
        if (! File::isDirectory($directory)) {
            return [];
        }

        return collect(File::allFiles($directory))
            ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.blade.php'))
            ->map(function ($file) use ($directory) {
                $absolute = $file->getPathname();
                $relative = Str::after($absolute, $directory.'/');
                $content = File::get($absolute);
                $lines = substr_count($content, PHP_EOL) + 1;

                return [
                    'relative_path' => str_replace('\\', '/', $relative),
                    'line_count' => $lines,
                    'sha1' => sha1($content),
                ];
            })
            ->sortBy('relative_path')
            ->values()
            ->all();
    }
}
