<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsApi\NewsApiCollection;
use App\News;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    /**
     * Retrieve the user for the given ID.
     *
     * @param  string  $type
     * @return NewsApiCollection
     */
    public function getNews($type = 'publishedAt')
    {
        $news = News::with('source')->groupBy($type)->get();

        return (new NewsApiCollection($news));
    }
}
