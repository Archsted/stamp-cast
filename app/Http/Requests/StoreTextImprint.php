<?php

namespace App\Http\Requests;

use App\Room;
use Illuminate\Foundation\Http\FormRequest;

class StoreTextImprint extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = request()->user();

        $room = $this->route('room');

        // @todo beta
        if ($room->id !== 1) {
            return false;
        }

        // ルームの持ち主のブラックリストに登録されていたらNG
        if ($room->user->blackListIps()->where('ip', request()->ip())->count() > 0) {
            return false;
        }

        if ($room->imprinter_level === Room::IMPRINTER_LEVEL_USER_ONLY) {
            if (is_null($user)) {
                return false;
            }
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
