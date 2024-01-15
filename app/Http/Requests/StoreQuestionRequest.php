<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'name' => ['string', 'max:255', 'required'],
            'a' => ['string', 'max:255', 'required'],
            'b' => ['string', 'max:255', 'required'],
            'c' => ['string', 'max:255', 'required'],
            'd' => ['string', 'max:255', 'required'],
            'correct_answer' => ['string', 'max:255', 'required'],
            'category_id' => ['integer', 'exists:categories,id', 'required'],
        ];
    }
}
