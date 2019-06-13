<?php

namespace App\Http\Controllers\Api;

use App\Jobs\AddUserCommentMessageCount;
use App\Models\Post;
use App\Models\PostComment;
use App\Transformers\PostCommentTransformer;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Redis;

class PostCommentsController extends Controller
{
    public function index(Request $request)
    {
        $query = PostComment::query();
        if ($request->post_id) {
            $query->where('post_id', $request->post_id);
        } else {
            if (Auth::check()) {
                $query->where(function ($query) {
                    $query->whereHas('post', function ($query) {
                        $query->orWhere('user_id', Auth::id());
                    });
                    $query->orWhere('to_user_id', Auth::id());
                });
            } else {
                return $this->response->errorUnauthorized('用户未登录');
            }
        }

        blank($request->q) || $query->where('title', 'like', '%'.$request->q.'%');

        $list = $query->recent()->paginate($request->per_page);

        return $this->response->paginator($list, new PostCommentTransformer());
    }

    public function store(Post $post, Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
//            'images' => 'required',
//            'address' => 'required',
//            'point' => 'required',
        ], [], [
            'content' => '内容',
            'images' => '配图',
            'to_comment_id' => '回复内容',
            'to_user_id' => '回复人',
        ]);

        $comment = new PostComment($request->only(['to_comment_id', 'to_user_id', 'content', 'images']));
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id();
        $comment->save();

        dispatch(new AddUserCommentMessageCount($comment));

        return $this->response->item($comment, new PostCommentTransformer());
    }

    public function show(PostComment $comment)
    {
        return $this->response->item($comment, new PostCommentTransformer());
    }

    public function destroy(PostComment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return $this->response->noContent();
    }
}
