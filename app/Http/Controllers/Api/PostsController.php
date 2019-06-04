<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\PostCategory;
use App\Transformers\PostCategoryTransformer;
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

        $request->category_id && $query->where('category_id', $request->category_id);
        blank($request->q) || $query->where('title', 'like', '%'.$request->q.'%');

        $list = $query->recent()->paginate($request->per_page);

        return $this->response->paginator($list, new ());


    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required',
            'name' => 'required',
            'sex' => 'required',
            'avatar' => 'required',
        ], [], [
            'nickname' => '昵称',
            'name' => '姓名',
            'sex' => '性别',
            'mobile' => '手机号',
            'avatar' => '头像',
        ]);

        Auth::user()->fill($request->only(['nickname', 'name', 'sex', 'mobile', 'avatar']));
        Auth::user()->save();
        return $this->response->item(Auth::user(), new UserTransformer(''));
    }
}
