<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Tall\Orm\Traits\Form;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadsFiles
{
    /**
     * @var UploadedFile
     */
    public $file;

    protected $route;

    public $x;
    public $y;
    public $width;
    public $height;
    public  $storage_disk;
    public $storage_path;
    public $files=[];

    public  function fileUpload($dataFiles=[])
    {
            $storage_disk = $this->storage_disk ?? config('form.storage_disk', config('filesystems.default'));
            $storage_path = $this->storage_path ?? config('form.storage_path','images');
            $files = [];
            foreach ($this->fields() as $field) {
                if($field->hasType('file')){
                    $data = data_get($this->form_data, $field->name);
                    if(!$field->has('multiple')){
                        $dataFiles[$field->name] = $data;
                    }
                    else{
                        $dataFiles = $data;
                    }
                    foreach ($dataFiles as  $key => $file) {
                        if($file instanceof UploadedFile){
                            $this->fileUploadate($field, [
                                'source' => $file->store($storage_path, $storage_disk),
                                'disk' => $storage_disk,
                                'name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'mime_type' => $file->getMimeType(),
                                ]);
                        }
                    }
                }
            }
            return $files;
        
    }

     public function fileUploadate($field, $uploaded_files)
    {
        if(method_exists($this->model, 'updateImages') && $field->has('multiple')){
            $this->form_data[$field->name] = $this->model->updateImages($uploaded_files);
        }
        else{
            $this->form_data[$field->name] = data_get($uploaded_files,'source');
        }
        $this->updated(sprintf('form_data.%s', $field->name));
       
    }


    public function reorderfiles($id, $ordering)
    {
        $originData = $this->model->images()->where('id',$id)->first();

        if($originData){
            $originData->ordering = $ordering;
            $originData->update();
        }
    }

    public function deleteUploadUrl($file,$id)
    {
     
        if(method_exists($this->model, 'deleteImage')){
            if(!$this->model->deleteImage($id)){
                if (!$this->isDeleteUrl($this->form_data[$file])) {
                    Storage::delete($this->form_data[$file]);
                    $this->form_data[$file] = null;
                    $this->updated(sprintf('form_data.%s', $file));
                }
            }
        }
       else{
        if (!$this->isDeleteUrl($this->form_data[$file])) {
            Storage::delete($this->form_data[$file]);
            $this->form_data[$file] = null;
            $this->updated(sprintf('form_data.%s', $file));
        }
       }
        
    }

    public function fileIcon($mime_type)
    {
        $icons = [
            'image' => 'fa-file-image',
            'audio' => 'fa-file-audio',
            'video' => 'fa-file-video',
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.ms-word' => 'fa-file-word',
            'application/vnd.oasis.opendocument.text' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel',
            'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint',
            'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint',
            'text/plain' => 'fa-file-alt',
            'text/html' => 'fa-file-code',
            'application/json' => 'fa-file-code',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint',
            'application/gzip' => 'fa-file-archive',
            'application/zip' => 'fa-file-archive',
            'application/x-zip-compressed' => 'fa-file-archive',
            'application/octet-stream' => 'fa-file-archive',
        ];

        if (isset($icons[$mime_type])) return $icons[$mime_type];
        $mime_group = explode('/', $mime_type, 2)[0];

        return (isset($icons[$mime_group])) ? $icons[$mime_group] : 'fa-file';
    }

    protected function isDeleteUrl($file)
    {
        if(is_array($file)){
            return false;
        }
        return Str::contains($file, 'no_image');
    }
}
