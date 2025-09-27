$(function () {
  console.log('calendar.js loaded');

  $(document).on('click', '.open-cancel-modal', function (e) {
    e.preventDefault();

    const $btn = $(this);

    // 1) まず data-* から読む（CalendarView 側で付けた場合）
    let date = $btn.data('date');          // 例: '2025-09-30'
    let part = $btn.data('part');          // 例: 1
    let partText = $btn.data('part-text'); // 例: '参加1部'

    // 2) 無い場合は同じセル内の input から拾う（A案）
    if (!date || !part) {
      const $scope = $btn.closest('td, .calendar-cell, .reserve-slot, .day_cell, .calendar-td, .reserve_inner');
      if (!date) date = $scope.find('input[name="getData[]"], input[name="getData"]').val();
      if (!part) part = $scope.find('input[name="getPart[]"], input[name="getPart"]').val();
    }

    // 3) part を数値化（'参加1部' 等から数字抽出も対応）
    if (part == null || part === '') {
      const m = String(partText || '').match(/\d+/);
      part = m ? Number(m[0]) : null;
    } else {
      part = Number(part);
    }

    // 4) 表示＆送信用 hidden
    $('#modalDate').text(date || '—');
    $('#modalPart').text(partText || (part ? `参加${part}部` : '—'));
    $('#cancelDate').val(date || '');
    $('#cancelPart').val(part || '');

    // 5) モーダル
    const modalEl = document.getElementById('cancelModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  });
});
