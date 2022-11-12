<?php
/**
* Created by Claudio Campos.
* User: callcocam@gmail.com, contato@sigasmart.com.br
* https://www.sigasmart.com.br
*/
namespace Tall\Orm\Models\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasCover
{
    /**
     * Update the user's profile photo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @return void
     */
    public function updateCover(UploadedFile $photo)
    {
        tap($this->cover, function ($previous) use ($photo) {
            $this->forceFill([
                'cover' => $photo->storePublicly(
                    'iamges/cover', ['disk' => $this->coverDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->coverDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteCover()
    {
        if (is_null($this->cover)) {
            return;
        }

        Storage::disk($this->coverDisk())->delete($this->cover);

        $this->forceFill([
            'cover' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getCoverUrlAttribute()
    {
        return $this->cover
                    ? Storage::disk($this->coverDisk())->url($this->cover)
                    : $this->defaultCoverUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultCoverUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function coverDisk()
    {
        return config('filesystems.default');
    }
}
