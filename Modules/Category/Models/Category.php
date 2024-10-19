<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Article\Models\Article;
use Modules\Category\Database\Factories\CategoryFactory;
use Modules\Category\Enums\CategoryStatusEnum;
use Modules\Media\Models\Media;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

/**
 * @property string $status
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Set fillable for columns.
     *
     * @var string[]
     */
    protected $fillable = [
        'media_id',
        'user_id',
        'parent_id',
        'title',
        'slug',
        'keywords',
        'status',
        'description',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var string[]
     */
    protected $with = ['media'];

    /**
     * Create a new factory instance for the model.
     *
     * @return CategoryFactory
     */
    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    // Attributes

    /**
     * Set parent attributes.
     *
     * @return string
     */
    public function getParentAttribute()
    {
        return is_null($this->parent_id)
            ? 'No parent'
            : $this->parent->title;
    }

    // Relations

    /**
     * Relation to media, relation is one to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Relation to user, relation is one to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation to parent, parent is this class(Category).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    /**
     * Relation to categories(upside down).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories()
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }

    /**
     * Relation to articles, relation is one to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }

    /**
     * Relation to products, relation is one to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    // Methods

    /**
     * Return css class for category status.
     *
     * @return string
     */
    public function getCssClassStatus()
    {
        return $this->status === CategoryStatusEnum::STATUS_ACTIVE->value
            ? 'success'
            : 'warning';
    }

    /**
     * Get path for detail category.
     *
     * @return string
     */
    public function path()
    {
        return route('categories.detail', $this->slug);
    }

    // Scopes

    /**
     * Actvie scope.
     *
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', CategoryStatusEnum::STATUS_ACTIVE->value);
    }
}
