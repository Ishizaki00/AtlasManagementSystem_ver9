<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
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
            'birth' => [
                'required',
                'date',
                'after_or_equal:2000-01-01',
                'before_or_equal:' . now()->format('Y-m-d'),
            ],
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();

        $year = $data['old_year'] ?? null;
        $month = $data['old_month'] ?? null;
        $day = $data['old_day'] ?? null;

        if ($year && $month && $day && checkdate((int)$month, (int)$day, (int)$year)) {
            $data['birth'] = sprintf('%04d-%02d-%02d', $year, $month, $day);
        } else {
            $data['birth'] = null;
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

            'role.required' => '役職は必須です。',
            'role.in' => '役職の値が不正です。正しい値を選択してください。',

            'password.required' => 'パスワードは必須です。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは30文字以内で入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',

            'old_year.required' => '年を選択してください。',
            'old_month.required' => '月を選択してください。',
            'old_day.required' => '日を選択してください。',

            'birth.required' => '生年月日が正しくありません。',
            'birth.date' => '正しい日付を入力してください（例: 2月31日や6月31日は無効です）。',
            'birth.after_or_equal' => '生年月日は2000年1月1日以降の日付を入力してください。',
            'birth.before_or_equal' => '生年月日は今日以前の日付を入力してください。',
        ];
    }
}
