<?php

namespace App;

use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function foo\func;

class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id', 'author', 'title', 'description', 'url', 'urlToImage', 'publishedAt', 'content', 'theme'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(NewsSources::class, 'source_id');
    }

    /** save news and sources
     * @param $news
     * @return array
     * @throws \Throwable
     */
    public static function saveNews($news, $theme)
    {
        $newsRows = [];

        if (gettype($news) === 'object') {
            $news = (array)$news;
        }

        $sources = array_column($news, 'source');

        // save Sources to NewsSources table
        DB::transaction(function() use($sources, &$news, &$newsRows, $theme) {
            foreach ($sources as $index => $source) {
               $newsSource = NewsSources::firstOrCreate([
                   'source_id' => $source->id,
                   'source_name' => $source->name
               ]);

                unset($news[$index]->source);
                $news[$index]->source_id = $newsSource->id;
                $news[$index]->publishedAt = (new \DateTime($news[$index]->publishedAt))->format('Y-m-d H:i:s');
                $news[$index]->theme = $theme;

                $newsRows[] = self::firstOrCreate((array)$news[$index]);
            }
        });

        return $newsRows;
    }

    public static function getNewsBySourceName($sourceName)
    {
        // sources is unique
        $news = NewsSources::with('news')->where('source_name', $sourceName)->first()->news;

        return $news;
    }

}
