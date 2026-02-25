<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogImage;
use App\Models\BlogPost;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BlogPostCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BlogPostCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(BlogPost::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/blog-post');
        CRUD::setEntityNameStrings('blog post', 'blog posts');
    }

    protected function setupListOperation()
    {
        CRUD::column('title');
        CRUD::column('slug');
        CRUD::column('category_id')->label('Category')->type('relationship');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(BlogPostRequest::class);

        CRUD::field('title');
        CRUD::field('content')->type('ckeditor');
        CRUD::field([   // Text
            'name' => 'slug',
            'target' => 'title', // will turn the title input into a slug
            'label' => 'Slug',
            'type' => 'slug',

            // optional
            'locale' => 'pt', // locale to use, defaults to app()->getLocale()
            'separator' => '', // separator to use
            'trim' => true, // trim whitespace
            'lower' => true, // convert to lowercase
            'strict' => true, // strip special characters except replacement
            'remove' => '/[*+~.()!:@]/g', // remove characters to match regex, defaults to null
        ]);
        CRUD::field('seo_title');
        CRUD::field('seo_description');
        CRUD::field('category_id')->type('select')->entity('category')->model(BlogCategory::class)->attribute('name');
        CRUD::addField([
            'name' => 'images',
            'label' => 'Add Images',
            'type' => 'upload_multiple',
            'upload' => true,
            'disk' => 'public',
            'path' => 'uploads/product_images',
            'attributes' => [
                'accept' => 'image/*',
            ],
            'value' => $this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->images ?
                $this->crud->getCurrentEntry()->images->pluck('path')->toArray() : [],
        ]);
        // Add any additional fields as necessary
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store(BlogPostRequest $request)
    {

        $blogPost = BlogPost::create($request->except(['images']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public'); // Store image in public disk
                BlogImage::create([
                    'blog_post_id' => $blogPost->id,
                    'path' => $path, // Save the path to the image
                ]);
            }
        }

        return redirect()->back()->with('success', 'Blog post created successfully!');
    }

    public function update(BlogPostRequest $request)
    {
        $blogPost = BlogPost::find($this->crud->getCurrentEntryId());

        $blogPost->update($request->except(['images']));

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public'); // Store image in public disk
                BlogImage::create([
                    'blog_post_id' => $blogPost->id,
                    'path' => $path, // Save the path to the image
                ]);
            }
        }

        return redirect()->back()->with('success', 'Blog post updated successfully!');
    }
}
