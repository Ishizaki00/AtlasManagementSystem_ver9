<x-sidebar>
<div class="vh-100 pt-5" style="background:#ECF1F6;">
  <div class="border w-75 m-auto pt-5 pb-5" style="border-radius:5px; background:#FFF;">
    <div class="w-75 m-auto border" style="border-radius:5px;">

      <p class="text-center">{{ $calendar->getTitle() }}</p>
      <div class="">
        {!! $calendar->render() !!}
      </div>
    </div>
    <div class="text-right w-75 m-auto">
      <input type="submit" class="btn btn-primary" value="予約する" form="reserveParts">
    </div>
  </div>
</div>

<!-- キャンセル確認モーダル -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-body">
        <p>予約日：<span id="modalDate"></span></p>
        <p>時間：<span id="modalPart"></span></p>
        <p>上記の予約をキャンセルしてもよろしいですか？</p>

        <!-- ▼ここから追加：フォームで送信 -->
        <form id="cancelForm" method="POST" action="{{ route('deleteParts') }}" class="d-flex justify-content-between mt-3 w-100">
          @csrf
          <input type="hidden" name="date" id="cancelDate" />
          <input type="hidden" name="part" id="cancelPart" />
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">閉じる</button>
          <button type="submit" class="btn btn-danger">キャンセルする</button>
        </form>
        <!-- ▲ここまで追加 -->
      </div>
    </div>
  </div>
</div>

{{-- Bootstrap 5 の JS（Popper 同梱 bundle） --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/calendar.js') }}"></script>
</x-sidebar>
