<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BookController extends Controller
{
    use ApiResponse;

    public function index(): \Illuminate\Http\JsonResponse
    {
        $book_id = request()->book_id;
        $per_page = \request()->per_page;

        $data = auth()->user()->books()->where(function ($query) use ($book_id) {
            $query->whereNull('parent_id');
            if ($book_id) {
                $query->where('books.id', $book_id);
            }
        })->with(['mediaFiles', 'children'])
            ->orderBy('books.created_at', 'desc')->paginate($per_page ?? 15);

        $responseData = $data->getCollection()->transform(function ($book) {
            $media = $book->mediaFiles->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
            $media = array_values($media);
            $book->is_favorite = $book->isFavoritedBy(auth()->user());
            $book = $book->toArray();
            unset($book['media_files']);
            unset($book['_lft']);
            unset($book['_rgt']);
            $book['media'] = $media;
            return $book;
        });

        $paginatedTransformedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $responseData,
            $data->total(),
            $data->perPage(),
            $data->currentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        if ($book_id) {
            if (!count($responseData)) {
                return $this->error("No book found with this ID", [
                    'book_id' => ['No book found with this ID']
                ], self::$responseCode::HTTP_NOT_FOUND);
            }
        }

        return $this->success("Success", $paginatedTransformedData, self::$responseCode::HTTP_OK);
    }

    public function show($book_id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(array_merge(request()->toArray(), ['book_id' => $book_id]), [
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $book = Book::where('id', $book_id)
            ->with(['mediaFiles', 'children'])->get();
        if ($book->count() <= 0) {
            return $this->error(
                "Book Does Not Exists",
                null,
                ResponseAlias::HTTP_NOT_FOUND
            );
        }

        $book = $book->map(function ($item) {
            $media = $item->mediaFiles->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();
            $media = array_values($media);
            $item->is_favorite = $item->isFavoritedBy(auth()->user());
            $item = $item->toArray();
            unset($item['media_files']);
            unset($item['_lft']);
            unset($item['_rgt']);
            $item['media'] = $media;
            return $item;
        })->first();


        return $this->success("success", $book, self::$responseCode::HTTP_OK);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Validation
        $validator = Validator::make($request->toArray(), [
            'title' => 'required|string',
            'description' => 'nullable|string|max:10000',
            'privacy' => 'required|string|in:public,private,friend',
            'category_id' => 'nullable|exists:user_categories,id',
            'parent_id' => 'nullable|exists:books,id',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $category = null;
        if ($request->category_id) {
            $category = auth()->user()->categories()->find($request->category_id);
            if (!$category) {
                return $this->error(
                    "This Category do not belongs to this user.",
                    ['category_id' => 'This Category do not belongs to this user.'],
                    ResponseAlias::HTTP_UNAUTHORIZED
                );
            }
        }

        $parentBook = null;
        if ($request->parent_id) {
            $parentBook = auth()->user()->books()->find($request->parent_id);
            if (!$parentBook) {
                return $this->error(
                    "You do not own the parent book!",
                    ['parent_id' => 'You do not own the parent book!'],
                    ResponseAlias::HTTP_UNAUTHORIZED
                );
            }
        }

        // Store
        $book = auth()->user()->books()->create([
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'category_id' => $category ? $category->id : null,
        ]);

        if ($parentBook) {
            $parentBook->appendNode($book);
        }

        // return Results
        return $this->success('Book Created.', $book, ResponseAlias::HTTP_CREATED);
    }


    /**
     * @param Request $request
     * @param int $book_id
     * @param string $mode [attach, detach]
     * @return JsonResponse
     */
    public function attachDetachMedia(Request $request, int $book_id, string $mode): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), [
            'book_id' => $book_id,
            'mode' => 'required|in:attach,detach',
        ]), [
            'uuids' => ['required', 'array', 'min:1'],
            'uuids.*' => ['uuid', 'exists:media,uuid', 'distinct:strict'],
            'book_id' => ['required', 'exists:books,id']

        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** check if media belongs to user */
        /** and book belongs to user */
        $book = auth()->user()->books()->where('id', $book_id)->first();
        if (!$book) {
            return $this->error("This User has to access to given book!", [
                'book_id' => ['This User has no access to given book!']
            ], self::$responseCode::HTTP_UNAUTHORIZED);
        }

        $media_ids = auth()->user()->media()->whereIn('uuid', $request->uuids)->get()->pluck(['id']);

        if (!$media_ids || count($media_ids ?? []) <= 0) {
            return $this->error("This User has no control over media files with these uuids.", [
                'uuids' => ['This User has no control over media files with these uuids.']
            ], self::$responseCode::HTTP_UNAUTHORIZED);
        }

        if ($mode == 'attach') {
            $book->mediaFiles()->syncWithoutDetaching($media_ids);
        } else {
            $book->mediaFiles()->detach($media_ids);
        }

        $media = auth()->user()->media()->whereIn('id', $media_ids)->get()
            ->map([$this, 'mediaMap'])->keyBy('uuid')->toArray();

        return $this->success("Success", $media, self::$responseCode::HTTP_ACCEPTED);
    }

    public function update(Request $request, $book_id): \Illuminate\Http\JsonResponse
    {
        // Validation
        $validator = Validator::make(array_merge($request->toArray(), ['book_id' => $book_id]), [
            'book_id' => 'required|exists:books,id',
            'title' => 'required|string',
            'description' => 'nullable|string|max:10000',
            'privacy' => 'required|string|in:public,private,friend',
            'category_id' => 'nullable|exists:user_categories,id'
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                $validator->errors(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }



        $book = auth()->user()->books()->find($book_id);
        if (!$book) {
            return $this->error("This User has to access to given book!", [
                'book_id' => ['This User has no access to given book!']
            ], self::$responseCode::HTTP_UNAUTHORIZED);
        }

        $category = null;
        if ($request->category_id) {
            $category = auth()->user()->categories()->find($request->category_id);
            if (!$category) {
                return $this->error(
                    "This Category do not belongs to this user.",
                    ['category_id' => 'This Category do not belongs to this user.'],
                    ResponseAlias::HTTP_UNAUTHORIZED
                );
            }
        }

        $book->update([
            'title' => $request->title,
            'description' => $request->description,
            'privacy' => $request->privacy,
            'category_id' => $category ? $category->id : null,
        ]);

        return $this->success(
            "Book Updated",
            auth()->user()->books()->find($book_id),
            self::$responseCode::HTTP_OK
        );
    }


    public function mediaMap(\Spatie\MediaLibrary\MediaCollections\Models\Media $media): array
    {
        return [
            'name' => $media->name,
            'file_name' => $media->file_name,
            'uuid' => $media->uuid,
            'preview_url' => $media->preview_url,
            'original_url' => $media->original_url,
            'order' => $media->order_column,
            'custom_properties' => $media->custom_properties,
            'extension' => $media->extension,
            'size' => $media->size,
            'type' => $media->collection_name,
        ];
    }

    // destroy book
    public function destroy($book_id): \Illuminate\Http\JsonResponse
{
    $book = Book::with('mediaFiles')->find($book_id);
    $relatedBooks = Book::where('parent_id', $book_id);

    if (!$book) {
        return $this->error("No book found with this ID", null, self::$responseCode::HTTP_NOT_FOUND);
    }

    if ($book->user_id !== auth()->id()) {
        return $this->error("You have no access to the given book!", null, self::$responseCode::HTTP_UNAUTHORIZED);
    }

    if($relatedBooks)
    {
        $relatedBooks->delete();
    }

    // Delete all media files associated with the book 
    foreach ($book->mediaFiles()->get() as $media) {
        $media->delete();
    }
    $book->delete();

    return $this->success("Book, its sub-books, and its media deleted successfully.", null, self::$responseCode::HTTP_OK);
}

}
