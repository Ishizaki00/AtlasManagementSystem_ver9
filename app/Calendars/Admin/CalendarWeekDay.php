<?php
namespace App\Calendars\Admin;

use Carbon\Carbon;
use App\Models\Calendars\ReserveSettings;

class CalendarWeekDay{
  protected $carbon;

  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  function getClassName(){
    return "day-" . strtolower($this->carbon->format("D"));
  }

  function render(){
    return '<p class="day">' . $this->carbon->format("j") . '日</p>';
  }

  function everyDay(){
    return $this->carbon->format("Y-m-d");
  }

  function dayPartCounts($ymd){
    $html = [];
    $one_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first();
    $two_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first();
    $three_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first();

    $html[] = '<div class="text-left">';
     // 1部
    if($one_part){
        $reserved = $one_part ? $one_part->users->count() : 0;
        $canReserve = ($one_part->limit_users > 0) && Carbon::parse($ymd)->isFuture();

        if($canReserve){
            $url = route('calendar.admin.detail', ['date' => $ymd, 'part' => 1]);
            $html[] = "<p class='day_part m-0 pt-1'><a href='{$url}' class='text-primary'>1部</a> <span class='text-muted'>{$reserved}</span></p>";
        }else{
            $html[] = "<p class='day_part m-0 pt-1 text-muted'>1部 {$reserved}</p>";
        }
    }

    // 2部
    if($two_part){
        $reserved = $two_part ? $two_part->users->count() : 0;
        $canReserve = ($two_part->limit_users > 0) && Carbon::parse($ymd)->isFuture();

        if($canReserve){
            $url = route('calendar.admin.detail', ['date' => $ymd, 'part' => 2]);
            $html[] = "<p class='day_part m-0 pt-1'><a href='{$url}' class='text-primary'>2部</a> <span class='text-muted'>{$reserved}</span></p>";
        }else{
            $html[] = "<p class='day_part m-0 pt-1 text-muted'>2部 {$reserved}</p>";
        }
    }

    // 3部
    if($three_part){
        $reserved = $three_part ? $three_part->users->count() : 0;
        $canReserve = ($three_part->limit_users > 0) && Carbon::parse($ymd)->isFuture();

        if($canReserve){
            $url = route('calendar.admin.detail', ['date' => $ymd, 'part' => 3]);
            $html[] = "<p class='day_part m-0 pt-1'><a href='{$url}' class='text-primary'>3部</a> <span class='text-muted'>{$reserved}</span></p>";
        }else{
            $html[] = "<p class='day_part m-0 pt-1 text-muted'>3部 {$reserved}</p>";
        }
    }

    $html[] = '</div>';
    return implode("", $html);
}


  function onePartFrame($day){
    $one_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '1')->first();
    if($one_part_frame){
      $one_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '1')->first()->limit_users;
    }else{
      $one_part_frame = "20";
    }
    return $one_part_frame;
  }
  function twoPartFrame($day){
    $two_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '2')->first();
    if($two_part_frame){
      $two_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '2')->first()->limit_users;
    }else{
      $two_part_frame = "20";
    }
    return $two_part_frame;
  }
  function threePartFrame($day){
    $three_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '3')->first();
    if($three_part_frame){
      $three_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '3')->first()->limit_users;
    }else{
      $three_part_frame = "20";
    }
    return $three_part_frame;
  }

  //
  function dayNumberAdjustment(){
    $html = [];
    $html[] = '<div class="adjust-area">';
    $html[] = '<p class="d-flex m-0 p-0">1部<input class="w-25" style="height:20px;" name="1" type="text" form="reserveSetting"></p>';
    $html[] = '<p class="d-flex m-0 p-0">2部<input class="w-25" style="height:20px;" name="2" type="text" form="reserveSetting"></p>';
    $html[] = '<p class="d-flex m-0 p-0">3部<input class="w-25" style="height:20px;" name="3" type="text" form="reserveSetting"></p>';
    $html[] = '</div>';
    return implode('', $html);
  }
}
