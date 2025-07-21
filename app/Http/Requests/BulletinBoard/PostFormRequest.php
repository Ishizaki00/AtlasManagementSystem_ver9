<?php

namespace App\Http\Requests\BulletinBoard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class PostFormRequest extends FormRequest
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
            'sub_category_id' => [
                'required',
                'integer',
                Rule::exists('sub_categories', 'id'),
            ],
            'post_title' => [
                'required',
                'string',
                'max:100',
            ],
            'post_body' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages()
    {
        return [
            'sub_category_id.required' => 'カテゴリーは必須項目です。',
            'sub_category_id.integer' => 'カテゴリーの形式が正しくありません。',
            'sub_category_id.exists' => '選択されたカテゴリーが無効です。',

            'post_title.required' => 'タイトルは必ず入力してください。',
            'post_title.string' => 'タイトルは文字列である必要があります。',
            'post_title.max' => 'タイトルは100文字以内で入力してください。',

            'post_body.required' => '内容は必ず入力してください。',
            'post_body.string' => '内容は文字列である必要があります。',
            'post_body.max' => '最大文字数は2000文字です。',
        ];
    }
}
