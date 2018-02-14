<?php

namespace App\Http\Requests;

use App\Room;
use Illuminate\Foundation\Http\FormRequest;

class StoreImprintGuest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var Room $room */
        $room = $this->route('room');

        // ルームの持ち主のブラックリストに登録されていたらNG
        if ($room->user->blackListIps()->where('ip', request()->ip())->count() > 0) {
            return false;
        }

        // スタンプ投稿がユーザーのみだった場合は不許可
        if ($room->imprinter_level === Room::IMPRINTER_LEVEL_USER_ONLY) {
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
            //
        ];
    }
}
