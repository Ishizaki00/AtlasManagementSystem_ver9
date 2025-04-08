<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

     public function rules()
{
    return [
        'mail_address' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],

        // 新規ユーザー登録
        'over_name' => ['required', 'string', 'max:10'],
        'under_name' => ['required', 'string', 'max:10'],
        'over_name_kana' => ['required', 'string', 'max:30', 'regex:/^[ァ-ヶー]+$/u'],
        'under_name_kana' => ['required', 'string', 'max:30', 'regex:/^[ァ-ヶー]+$/u'],
        'mail_address' => ['required', 'email', 'max:100', 'unique:users,mail_address'],
        'sex' => ['required', 'in:1,2,3'],
        'role' => ['required', 'in:1,2,3,4'],
        'password' => ['required', 'string', 'min:8', 'max:30', 'confirmed'],
        'old_year' => 'required|digits:4',
        'old_month' => 'required|digits_between:1,2',
        'old_day' => 'required|digits_between:1,2',
            // 'old_year' => ['required_with:old_month,old_day', 'between:2000,' . date('Y')],
            // 'old_month' => ['required_with:old_year,old_day', 'between:1,12'],
            // 'old_day' => ['required_with:old_year,old_month', 'between:1,31'],

 // 合成されるbirthのバリデーション
// 'old_year'  => 'required_with:old_month,old_day|integer|between:2000,' . date('Y'),
//         'old_month' => 'required_with:old_year,old_day|integer|between:1,12',
//         'old_day'   => 'required_with:old_year,old_month|integer|between:1,31',

        'birth' => [
            'required',
            'date',
            'after_or_equal:2000-01-01',
            'before_or_equal:' . now()->format('Y-m-d'),],
];
}

protected function getValidatorInstance()
{
    $data = $this->all();

    $year = $data['old_year'] ?? null;
    $month = $data['old_month'] ?? null;
    $day = $data['old_day'] ?? null;

    if ($year && $month && $day && checkdate((int)$month, (int)$day, (int)$year)) {
        $data['birth'] = $year . '-' . $month . '-' . $day;
    } else {
        $this->merge(['birth' => null]);
    }

    $this->replace($data);
    return parent::getValidatorInstance();
}

public function messages()
    {
        return [
            'over_name.required' => '姓は必須です。',
        'over_name.string' => '姓は文字列で入力してください。',
        'over_name.max' => '姓は最大10文字までです。',

        'under_name.required' => '名は必須です。',
        'under_name.string' => '名は文字列で入力してください。',
        'under_name.max' => '名は最大10文字までです。',

        'over_name_kana.required' => '姓（カナ）は必須です。',
        'over_name_kana.string' => '姓（カナ）は文字列で入力してください。',
        'over_name_kana.max' => '姓（カナ）は最大30文字までです。',
        'over_name_kana.regex' => '姓（カナ）はカタカナのみで入力してください。',

        'under_name_kana.required' => '名（カナ）は必須です。',
        'under_name_kana.string' => '名（カナ）は文字列で入力してください。',
        'under_name_kana.max' => '名（カナ）は最大30文字までです。',
        'under_name_kana.regex' => '名（カナ）はカタカナのみで入力してください。',

        'mail_address.required' => 'メールアドレスは必須です。',
        'mail_address.email' => 'メールアドレスの形式が正しくありません。',
        'mail_address.max' => 'メールアドレスは最大100文字までです。',
        'mail_address.unique' => 'このメールアドレスはすでに登録されています。',

        'sex.required' => '性別は必須です。',
        'sex.in' => '性別の値が不正です。正しい値を選択してください。',

            // 'old_year.required_with' => '生まれた年は必須です。',
            // 'old_year.between' => '生まれた年は2000年から' . date('Y') . '年の間で入力してください。',

            // 'old_month.required_with' => '生まれた月は必須です。',
            // 'old_month.between' => '生まれた月は1〜12の範囲で入力してください。',

            // 'old_day.required_with' => '生まれた日は必須です。',
            // 'old_day.between' => '生まれた日は1〜31の範囲で入力してください。',

            // 'birth.required' => '生年月日は必須です。',
            // 'birth.date' => '正しい日付を入力してください（例: 2/31 や 6/31 は無効です）。',
            'birth.after_or_equal' => '生年月日は2000年1月1日以降の日付を入力してください。',
            'birth.before_or_equal' => '生年月日は今日以前の日付を入力してください。',
            'birth.required' => '生年月日が正しくありません。',
        'old_year.required' => '年を選択してください。',
        'old_month.required' => '月を選択してください。',
        'old_day.required' => '日を選択してください。',

        'role.required' => '役職は必須です。',
        'role.in' => '役職の値が不正です。正しい値を選択してください。',

        'password.required' => 'パスワードは必須です。',
        'password.string' => 'パスワードは文字列で入力してください。',
        'password.min' => 'パスワードは8文字以上で入力してください。',
        'password.max' => 'パスワードは30文字以内で入力してください。',
        'password.confirmed' => 'パスワードが一致しません。',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('mail_address', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'mail_address' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'mail_address' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('mail_address')).'|'.$this->ip();
    }
}
