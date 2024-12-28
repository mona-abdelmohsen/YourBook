<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        if(!$request->category_id){
            $category = $user->categories()->create([
                'title' => $request->category,
            ]);
        }else{
            $category = $user->categories()->find($request->category_id);
        }

        $book = $user->books()->create([
            'title'         => $request->book_name,
            'description'   => $request->description,
            'privacy'       => $request->privacy,
            'category_id'   => $category ? $category->id: null,
        ]);


        $mediaIds = [];
        // store descriptions
        foreach($request->media ?? [] as $mediaItem){
            $mediaIds[] = $mediaItem['media_id'];
            // Set Description ...
            $mediaModel = $user->media()->find($mediaItem['media_id'])->setCustomProperty('description', $mediaItem['description']);
            $mediaModel->save();

            // Set Book Cover
            if($book->getMedia('cover')->count() <= 0 && $mediaModel->collection_name == 'images'){
                $mediaModel->copy($book, 'cover', 's3');
            }
        }

        $book->mediaFiles()->syncWithoutDetaching($mediaIds);

        return response()->json($book->id, 201);
    }


    /**
     * @param $book_id
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function show($book_id): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $book = Book::with('mediaFiles')->where([
            'user_id'   => auth()->id(),
            'books.id'  => $book_id,
        ])->firstOrFail();

        return view('user.book')->with([
            'book'  => $book,
        ]);
    }

}
