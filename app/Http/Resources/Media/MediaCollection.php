<?php
namespace App\Http\Resources\Media;

use Illuminate\Http\Resources\Json\Resource;
class MediaCollection extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $arrMedia=[];
        $mediaData=$this;
        if(!empty($mediaData)){
            foreach ($mediaData->all() as $media){
                $arrMedia[]=[
                  "original"=>$media->getUrl(),
                  "url"=>$media->getFullUrl(),
                  "path"=>$media->getPath(),
                  "file_name"=>$media->file_name,
                  //"size"=>$media->size." bytes",
                  "size"=>$media->human_readable_size,
                  "mime_type"=>$media->mime_type,
                  "conversions"=>[
                      "thumb"=>$media->getUrl('thumb'),
                      "square"=>$media->getUrl('square'),
                  ]
                ];
            }
        }
        return $arrMedia;
    }
}
