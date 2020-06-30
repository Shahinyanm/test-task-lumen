<?php

namespace App\Http\Resources\NewsApi;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NewsApiCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function ($news) {
            return (new NewsApiResource($news));
        });

        return parent::toArray($request);
    }
}
