<?php

namespace App\Http\Controllers\api\app\news;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\news\NewsStoreRequest;
use App\Http\Requests\app\news\NewsUpdateRequest;
use App\Models\News;
use App\Models\NewsDocument;
use App\Models\NewsTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function authNewses(Request $request,$page)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->where('n.visible', 1)
            ->select(
                'n.id as id',
                'n.visible',
                'n.date',
                'n.visibility_date',
                'n.news_type_id',
                'ntt.value AS news_type',
                'n.priority_id',
                'pt.value AS priority',
                'ntr.title',
                'ntr.contents',
                'nd.url AS image',  // Assuming you want the first image URL
                'n.created_at'
            );


        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json(
            [
                "newses" => $result,
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
            );
    }

    public function authNews(Request $request)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->join('users as us', 'us.id', '=', 'n.user_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
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

                return response()->json([
                "news" => $query
               
            ], 200, [], JSON_UNESCAPED_UNICODE);
           
    }

        public function publicNewses(Request $request,$page)
    {
                $perPage = $request->input('per_page', 10); // Number of records per page
            $page = $request->input('page', 1); // Current page
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
                            'n.id as id',
                            'n.visible',
                            'n.date',
                            'n.visibility_date',
                            'n.news_type_id',
                            'ntt.value AS news_type',
                            'n.priority_id',
                            'pt.value AS priority',
                            'ntr.title',
                            'ntr.contents',
                            'nd.url AS image',  // Assuming you want the first image URL
                            'n.create_at'
            );
        

             $this->applyFilters($query,$request);
             $this->applySearch($query,$request);

          $result = $query->paginate($perPage, ['*'], 'page', $page);

               return response()->json(
            [
                "users" => $result,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
   
   
    }

    public function publicNewses(Request $request)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->where('n.visible', 1)
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

    public function publicNews(Request $request, $id)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->where('n.visible', 1)
            ->where('n.id', $id)
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
                return response()->json([
                "news" => $query
               
            ], 200, [], JSON_UNESCAPED_UNICODE);
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
    // date function 
    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('n.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('n.created_at', '<=', $endDate);
        }
    }

    public function update(NewsUpdateRequest $request)
{
    $validatedData = $request->validated();
    $authUser = $request->user();

    $id =$validatedData->id;
    // Begin transaction
    DB::beginTransaction();
        // Find the news record or throw an exception if not found
        $news = News::findOrFail($id);

        // Update news fields
        $news->update([
            "user_id" => $authUser->id,
            "visible" => $validatedData["visible"],
            "date" => $validatedData["date"],
            "visibility_date" => $validatedData["visibility_date"],
            "priority_id" => $validatedData["priority"],
            "news_type_id" => $validatedData["type"]
        ]);

        // Update translations
        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::default->value],
            ["title" => $validatedData["title_english"], "contents" => $validatedData["content_english"]]
        );

        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::pashto->value],
            ["title" => $validatedData["title_pashto"], "contents" => $validatedData["content_pashto"]]
        );

        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::farsi->value],
            ["title" => $validatedData["title_farsi"], "contents" => $validatedData["content_farsi"]]
        );

        // Update document if a new one is uploaded
        if ($request->hasFile('cover_pic')) {
            // Delete the old document if it exists
            $existingDocument = NewsDocument::where('news_id', $news->id)->first();
            if ($existingDocument) {
                Storage::delete($existingDocument->url);
                $existingDocument->delete();
            }

            // Store new document
            $document = $this->storeDocument($request, "public", "news", 'cover_pic');

            NewsDocument::create([
                "news_id" => $news->id,
                "url" => $document['path'],
                "extintion" => $document['extintion'],
                "name" => $document['name'],
            ]);
        }

        // Commit transaction
        DB::commit();

        // Prepare localized title and content
        $title = $validatedData["title_english"];
        $contents = $validatedData["content_english"];
        $locale = App::getLocale();

        if ($locale === LanguageEnum::farsi->value) {
            $title = $validatedData["title_farsi"];
            $contents = $validatedData["content_farsi"];
        } elseif ($locale === LanguageEnum::pashto->value) {
            $title = $validatedData["title_pashto"];
            $contents = $validatedData["content_pashto"];
        }

        // Return a success response
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'news' => [
                    "id" => $news->id,
                    "user" => $authUser->username,
                    "visible" => $news->visible,
                    "visibility_date" => $news->visibility_date,
                    "title" => $title,
                    "news_type" => $request->type_name,
                    "priority" => $request->priority_name,
                    "date" => $news->date,
                    "updated_at" => $news->updated_at,
                    "contents" => $contents,
                    "image" => $document['path'],

                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
}




    // search function 
      protected function applySearch($query,$request){

        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = ['title', 'contents'];

            // Ensure that the search column is allowed
            if (in_array($searchColumn, $allowedColumns)) {
                $query->where($searchColumn, 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 

        if ($sort && in_array($sort, ['news_type_id', 'priority_id', 'visible', 'visibility_date', 'date'])) {
            $query->orderBy($sort, $order);
        } else {
            // Default sorting if no sort is provided
            $query->orderBy("created_at", 'desc');
        }
    }
}
