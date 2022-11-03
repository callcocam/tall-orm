<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Tall\Cms\Scopes\UuidGenerate;
use Tall\Sluggable\SlugOptions;
use Tall\Sluggable\HasSlug;
use Tall\Theme\Models\Make;

class AbstractModel extends Model
{
    use UuidGenerate, HasSlug;

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
    public function make()
    {
        if(class_exists('\App\Models\Make')){
            return $this->belongsTo('\App\Models\Make');
        }
        return $this->belongsTo(Make::class);
    }
}
