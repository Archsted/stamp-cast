<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBookStamps extends FormRequest
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

        // Bookの持ち主ではなかったらNG
        if ($book->user_id !== $userId) {
            return false;
        }

        $stampIds = $this->request->get('stampIds');

        // IDに重複があったらNG
        if (count($stampIds) !== count(array_unique($stampIds))) {
            return false;
        }

        // Bookに未登録のIDがあったらNG
        // 重複無しが保証されたID配列で件数を取り、ID配列の件数と一致するかで判定
        if (count($stampIds) !== $book->stamps()->whereIn('stamps.id', $stampIds)->count()) {
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
            'stampIds' => 'required|array',
            'stampIds.*' => 'integer',
        ];
    }
}
