<x-sidebar>
<p>ユーザー検索</p>
<div class="search_content w-100 border d-flex">
  <!-- 左：ユーザー一覧 -->
  <div class="reserve_users_area w-75">
    @foreach($users as $user)
    <div class="border one_person">
      <div><span>ID : </span><span>{{ $user->id }}</span></div>
      <div><span>名前 : </span>
        <a href="{{ route('user.profile', ['id' => $user->id]) }}">
          <span>{{ $user->over_name }}</span>
          <span>{{ $user->under_name }}</span>
        </a>
      </div>
      <div>
        <span>カナ : </span>
        <span>({{ $user->over_name_kana }}</span>
        <span>{{ $user->under_name_kana }})</span>
      </div>
      <div>
        <span>性別 : </span>
        <span>
          @if($user->sex == 1) 男
          @elseif($user->sex == 2) 女
          @else その他 @endif
        </span>
      </div>
      <div><span>生年月日 : </span><span>{{ $user->birth_day }}</span></div>
      <div>
        <span>権限 : </span>
        <span>
          @if($user->role == 1) 教師(国語)
          @elseif($user->role == 2) 教師(数学)
          @elseif($user->role == 3) 講師(英語)
          @else 生徒 @endif
        </span>
      </div>
      <div>
        @if($user->role == 4)
        <span>選択科目 :</span>
        {{ $user->subjects->isNotEmpty() ? implode('、', $user->subjects->pluck('subject')->toArray()) : 'なし' }}
        @endif
      </div>
    </div>
    @endforeach
  </div>

  <!-- 右：検索フォーム -->
  <div class="search_area w-25 border">
    <form action="{{ route('user.show') }}" method="get" id="userSearchRequest">
      <div>
        <input type="text" class="free_word" name="keyword" placeholder="キーワードを検索">
      </div>

      <div>
        <label>カテゴリ</label>
        <select name="category">
          <option value="name">名前</option>
          <option value="id">社員ID</option>
        </select>
      </div>

      <div>
        <label>並び替え</label>
        <select name="updown">
          <option value="ASC">昇順</option>
          <option value="DESC">降順</option>
        </select>
      </div>

      <div>
        <p class="m-0 search_conditions"><span>検索条件の追加</span></p>
        <div class="search_conditions_inner">
          <div>
            <label>性別</label>
            <span>男</span><input type="radio" name="sex" value="1">
            <span>女</span><input type="radio" name="sex" value="2">
            <span>その他</span><input type="radio" name="sex" value="3">
          </div>

          <div>
            <label>権限</label>
            <select name="role" class="engineer">
              <option selected disabled>----</option>
              <option value="1">教師(国語)</option>
              <option value="2">教師(数学)</option>
              <option value="3">教師(英語)</option>
              <option value="4">生徒</option>
            </select>
          </div>

          <div class="selected_engineer">
            <label>選択科目</label>
            @foreach ($subjects as $subject)
              <div>
                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                  {{ (is_array(request()->input('subjects')) && in_array($subject->id, request()->input('subjects'))) ? 'checked' : '' }}>
                <label>{{ $subject->subject }}</label>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div>
        <button type="button" onclick="resetSearch()">リセット</button>
      </div>
      <script>
        function resetSearch() {
          // 元の検索画面URLにパラメータ無しでリダイレクト
          window.location.href = "{{ route('user.show') }}";
        }
      </script>

      <div>
        <input type="submit" name="search_btn" value="検索">
      </div>
    </form>
  </div>
</div>
</x-sidebar>
