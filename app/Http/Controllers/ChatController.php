<?php

namespace App\Http\Controllers;

use App\AttachmentImage;
use App\Events\MessageCreated;
use App\Extensions\Attachments\AttachmentFactory;
use App\Message;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ChatController extends Controller
{
    /**
     * ChatController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showChat()
    {
        return view('index')->with([
            'user' => Auth::user(),
        ]);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getMessages($limit = 50, $offset = 0)
    {
        $messages = Message::with([
            'likes',
            'attachmentImages',
            'attachmentLinks',
            'attachmentYoutubes',
        ])->orderByDesc('created_at')->skip($offset)->take($limit)->get();

        $messagesTaken = $limit + $offset;

        $haveElseMessages = Message::take($messagesTaken + 1)->count() > $messagesTaken;

        return [
            'messages' => $messages,
            'haveElseMessages' => $haveElseMessages,
        ];
    }

    /**
     * @param Request $request
     */
    public function postMessage(Request $request)
    {
        $user = $request->user();

        $messageText = $request->input('message');

        $attachments = $request->input('attachments');

        $message = new Message();

        $message->fill([
            'author' => $user->username,
            'content' => $messageText,
            'user_id' => $user->id,
        ])->save();

        AttachmentFactory::buildAttachments($message, $attachments);

        event(new MessageCreated($message));
    }

    /**
     * @param Message $message
     */
    public function deleteMessage(Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();
    }

    /**
     * @param Message $message
     */
    public function likeMessage(Message $message)
    {
        $this->authorize('like', $message);

        $message->like();
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function postAttachmentImage(Request $request)
    {
        $uploadedFile = $request->file('image');

        $rules = array(
            'image' => 'image|mimes:jpeg,jpg,png,gif|required|max:51200' // max 50Mb
        );

        $validator = Validator::make([
            'image' => $uploadedFile,
        ], $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 400);
        }

        $newFileName = AttachmentImage::storeImageToTempDirectory($uploadedFile);

        return [
            'fileName' => $newFileName,
        ];
    }

    /**
     * @param Request $request
     */
    public function deleteTemporallyAttachmentImage(Request $request)
    {
        $fileName = $request->get('fileName');

        Storage::disk('temp')->delete($fileName);
    }

}
