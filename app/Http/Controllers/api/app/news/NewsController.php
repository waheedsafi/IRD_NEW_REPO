<?php

namespace App\Http\Controllers\api\app\news;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\news\NewsStoreRequest;
use App\Models\News;
use App\Models\NewsDocument;
use App\Models\NewsTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    //

    public function news(Request $request)
    {

        $locale = App::getLocale();

        $query = News::with([
            'newsTran' => function ($query) use ($locale) {
                $query->where('language_name', $locale)->select('id', 'news_id', 'title');
            },
            'priority:id,name',
            'newsType:id,name'

        ]);
    }
    public function showNews(Request $request)
    {


        // return News::with('newsDocument')->get();

        $request->validate([
            'searchValue' => 'string'
        ]);

        $locale = App::getLocale();

        // Fetching news with optimized relations and filtering
        $query = News::with([
            'newsTran' => function ($query) use ($locale) {
                $query->where('language_name', $locale)
                    ->select('id', 'news_id', 'title', 'contents');
            },
            'priority:id,name',
            'newsType:id,name',
            'newsDocument:id,news_id,url,extintion',
        ])->where('visible', 1)
            ->where('submited', 1);

        // Add search condition if searchValue exists
        if (!empty($request->searchValue)) {
            $searchValue = $request->searchValue;

            $query->whereHas('newsTran', function ($subQuery) use ($searchValue) {
                $subQuery->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('contents', 'like', "%{$searchValue}%");
            });
        }

        return $query->get();
    }


    public function store(NewsStoreRequest $request)
    {
        $validatedData = $request->validated();
        $authUser = $request->user();

        // Begin transaction
        DB::beginTransaction();

        $news = News::create([
            "user_id" => $authUser->id,
            "visible" => true,
            "date" => $validatedData["date"],
            "visibility_date" => $request->visibility_date,
            "priority_id" => $validatedData["priority"],
            "news_type_id" => $validatedData["type"]
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::default->value,
            "title" => $validatedData["title_english"],
            "contents" => $validatedData["content_english"],
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::pashto->value,
            "title" => $validatedData["title_pashto"],
            "contents" => $validatedData["content_pashto"],
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::farsi->value,
            "title" => $validatedData["title_farsi"],
            "contents" => $validatedData["content_farsi"],
        ]);

        // 3. Store documents
        $document = $this->storeDocument($request, "public", "news", 'cover_pic');
        NewsDocument::create([
            "news_id" => $news->id,
            "url" => $document['path'],
            "extintion" => $document['extintion'],
            "name" => $document['name'],
        ]);

        // If everything goes well, commit the transaction
        DB::commit();
        // Return a success response

        $title = $validatedData["title_english"];
        $locale = App::getLocale();
        if ($locale === LanguageEnum::farsi->value)
            $title = $validatedData["title_farsi"];
        else if ($locale === LanguageEnum::pashto->value)
            $title = $validatedData["title_pashto"];

        return response()->json(
            [
                'message' => __('app_translation.success'),
                'news' => [
                    "id" => $news->id,
                    "user" => $authUser->username,
                    "visible" => true,
                    "visibility_date" => $request->visibility_date,
                    "title" => $title,
                    "news_type" => $request->type_name,
                    "priority" => $request->priority_name,
                    "created_at" => $news->created_at
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
