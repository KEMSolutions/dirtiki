<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Sluggable;
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
                'maxLength' => 512,
                'onUpdate' => true,
                'unique' => true,
            ],
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the Body record associated with the Page.
     */
    public function body()
    {
        return $this->hasOne(Body::class)->withDefault();
    }

    /**
     * Get the redirections for the page.
     */
    public function pageRedirections()
    {
        return $this->hasMany(PageRedirection::class);
    }

}
