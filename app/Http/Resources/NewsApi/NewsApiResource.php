<?php

namespace App\Http\Resources\NewsApi;

use App\Http\Resources\Main\PageWidgetOption\PageWidgetOptionCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        dd($this);
        return [
            "source" => $this->source->source_name,
            "author" => $this->author,
            "title" => $this->title,
            "description" => $this->description,
            "url" => $this->url,
            "urlToImage" => $this->urlToImage,
            "publishedAt" => $this->publishedAt,
            "content" => $this->content,
            "theme" => $this->theme
        ];
    }
}
