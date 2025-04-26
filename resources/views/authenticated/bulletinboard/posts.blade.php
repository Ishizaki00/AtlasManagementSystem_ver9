<x-sidebar>
<div class="board_area w-100 border m-auto d-flex">
  <div class="post_view w-75 mt-5">
    <p class="w-75 m-auto">投稿一覧</p>
    <!-- @foreach($posts as $post)
    <div class="post_area border w-75 m-auto p-3">
      <p><span>{{ $post->user->over_name }}</span><span class="ml-3">{{ $post->user->under_name }}</span>さん</p>
      <p><a href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->post_title }}</a></p>



      <div class="post_bottom_area d-flex">
        <div class="d-flex post_status">
          <div class="mr-5">
            <i class="fa fa-comment"></i><span class=""></span>
          </div>
          <div>
            @if(Auth::user()->is_Like($post->id))
            <p class="m-0"><i class="fas fa-heart un_like_btn" post_id="{{ $post->id }}"></i><span class="like_counts{{ $post->id }}"></span></p>
            @else
            <p class="m-0"><i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i><span class="like_counts{{ $post->id }}"></span></p>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endforeach -->
    @foreach($posts as $post)
  <div class="post_area border w-75 m-auto p-3">
    <p>
      <span>{{ $post->user->over_name }}</span>
      <span class="ml-3">{{ $post->user->under_name }}</span>さん
    </p>
    <p>
      <a href="{{ route('post.detail', ['id' => $post->id]) }}">{{ $post->post_title }}</a>
    </p>

    <div class="post_bottom_area d-flex">
      <div class="d-flex post_status">

        <!-- コメント数 -->
        <div class="mr-5">
          <i class="fa fa-comment"></i>
          <span>{{ $post->postComments->count() }}</span>
        </div>

        <!-- いいね -->
        <div>
          @if(Auth::user()->is_Like($post->id))
            <p class="m-0">
              <i class="fas fa-heart text-danger un_like_btn" post_id="{{ $post->id }}"></i>
              <span class="like_counts{{ $post->id }}">{{ $post->likes->count() }}</span>
            </p>
          @else
            <p class="m-0">
              <i class="fas fa-heart like_btn" post_id="{{ $post->id }}"></i>
              <span class="like_counts{{ $post->id }}">{{ $post->likes->count() }}</span>
            </p>
          @endif
        </div>

      </div>
    </div>
  </div>
@endforeach

  </div>
  <div class="other_area border w-25">
    <div class="border m-4">
      <div class=""><a href="{{ route('post.input') }}">投稿</a></div>
      <div class="">
        <input type="text" placeholder="キーワードを検索" name="keyword" form="postSearchRequest">
        <input type="submit" value="検索" form="postSearchRequest">
      </div>
      <input type="submit" name="like_posts" class="category_btn" value="いいねした投稿" form="postSearchRequest">
      <input type="submit" name="my_posts" class="category_btn" value="自分の投稿" form="postSearchRequest">
      <ul>
        @foreach($categories as $category)
        <li class="main_categories" category_id="{{ $category->id }}"><span>{{ $category->main_category }}<span></li>
        @endforeach
      </ul>
    </div>
  </div>
  <form action="{{ route('post.show') }}" method="get" id="postSearchRequest"></form>
</div>

<!-- 投稿編集モーダル -->
 <!-- <div class="modal fade" id="editPostModal" tabindex="-1" role="dialog" aria-labelledby="editPostModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('post.update', ['id' => '__ID__']) }}" id="editPostForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPostModalLabel">投稿を編集</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>タイトル</label>
            <input type="text" name="post_title" id="editPostTitle" class="form-control" required>
          </div>
          <div class="form-group">
            <label>内容</label>
            <textarea name="post_content" id="editPostContent" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">更新</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
        </div>
      </div>
    </form>
  </div>
</div> -->
<!-- 投稿編集モーダル -->
<!-- 投稿編集モーダル -->
<div class="modal fade" id="editPostModal" tabindex="-1" role="dialog" aria-labelledby="editPostModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('post.edit') }}">
        @csrf
        <input type="hidden" name="post_id" id="edit-post-id">

        <div class="modal-header">
          <h5 class="modal-title" id="editPostModalLabel">投稿の編集</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="edit-post-title">タイトル</label>
            <input type="text" name="post_title" id="edit-post-title" class="form-control">
          </div>

          <div class="form-group">
            <label for="edit-post-body">本文</label>
            <textarea name="post_body" id="edit-post-body" class="form-control" rows="5"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">保存</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
        </div>
      </form>
    </div>
  </div>
</div>


</x-sidebar>
