<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookStampOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userId = $this->user()->id;

        $book = $this->route('book');

        if ($book->user_id !== $userId) {
            return false;
        }

        $stampId = $this->route('stampId');

        if ($book->stamps()->where('stamps.id', $stampId)->count() === 0) {
            return false;
        }

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
            'order' => 'required|integer|min:1',
        ];
    }
}
