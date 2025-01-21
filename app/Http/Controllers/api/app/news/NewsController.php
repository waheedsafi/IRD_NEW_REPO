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
    public function authNewses(Request $request)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt','ntt.news_type_id','=','n.news_type_id')
            ->join('priority_trans as pt','pt.priority_id','=','n.priority_id')
            ->join('users as us','us.id','=','n.user_id')
            ->leftJoin('news_documents as nd','nd.news_id', '=', 'n.id')
            ->where('ntr.language_name',$locale)
            ->where('pt.language_name',$locale)
            ->where('ntt.language_name',$locale)
            ->select(
                        'n.id',
                        'n.visible',
                        'n.date',
                        'n.visibility_date',
                        'n.news_type_id',
                        'ntt.value AS news_type',
                        'n.priority_id',
                        'pt.value AS priority',
                        'us.username AS user',
                        'ntr.title',
                        'ntr.contents',
                        'nd.url AS image'  // Assuming you want the first image URL
                    )
            ->get();
            return $query;
    }

      public function authNews(Request $request)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt','ntt.news_type_id','=','n.news_type_id')
            ->join('priority_trans as pt','pt.priority_id','=','n.priority_id')
            ->join('users as us','us.id','=','n.user_id')
            ->leftJoin('news_documents as nd','nd.news_id', '=', 'n.id')
            ->where('pt.language_name',$locale)
            ->where('ntt.language_name',$locale)
            ->select(
                        'n.id',
                        'n.visible',
                        'n.date',
                        'n.visibility_date',
                        'n.news_type_id',
                        'ntt.value AS news_type',
                        'n.priority_id',
                        'pt.value AS priority',
                        'us.username AS user',
                        'ntr.title',
                        'ntr.contents',
                        'nd.url AS image'  // Assuming you want the first image URL
                    )
            ->get();
            return $query;
    }


        public function publicNewses(Request $request)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt','ntt.news_type_id','=','n.news_type_id')
            ->join('priority_trans as pt','pt.priority_id','=','n.priority_id')
            ->leftJoin('news_documents as nd','nd.news_id', '=', 'n.id')
            ->where('ntr.language_name',$locale)
            ->where('pt.language_name',$locale)
            ->where('ntt.language_name',$locale)
            ->where('n.visible',1)
            ->select(
                        'n.id',
                        'n.visible',
                        'n.date',
                        'n.visibility_date',
                        'n.news_type_id',
                        'ntt.value AS news_type',
                        'n.priority_id',
                        'pt.value AS priority',
                        'ntr.title',
                        'ntr.contents',
                        'nd.url AS image'  // Assuming you want the first image URL
                    )
            ->get();
            return $query;
    }

    public function publicNews(Request $request,$id){

         $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt','ntt.news_type_id','=','n.news_type_id')
            ->join('priority_trans as pt','pt.priority_id','=','n.priority_id')
            ->leftJoin('news_documents as nd','nd.news_id', '=', 'n.id')
            ->where('ntr.language_name',$locale)
            ->where('pt.language_name',$locale)
            ->where('ntt.language_name',$locale)
            ->where('n.visible',1)
            ->where('n.id',$id)
            ->select(
                        'n.id',
                        'n.visible',
                        'n.date',
                        'n.visibility_date',
                        'n.news_type_id',
                        'ntt.value AS news_type',
                        'n.priority_id',
                        'pt.value AS priority',
                        'ntr.title',
                        'ntr.contents',
                        'nd.url AS image'  // Assuming you want the first image URL
                    )
            ->get();
            return $query;
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
        $contents = $validatedData["content_english"];
        $locale = App::getLocale();
        if ($locale === LanguageEnum::farsi->value) {
            $title = $validatedData["title_farsi"];
            $contents = $validatedData["content_farsi"];
        } else if ($locale === LanguageEnum::pashto->value) {
            $title = $validatedData["title_pashto"];
            $contents = $validatedData["content_pashto"];
        }

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
                    "date" => $validatedData["date"],
                    "created_at" => $news->created_at,
                    "contents" => $contents,
                    "image" => $document['path'],
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
