<?php

namespace App\Jobs;


use App\News;
use App\Services\NewsApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GetNewsJob extends Job
{
    public $page = 1;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return array
     * @throws \Throwable
     */
    public function handle()
    {
        $newsApi = new NewsApi(config('newsapi.NEWS_API_KEY'));
        $newsId = [];

        $page = DB::table('news_api_page')
            ->orderByDesc('page')
            ->first();

        if ($page) {
             $this->page = $page->page + 1;
        }

        $newsBitcoin = $newsApi->everything(['q' => 'Bitcoin', 'pageSize' => 1, 'page' => $this->page]);
        $newsLitecoin = $newsApi->everything(['q' => 'Litecoin', 'pageSize' => 1, 'page' => $this->page]);
        $newsRipple = $newsApi->everything(['q' => 'Ripple', 'pageSize' => 1, 'page' => $this->page]);
        $newsDash = $newsApi->everything(['q' => 'Dash', 'pageSize' => 1, 'page' => $this->page]);
        $newsEthereum = $newsApi->everything(['q' => 'Ethereum', 'pageSize' => 1, 'page' => $this->page]);

        if ($newsBitcoin->status !== 'ok' &&
            $newsLitecoin->status !== 'ok' &&
            $newsRipple->status !== 'ok' &&
            $newsDash->status !== 'ok' &&
            $newsEthereum->status !== 'ok'
        ) {
            return false;
        }

        // save if api returns news
        if ($newsBitcoin) {
            $newsBitcoin = News::saveNews($newsBitcoin->articles, 'Bitcoin');
            $newsId[] = $newsBitcoin[0]->id;
        }

        if ($newsLitecoin) {
            $newsLitecoin = News::saveNews($newsLitecoin->articles, 'Litecoin');
            $newsId[] = $newsLitecoin[0]->id;
        }

        if ($newsRipple) {
            $newsRipple = News::saveNews($newsRipple->articles, 'Ripple');
            $newsId[] = $newsRipple[0]->id;
        }

        if ($newsDash) {
            $newsDash = News::saveNews($newsDash->articles, 'Dash');
            $newsId[] = $newsDash[0]->id;
        }

        if ($newsEthereum) {
            $newsEthereum = News::saveNews($newsEthereum->articles, 'Ethereum');
            $newsId[] = $newsEthereum[0]->id;
        }

        $lastPage = DB::table('news_api_page')
            ->whereIn('news_id', $newsId)
            ->orderByDesc('page')
            ->first();

        if ($lastPage) $this->page = $lastPage->page + 1;

        if (isset($newsBitcoin)) {
            $pageData = ['news_id' => $newsBitcoin[0]->id, 'page' => $this->page];
            $page = DB::table('news_api_page')->updateOrInsert(['news_id' => $newsBitcoin[0]->id], $pageData);
        }

        if (isset($newsLitecoin)) {
            $pageData = ['news_id' => $newsLitecoin[0]->id, 'page' => $this->page];
            $page = DB::table('news_api_page')->updateOrInsert(['news_id' => $newsLitecoin[0]->id], $pageData);
        }

        if (isset($newsRipple)) {
            $pageData = ['news_id' => $newsRipple[0]->id, 'page' => $this->page];
            $page = DB::table('news_api_page')->updateOrInsert(['news_id' => $newsRipple[0]->id], $pageData);
        }

        if (isset($newsDash)) {
            $pageData = ['news_id' => $newsDash[0]->id, 'page' => $this->page];
            $page = DB::table('news_api_page')->updateOrInsert(['news_id' => $newsDash[0]->id], $pageData);
        }

        if (isset($newsEthereum)) {
            $pageData = ['news_id' => $newsEthereum[0]->id, 'page' => $this->page];
            $page = DB::table('news_api_page')->updateOrInsert(['news_id' => $newsEthereum[0]->id], $pageData);
        }


        return [
            'Bitcoin' => $newsBitcoin,
            'Litecoin' => $newsLitecoin,
            'Ripple' => $newsRipple,
            'Dash' => $newsDash,
            'Ethereum' => $newsEthereum
        ];
    }
}
