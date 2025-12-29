<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Category model.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon_path
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
    ];

    /**
     * Get all clips for this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clips(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Clip::class);
    }

    // There is no automatic icon download logic. icon_path is only set by upload.
}
