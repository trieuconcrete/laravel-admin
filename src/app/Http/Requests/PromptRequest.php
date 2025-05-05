<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'example_output' => 'nullable|string',
            'variable_descriptions' => 'nullable|json',
            'is_public' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ];

        if ($this->isMethod('post')) {
            $rules['slug'] = 'nullable|string|max:255|unique:prompts,slug';
        } else {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('prompts')->ignore($this->route('prompt'))
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục',
            'title.required' => 'Tiêu đề là bắt buộc',
            'content.required' => 'Nội dung prompt là bắt buộc',
            'slug.unique' => 'Slug đã tồn tại',
            'variable_descriptions.json' => 'Mô tả biến phải là JSON hợp lệ'
        ];
    }
}