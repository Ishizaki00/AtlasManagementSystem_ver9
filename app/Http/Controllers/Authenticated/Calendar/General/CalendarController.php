<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\USers\User;
use Auth;
use DB;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function show(){
        $calendar = new CalendarView(time());
        return view('authenticated.calendar.general.calendar', compact('calendar'));
    }

    public function reserve(Request $request){
        DB::beginTransaction();
        try{
            $getPart = $request->getPart;
            $getDate = $request->getData;
            $reserveDays = array_filter(array_combine($getDate, $getPart));
            foreach($reserveDays as $key => $value){
                $reserve_settings = ReserveSettings::where('setting_reserve', $key)->where('setting_part', $value)->first();
                $reserve_settings->decrement('limit_users');
                $reserve_settings->users()->attach(Auth::id());
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
        }
        return redirect()->route('calendar.general.show', ['user_id' => Auth::id()]);
    }

    public function reserveDetail(string $date, int $part)
{
    $reservePersons = ReserveSettings::with('users')
        ->where('setting_reserve', $date)
        ->where('setting_part', $part)
        ->get();

    return view('authenticated.calendar.admin.reserve_detail',
        compact('reservePersons', 'date', 'part'));
}


public function delete(Request $request)
{
    // バリデーション（パイプライン表記）
    $request->validate([
        'date' => 'required|date_format:Y-m-d',
        'part' => 'required|integer', // 必要なら in:1,2,3 などに調整
    ]);

    // 当日を過去扱い → 当日以降はキャンセル不可
    if (Carbon::parse($request->date)->lte(Carbon::today())) {
        return back()->with('error', '当日以降の予約はキャンセルできません。');
    }

    $userId = Auth::id();

    DB::beginTransaction();
    try {
        // 対象枠をロックして取得
        $reserve = ReserveSettings::where('setting_reserve', $request->date)
                    ->where('setting_part', (int)$request->part)
                    ->lockForUpdate()
                    ->first();

        if (!$reserve) {
            DB::rollBack();
            return back()->with('error', '対象の予約枠が見つかりません。');
        }

        // そのユーザーが予約しているか確認
        $has = $reserve->users()->where('users.id', $userId)->exists();
        if (!$has) {
            DB::rollBack();
            return back()->with('error', 'この枠にあなたの予約はありません。');
        }

        // 予約解除 + 枠数を戻す
        $reserve->users()->detach($userId);
        $reserve->increment('limit_users');

        DB::commit();
        return redirect()
            ->route('calendar.general.show', ['user_id' => $userId])
            ->with('message', 'キャンセルが完了しました。');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'キャンセルに失敗しました。');
    }
}

}
