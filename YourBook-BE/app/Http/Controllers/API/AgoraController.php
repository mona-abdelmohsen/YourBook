<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAgoraTokenRequest;
use App\Models\Auth\User;
use App\Notifications\Call;
use App\Traits\ApiResponse;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Http\JsonResponse;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class AgoraController extends Controller
{
    use ApiResponse;

    /**
     * @param CreateAgoraTokenRequest $request
     * @return JsonResponse
     */
    public function getToken(CreateAgoraTokenRequest $request): JsonResponse
    {
        $appId = "ab31004ffd0f41278725cace531f23f2";
        $appCertificate = "5daa2afc09c24aa7809c3300e3ae62f3";
        $target_user_id = $request->input('target_user_id');
        $targetModel = User::find($target_user_id);

        $channelName = $this->generateCallChannelName(
            auth()->id(),
            $target_user_id
        );

        $caller_uid = auth()->id();
        $expirationTimeInSeconds = 86400;
        $currentTimeStamp = time();
        $privilegeExpiredTs = $currentTimeStamp + $expirationTimeInSeconds;

        $caller_token = RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, $caller_uid, RtcTokenBuilder::RolePublisher, $privilegeExpiredTs);
        $target_token = RtcTokenBuilder::buildTokenWithUid($appId, $appCertificate, $channelName, $target_user_id, RtcTokenBuilder::RoleSubscriber, $privilegeExpiredTs);


        $data = [
            'from_user_id'  => auth()->id(),
            'token'         => $target_token,
            'channel'       => $channelName,
            'uid'           => $target_user_id
        ];

        // Notify clients using Pusher
        Chatify::push("private-chatify.".$target_user_id, 'call', $data);

        // To FCM
        $targetModel->notify(new Call($data));

        return $this->success(
            "success",
            [
                'token'     => $caller_token,
                'channel'   => $channelName,
                'uid'       => $caller_uid
            ],
            self::$responseCode::HTTP_CREATED
        );
    }


    /**
     * @param int $user_A
     * @param int $user_B
     * @return string
     */
    private function generateCallChannelName(int $user_A, int $user_B): string
    {
        $sortedUserIds = [$user_A, $user_B];
        sort($sortedUserIds);

        return $sortedUserIds[0] . '_' . $sortedUserIds[1] . '_call';
    }

}
