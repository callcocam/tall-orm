<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Tall\Cms\Scopes\UuidGenerate;
use Tall\Sluggable\SlugOptions;
use Tall\Sluggable\HasSlug;

class AbstractModel extends Model
{
    use UuidGenerate, HasSlug;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setIncrementing(config('tall-cms.incrementing', false));
        $this->setKeyType(config('tall-cms.keyType', 'string'));
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
}
