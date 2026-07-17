<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use CrudTrait, HasFactory;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'content',
        'slug',
        'seo_title',
        'seo_description',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(BlogImage::class, 'blog_post_id');
    }

    public function setImagesAttribute($value)
    {
        $attribute_name = 'images';
        $disk = 'public';
        $destination_path = 'uploads/blog_images';

        $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }
}
