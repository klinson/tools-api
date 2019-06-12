<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\PostCategory;
use App\Transformers\PostCategoryTransformer;
use App\Transformers\PostTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Auth;

class PostsController extends Controller
{
    public function categories()
    {
        return $this->response->collection(PostCategory::getByCache(), new PostCategoryTransformer());
    }

    public function index(Request $request)
    {
        $query = Post::query();
        $query->select()->withCount('comments');

        $request->category_id && $query->where('category_id', $request->category_id);
        blank($request->q) || $query->where('title', 'like', '%'.$request->q.'%');

        if ($request->point) {
            if (is_array($request->point)) {
                $point = $request->point;
            } else {
                $point = explode(',', $request->point);
            }
            $query->withDistance($point[0], $point[1]);
        }

        if ($request->mine == 1) {
            // 获取自己的
            if (! Auth::check()) {
                return $this->response->errorUnauthorized();
            }
            $query->mine();
        }

        $list = $query->top()->recent()->paginate($request->per_page);

        return $this->response->paginator($list, new PostTransformer('list'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'category_id' => 'required',
            'content' => 'required',
            'images' => 'required',
//            'address' => 'required',
//            'point' => 'required',
        ], [], [
            'title' => '标题',
            'category_id' => '所属话题',
            'content' => '内容',
            'images' => '配图',
            'address' => '定位',
            'point' => '定位',
        ]);

        $post = new Post($request->only(['title', 'category_id', 'content', 'images', 'address', 'point']));
        $post->user_id = Auth::id();
        $post->is_top = 0;
        $post->save();

        return $this->response->item($post, new PostTransformer());
    }

    public function update(Post $post, Request $request)
    {
        $this->authorize('is-mine', $post);

        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'images' => 'required',
//            'address' => 'required',
//            'point' => 'required',
        ], [], [
            'title' => '标题',
            'content' => '内容',
            'images' => '配图',
            'address' => '定位',
            'point' => '定位',
        ]);

        $post->fill($request->only(['title', 'content', 'images', 'address', 'point']));
        $post->save();
        return $this->response->item($post, new PostTransformer());
    }

    public function show($post, Request $request)
    {
        $query = Post::where('id', $post)->select()->withPoint();
        if ($request->point) {
            if (is_array($request->point)) {
                $point = $request->point;
            } else {
                $point = explode(',', $request->point);
            }
            $query->withDistance($point[0], $point[1]);
        }
        $post = $query->firstOrFail();

        return $this->response->item($post, new PostTransformer());
    }

    public function destroy(Post $post)
    {
        $this->authorize('is-mine', $post);
        $post->delete();
        return $this->response->noContent();
    }
}
