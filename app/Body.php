<?php

namespace App;

use App\Events\BodySaved;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Body extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => BodySaved::class,
    ];

    /**
     * Indicates if the IDs are auto-incrementing. They are not.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    public $primaryKey = "page_id";

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['page'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'type',
    ];

    /**
     * Get the Page that owns the Body.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
