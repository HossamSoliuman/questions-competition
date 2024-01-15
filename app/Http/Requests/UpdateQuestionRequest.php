<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['string', 'max:255', 'nullable'],
            'a' => ['string', 'max:255', 'nullable'],
            'b' => ['string', 'max:255', 'nullable'],
            'c' => ['string', 'max:255', 'nullable'],
            'd' => ['string', 'max:255', 'nullable'],
            'correct_answer' => ['string', 'max:255', 'nullable'],
            'category_id' => ['integer', 'exists:categories,id', 'nullable'],
        ];
    }
}
