<?php
namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView{

  private $carbon;
  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  public function getTitle(){
    return $this->carbon->format('Y年n月');
  }

  function render(){
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] = '<table class="table">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th>月</th>';
    $html[] = '<th>火</th>';
    $html[] = '<th>水</th>';
    $html[] = '<th>木</th>';
    $html[] = '<th>金</th>';
    $html[] = '<th>土</th>';
    $html[] = '<th>日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';

    $weeks = $this->getWeeks();
    foreach($weeks as $week){
      $html[] = '<tr class="'.$week->getClassName().'">';

      $days = $week->getDays();
      foreach($days as $day){
        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay    = $this->carbon->copy()->format("Y-m-d");

        // 過去日判定（当日は過去扱い）
        $ymd    = $day->everyDay();
        $isPast = Carbon::parse($ymd)->lte(Carbon::today());

        // td クラス
        if($startDay <= $ymd && $toDay >= $ymd){
          $tdClass = 'calendar-td';
        }else{
          $tdClass = 'calendar-td '.$day->getClassName();
        }
        if ($isPast) {
          $tdClass .= ' is-past';
        }
        $html[] = '<td class="'. $tdClass .'">';

        // 日付数字
        $html[] = $day->render();

        // 予約済み
if(in_array($day->everyDay(), $day->authReserveDay())){
  // 何部か（表示用）
  $reservePart = $day->authReserveDate($day->everyDay())->first()->setting_part;
  if($reservePart == 1){
    $reservePart = "リモ1部";
  }else if($reservePart == 2){
    $reservePart = "リモ2部";
  }else if($reservePart == 3){
    $reservePart = "リモ3部";
  }

  // 参加した“部数”
  $attendedUnits = $day->authReserveDate($day->everyDay())->count();
  $reserveLabel  = '参加'.$attendedUnits.'部';

  if($isPast){
    // ★過去日：テキストのみ（クリック不可）
    $html[] = '<p class="m-auto p-0 w-75" style="font-size:12px">'.$reserveLabel.'</p>';
    $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
  }else{
    // ★未来日：モーダル起動ボタン（クリック可・送信しない）
    $html[] = '<button type="button"
                      class="btn btn-secondary p-0 w-75 open-cancel-modal"
                      data-date="'.$day->everyDay().'"
                      data-part-text="'.$reservePart.'"
                      style="font-size:12px">'.$reserveLabel.'</button>';
    $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
  }
}else{
  // 未予約：過去は受付終了／未来は選択
  if($isPast){
    $html[] = '<p class="m-auto p-0 w-75" style="font-size:12px">受付終了</p>';
    $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
  }else{
    $html[] = $day->selectPart($day->everyDay());
  }
}

        // hiddenで日付は必ず出す
        $html[] = $day->getDate();
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';
    $html[] = '<form action="/reserve/calendar" method="post" id="reserveParts">'.csrf_field().'</form>';
    $html[] = '<form action="/delete/calendar" method="post" id="deleteParts">'.csrf_field().'</form>';

    return implode('', $html);
  }

  protected function getWeeks(){
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    while($tmpDay->lte($lastDay)){
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}
