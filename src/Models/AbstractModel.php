<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Tall\Sluggable\SlugOptions;
use Tall\Sluggable\HasSlug;
use Tall\Theme\Models\Make;
use Tall\Theme\Models\Status;

class AbstractModel extends Model
{
    use HasUuids;
    use HasSlug;

    protected $with = ['status'];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setIncrementing(config('tall-orm.incrementing', false));
        $this->setKeyType(config('tall-orm.keyType', 'string'));
    }   

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * @return SlugOptions
     */
    public function getSlugOptions()
    {
        if (is_string($this->slugTo())) {
            return SlugOptions::create()
                ->generateSlugsFrom($this->slugFrom())
                ->saveSlugsTo($this->slugTo());
        }
    }
    public function isUser()
    {
        return true;
    }

      /**
     * Get the post's image.
     */
    public function makes()
    {
        if(class_exists('\App\Models\Make')){
            return $this->belongsTo('\App\Models\Make');
        }
        return $this->belongsTo(Make::class);
    }

      /**
     * Get the post's image.
     */
    public function tenant()
    {
        return $this->belongsTo(Make::class);
    }

      /**
     * Get the post's image.
     */
    public function status()
    {
        if(class_exists('\App\Models\Status')){
            return $this->belongsTo('\App\Models\Status');
        }
        return $this->belongsTo(Status::class);
    }


    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function getStatusColorAttribute()
    {
        return [
            'draft'=>'secondary',
            'published'=>'success',
        ][data_get($this->status, 'slug')] ?? 'primary';
    }
}
