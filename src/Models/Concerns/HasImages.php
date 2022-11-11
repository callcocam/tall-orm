<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Tall\Theme\Models\Image;

trait HasImages
{
    /**
     * Update the user's profile photo.
     *
     * @param array $data
     * @return void
     */
    public function updateImages($data=[])
    {
        $this->images()->create($data);

        return $this->images;
    }
    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteImage($id)
    {
        if (is_null($id)) {
            return false;
        }

        if($image = $this->images()->where('id',$id)->first()){
            Storage::disk($this->sourceDisk())->delete($image->source);            
            
            return $image->forceDelete();
        }
        return false;
        

    }
    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function sourceDisk()
    {
        return config('filesystems.default');
    }

      /**
     * Get all of the post's images.
     */
    public function images()
    {
        if(class_exists('\\App\\Model\\Image')){
            return $this->morphMany('\\App\\Model\\Image', 'imageable')->orderBy('ordering');
        }
        return $this->morphMany(Image::class, 'imageable')->orderBy('ordering');
    }
}
