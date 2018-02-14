<?php

namespace App\Http\Requests;

use App\Room;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreImprint extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $room = $this->route('room');

        if ($room->imprinter_level === Room::IMPRINTER_LEVEL_USER_ONLY) {

            $user = request()->user();

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
        ];
    }
}
