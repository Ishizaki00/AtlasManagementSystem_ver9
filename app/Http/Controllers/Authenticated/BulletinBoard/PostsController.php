<?php

namespace App\Http\Controllers\Authenticated\BulletinBoard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\MainCategory;
use App\Models\Categories\SubCategory;
use App\Models\Posts\Post;
use App\Models\Posts\PostComment;
use App\Models\Posts\Like;
use App\Models\Users\User;
use App\Http\Requests\BulletinBoard\PostFormRequest;
use Auth;
use Illuminate\Validation\Rule;

class PostsController extends Controller
{
public function show(Request $request)
{
    // サブカテゴリ付きでメインカテゴリを取得
    $categories = MainCategory::with('subCategories')->get();

    // すべての投稿を取得
    $posts = Post::with('user', 'postComments')->get();

    // 「いいね」モデルなどを渡すための準備
    $like = new Like;
    $post_comment = new Post;

    // 検索ワードによる絞り込み（サブカテゴリ名と一致する場合）
    if (!empty($request->keyword)) {
    $matchedSub = SubCategory::where('sub_category', $request->keyword)->first();

    if ($matchedSub) {
        $posts = Post::whereHas('subCategories', function ($query) use ($matchedSub) {
            $query->where('sub_categories.id', $matchedSub->id);
        })->with('user', 'postComments')->get();
    } else {
        $posts = Post::with('user', 'postComments')
            ->where('post_title', 'like', '%' . $request->keyword . '%')
            ->orWhere('post', 'like', '%' . $request->keyword . '%')->get();
    }
}
    // サブカテゴリーのボタンクリック
    elseif ($request->sub_category_name) {
       $matchedSub = SubCategory::where('sub_category', $request->sub_category_name)->first();

    if ($matchedSub) {
        $posts = Post::whereHas('subCategories', function ($query) use ($matchedSub) {
            $query->where('sub_categories.id', $matchedSub->id);
        })->with('user', 'postComments')->get();
    } else {
        $posts = collect();
    }
    }
    // いいねした投稿
    elseif ($request->like_posts) {
        $likes = Auth::user()->likePostId()->get('like_post_id');
        $posts = Post::with('user', 'postComments')
            ->whereIn('id', $likes)->get();
    }
    // 自分の投稿
    elseif ($request->my_posts) {
        $posts = Post::with('user', 'postComments')
            ->where('user_id', Auth::id())->get();
    }

    // ビューに必要なデータを渡して表示
    return view('authenticated.bulletinboard.posts', compact('posts', 'categories', 'like', 'post_comment'));
}

    public function postDetail($post_id){
        $post = Post::with('user', 'postComments')->findOrFail($post_id);
        return view('authenticated.bulletinboard.post_detail', compact('post'));
    }

    public function postInput(){
        $main_categories = MainCategory::get();
        return view('authenticated.bulletinboard.post_create', compact('main_categories'));
    }

    public function postCreate(PostFormRequest $request){
        $post = Post::create([
            'user_id' => Auth::id(),
            'post_title' => $request->post_title,
            'post' => $request->post_body
        ]);
        $post->subCategories()->attach($request->sub_category_id);
        return redirect()->route('post.show');
    }

    public function postEdit(Request $request){
        // バリデーションの追加
            $request->validate([
                'post_title' => ['required', 'string', 'max:100'],
                'post_body' => ['required', 'string', 'max:2000'],
            ], [
                'post_title.required' => 'タイトルは必須項目です。',
                'post_title.string' => 'タイトルは文字列で入力してください。',
                'post_title.max' => 'タイトルは100文字以内で入力してください。',
                'post_body.required' => '投稿内容は必須項目です。',
                'post_body.string' => '投稿内容は文字列で入力してください。',
                'post_body.max' => '投稿内容は2000文字以内で入力してください。',
            ]);

        //投稿の更新
        Post::where('id', $request->post_id)->update([
            'post_title' => $request->post_title,
            'post' => $request->post_body,
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function postDelete($id){
        Post::findOrFail($id)->delete();
        return redirect()->route('post.show');
    }
    public function mainCategoryCreate(Request $request)
    {
        $request->validate([
            'main_category_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('main_categories', 'main_category'),
            ],
        ], [
            'main_category_name.required' => 'メインカテゴリー名は必須項目です。',
            'main_category_name.string' => 'メインカテゴリー名は文字列で入力してください。',
            'main_category_name.max' => 'メインカテゴリー名は100文字以内で入力してください。',
            'main_category_name.unique' => '既に同名のメインカテゴリーが存在します。',
        ]);

        MainCategory::create(['main_category' => $request->main_category_name]);
        return redirect()->route('post.input');
    }

    public function commentCreate(Request $request){
        $request->validate([
            'comment' => ['required', 'string', 'max:250'],
        ], [
            'comment.required' => 'コメントは必須項目です。',
            'comment.string' => 'コメントは文字列で入力してください。',
            'comment.max' => 'コメントは250文字以内で入力してください。',
        ]);

        PostComment::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);
        return redirect()->route('post.detail', ['id' => $request->post_id]);
    }

    public function myBulletinBoard(){
        $posts = Auth::user()->posts()->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_myself', compact('posts', 'like'));
    }

    public function likeBulletinBoard(){
        $like_post_id = Like::with('users')->where('like_user_id', Auth::id())->get('like_post_id')->toArray();
        $posts = Post::with('user')->whereIn('id', $like_post_id)->get();
        $like = new Like;
        return view('authenticated.bulletinboard.post_like', compact('posts', 'like'));
    }

    public function postLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->like_user_id = $user_id;
        $like->like_post_id = $post_id;
        $like->save();

        return response()->json();
    }

    public function postUnLike(Request $request){
        $user_id = Auth::id();
        $post_id = $request->post_id;

        $like = new Like;

        $like->where('like_user_id', $user_id)
             ->where('like_post_id', $post_id)
             ->delete();

        return response()->json();
    }

    // サブカテゴリー
public function subCategoryCreate(Request $request)
{
    $request->validate([
        'main_category_id' => ['required', 'integer'], // メインカテゴリがないと困るので
        'sub_category' => [
            'required',
            'string',
            'max:100',
            Rule::unique('sub_categories', 'sub_category')->where(function ($query) use ($request) {
                return $query->where('main_category_id', $request->main_category_id);
            })
        ],
    ], [
        'main_category_id.required' => 'メインカテゴリーは必須です。',
        'main_category_id.integer' => 'メインカテゴリーIDが正しくありません。',
        'sub_category.required' => 'サブカテゴリー名は必須項目です。',
        'sub_category.string' => 'サブカテゴリー名は文字列で入力してください。',
        'sub_category.max' => 'サブカテゴリー名は100文字以内で入力してください。',
        'sub_category.unique' => '同じメインカテゴリー内に同名のサブカテゴリーは登録できません。',
    ]);

    SubCategory::create([
        'main_category_id' => $request->main_category_id,
        'sub_category' => $request->sub_category,
    ]);

    return redirect()->route('post.input')->with('success', 'サブカテゴリーを追加しました');
}
}
