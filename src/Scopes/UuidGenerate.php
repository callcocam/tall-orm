<?php
/**
 * Created by Bengs.
 * User: contato@bengs.com.br
 * https://www.bengs.com.br
 */

namespace Tall\Cms\Scopes;

use Ramsey\Uuid\Uuid;

trait UuidGenerate
{

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(!$model->getIncrementing()){
                if (is_null($model->id)):
                    $model->id = Uuid::uuid4();
                endif;
            }        
            if($model->isUser()){
                if(auth()->check()){
                    if (is_null($model->user_id)):
                        $model->user_id = auth()->id();
                    endif;
                }
            }
        });
    }
}
