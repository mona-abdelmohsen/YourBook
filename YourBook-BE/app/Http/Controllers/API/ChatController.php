<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\ChFavorite as Favorite;
use App\Models\ChMessage as Message;
use App\Notifications\NewChatMessage;
use App\Traits\ApiResponse;
use App\Traits\Mapping;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Pusher\Pusher;
use Pusher\PusherException;
use Kreait\Firebase\Messaging\CloudMessage; 
use Illuminate\Support\Facades\Log;      


class ChatController extends Controller
{
    use ApiResponse, Mapping;

    protected int $perPage = 30;
    private Pusher $pusher;

    /**
     * @throws PusherException
     */
    public function auth(Request $request): JsonResponse
    {
        $this->pusher = new Pusher(
            config('chatify.pusher.key'),
            config('chatify.pusher.secret'),
            config('chatify.pusher.app_id'),
            config('chatify.pusher.options'),
        );

        // Auth data
        $authData = json_encode([
            'user_id' => auth()->id(),
            'user_info' => [
                'name' => auth()->user()->name
            ]
        ]);
        // check if user authenticated
        if (Auth::check()) {
            if($request->user()->id == auth()->id()){
                $data = json_decode($this->pusher->authorizeChannel(
                    $request['channel_name'],
                    $request['socket_id'],
                    $authData
                ));
                return $this->success('success', $data,self::$responseCode::HTTP_CREATED);
            }
            // if not authorized
            return $this->error('Unauthorized', null, self::$responseCode::HTTP_UNAUTHORIZED);
        }
        return $this->error('Unauthenticated', null, self::$responseCode::HTTP_UNAUTHORIZED);
    }

    /**
     * Send a message to database
     */
    // public function send(Request $request): JsonResponse
    // {
    //     // default variables
    //     $error = (object)[
    //         'status' => 0,
    //         'message' => null
    //     ];
    //     $attachment = null;
    //     $attachment_title = null;

    //     // if there is attachment [file]
    //     if ($request->hasFile('file')) {
    //         // allowed extensions
    //         $allowed_images = Chatify::getAllowedImages();
    //         $allowed_files  = Chatify::getAllowedFiles();
    //         $allowed        = array_merge($allowed_images, $allowed_files);

    //         $file = $request->file('file');
    //         // check file size
    //         if ($file->getSize() < Chatify::getMaxUploadSize()) {
    //             if (in_array(strtolower($file->extension()), $allowed)) {
    //                 // get attachment name
    //                 $attachment_title = $file->getClientOriginalName();
    //                 // upload attachment and store the new name
    //                 $attachment = Str::uuid() . "." . $file->extension();
    //                 $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
    //             } else {
    //                 $error->status = 1;
    //                 $error->message = "File extension not allowed!";
    //             }
    //         } else {
    //             $error->status = 1;
    //             $error->message = "File size you are trying to upload is too large!";
    //         }
    //     }

    //     if (!$error->status) {
    //         // send to database
    //         $messageText = htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8');
    //         $message = Chatify::newMessage([
    //             'type' => 'user',
    //             'from_id' => Auth::user()->id,
    //             'to_id' => $request['user_id'],
    //             'body' => $messageText,
    //             'attachment' => ($attachment) ? json_encode((object)[
    //                 'new_name' => $attachment,
    //                 'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
    //             ]) : null,
    //         ]);

    //         // fetch message to send it with the response
    //         $messageData = Chatify::parseMessage($message);

    //         // send to user using pusher
    //         if (Auth::user()->id != $request['user_id']) {
    //             $user = Auth::user();
    //             $target = User::find($request['user_id']);

    //             Chatify::push("private-chatify.".$request['user_id'], 'messaging', [
    //                 'from_id' => $user->id,
    //                 'name'  => $user->name,
    //                 'avatar' => $user->avatar? url('storage/avatars/'.basename($user->avatar)): null,
    //                 'to_id' => $request['user_id'],
    //                 'message' => $messageData
    //             ]);

    //             $target->notify(new NewChatMessage($user, $messageText, $messageData));
    //         }
    //         return $this->success("success", $messageData, self::$responseCode::HTTP_OK);
    //     }

    //     return $this->error("Error", $error, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
    // }

    public function send(Request $request): JsonResponse 
{
    // Default variables
    $error = (object)[
        'status' => 0,
        'message' => null
    ];
    $attachment = null;
    $attachment_title = null;

    // If there is an attachment [file]
    if ($request->hasFile('file')) {
        // Allowed extensions
        $allowed_images = Chatify::getAllowedImages();
        $allowed_files  = Chatify::getAllowedFiles();
        $allowed        = array_merge($allowed_images, $allowed_files);

        $file = $request->file('file');
        // Check file size
        if ($file->getSize() < Chatify::getMaxUploadSize()) {
            if (in_array(strtolower($file->extension()), $allowed)) {
                // Get attachment name
                $attachment_title = $file->getClientOriginalName();
                // Upload attachment and store the new name
                $attachment = Str::uuid() . "." . $file->extension();
                $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
            } else {
                $error->status = 1;
                $error->message = "File extension not allowed!";
            }
        } else {
            $error->status = 1;
            $error->message = "File size you are trying to upload is too large!";
        }
    }

    if (!$error->status) {
        // Send to database
        $messageText = htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8');
        $message = Chatify::newMessage([
            'type' => 'user',
            'from_id' => Auth::user()->id,
            'to_id' => $request['user_id'],
            'body' => $messageText,
            'attachment' => ($attachment) ? json_encode((object)[
                'new_name' => $attachment,
                'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
            ]) : null,
        ]);

        // Fetch message to send it with the response
        $messageData = Chatify::parseMessage($message);

        // Send to user using Pusher
        if (Auth::user()->id != $request['user_id']) {
            $user = Auth::user();
            $target = User::find($request['user_id']);

            // Pusher notification
            Chatify::push("private-chatify." . $request['user_id'], 'messaging', [
                'from_id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? url('storage/avatars/' . basename($user->avatar)) : null,
                'to_id' => $request['user_id'],
                'message' => $messageData
            ]);

            $target->notify(new NewChatMessage($user, $messageText, $messageData));

            // FCM notification
            $this->sendFcmNotification($target, $messageText, $messageData);
        }

        return $this->success("success", $messageData, self::$responseCode::HTTP_OK);
    }

    return $this->error("Error", $error, self::$responseCode::HTTP_UNPROCESSABLE_ENTITY);
}

/**
 * Send FCM notification to the user.
 *
 * @param User $target
 * @param string $messageText
 * @param array $messageData
 * @return void
 */
private function sendFcmNotification(User $target, string $messageText, array $messageData): void
{
    $fcmTokens = $target->fcm_tokens ?? []; // Assuming the user's FCM tokens are stored in an array or JSON format.

    if (!empty($fcmTokens)) {
        foreach ($fcmTokens as $token) {
            try {
                $messaging = app('firebase.messaging'); // Ensure Firebase is properly configured
                $message = CloudMessage::fromArray([
                    'token' => $token,
                    'notification' => [
                        'title' => 'New Message from ' . Auth::user()->name,
                        'body' => $messageText,
                    ],
                    'data' => [
                        'from_id' => Auth::user()->id,
                        'message' => json_encode($messageData),
                    ],
                ]);
                $messaging->send($message);
            } catch (\Exception $e) {
                Log::error("Failed to send FCM notification: " . $e->getMessage());
            }
        }
    }
}


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch(Request $request): JsonResponse
    {
        $query = Chatify::fetchMessagesQuery($request['user_id'])->latest();
        $messages = $query->paginate($request->per_page ?? $this->perPage);
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
        $messages->transform([$this, 'messageMap']);
        $response = [
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => $messages->items(),
        ];
        return $this->success("success", $response, self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function seen(Request $request): JsonResponse
    {
        // make as seen
        $seen = Chatify::makeSeen($request['user_id']);
        // send the response
        return $this->success('success', $seen, self::$responseCode::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConversation(Request $request): JsonResponse
    {
        // delete
        $delete = Chatify::deleteConversation($request['user_id']);

        // send the response
        return $this->success('success', [
            'deleted' => $delete,
        ], self::$responseCode::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMessage(Request $request): JsonResponse
    {
        $delete = Chatify::deleteMessage($request['message_id']);

        return $this->success('success', [
            'deleted' => $delete,
        ], self::$responseCode::HTTP_OK);
    }

    /**
     * @param $fileName
     * @return JsonResponse
     */
    public function download($fileName): JsonResponse
    {
        $path = config('chatify.attachments.folder') . '/' . $fileName;
        if (Chatify::storage()->exists($path)) {
            return $this->success('success', [
                'file_name' => $fileName,
                'download_path' => Chatify::storage()->url($path)
            ], self::$responseCode::HTTP_OK);
        } else {
            return $this->error('Sorry, File does not exist in our server or may have been deleted!', null, self::$responseCode::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getContacts(Request $request): JsonResponse
    {
        // get all users that received/sent message from/to [Auth user]
        $users = Message::join('users', function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
            ->where(function ($q) {
                $q->where('ch_messages.from_id', Auth::user()->id)
                    ->orWhere('ch_messages.to_id', Auth::user()->id);
            })
            ->where('users.id', '!=', Auth::user()->id)
            ->select([
                'users.*',
                DB::raw('MAX(ch_messages.created_at) as max_created_at'),
                DB::raw('SUM(CASE WHEN ch_messages.seen = 0 AND ch_messages.to_id = ' . Auth::user()->id . ' THEN 1 ELSE 0 END) as unseen_count'),
                DB::raw('(SELECT body FROM ch_messages m WHERE (m.from_id = users.id OR m.to_id = users.id) AND (m.from_id = ' . Auth::user()->id . ' OR m.to_id = ' . Auth::user()->id . ') ORDER BY m.created_at DESC LIMIT 1) as body'),
                DB::raw('(SELECT attachment FROM ch_messages m WHERE (m.from_id = users.id OR m.to_id = users.id) AND (m.from_id = ' . Auth::user()->id . ' OR m.to_id = ' . Auth::user()->id . ') ORDER BY m.created_at DESC LIMIT 1) as attachment'),
                DB::raw('(SELECT seen FROM ch_messages m WHERE (m.from_id = users.id OR m.to_id = users.id) AND (m.from_id = ' . Auth::user()->id . ' OR m.to_id = ' . Auth::user()->id . ') ORDER BY m.created_at DESC LIMIT 1) as seen'),
                DB::raw('(SELECT from_id FROM ch_messages m WHERE (m.from_id = users.id OR m.to_id = users.id) AND (m.from_id = ' . Auth::user()->id . ' OR m.to_id = ' . Auth::user()->id . ') ORDER BY m.created_at DESC LIMIT 1) as from_id'),
            ])
            ->groupBy('users.id')
            ->orderBy('max_created_at', 'desc')
            ->paginate($request->per_page ?? $this->perPage);

        $users->transform([$this, 'contactUserMap']);

        return $this->success('success', [
            'contacts' => $users->items(),
            'total' => $users->total() ?? 0,
            'last_page' => $users->lastPage() ?? 1,
        ], self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setActiveStatus(Request $request): JsonResponse
    {
        $activeStatus = $request['status'] > 0 ? 1 : 0;
        User::where('id', Auth::user()->id)->update(['active_status' => $activeStatus]);
        return $this->success('success', [
            'status'    => $activeStatus,
        ], self::$responseCode::HTTP_OK);
    }

    public function sharedPhotos(Request $request): JsonResponse
    {
        $images = array(); // Default
        // Get messages
        $msgs = Chatify::fetchMessagesQuery($request['user_id'])
            ->orderBy('created_at', 'DESC')
            ->whereNotNull('attachment')->select('attachment')
            ->paginate($request->per_page ?? $this->perPage);
        $msgs->map([$this, 'messageMap']);
        $msgs->transform(function($msg){
            return $msg->attachment;
        });

        // send the response
        return $this->success('success', [
            'files' => $msgs->items(),
            'total' => $msgs->total() ?? 0,
            'last_page' => $msgs->lastPage() ?? 1,
        ], self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function favorite(Request $request): JsonResponse
    {
        $userId = $request['user_id'];
        if($userId == auth()->id()){
            return $this->success('success', [
                'star' => 0,
            ], self::$responseCode::HTTP_OK);
        }
        // check action [star/unstar]
        $favoriteStatus = Chatify::inFavorite($userId) ? 0 : 1;
        Chatify::makeInFavorite($userId, $favoriteStatus);

        // send the response
        return $this->success('success', [
            'star' => @$favoriteStatus,
        ], self::$responseCode::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFavorites(Request $request)
    {
        $favorites = Favorite::where('user_id', Auth::user()->id)
            ->join('users', 'users.id', 'ch_favorites.favorite_id')
            ->select('users.*')
            ->get()->map([$this, 'userMap']);

        return $this->success('success', [
            'total' => count($favorites),
            'favorites' => $favorites ?? [],
        ], self::$responseCode::HTTP_OK);
    }
}
