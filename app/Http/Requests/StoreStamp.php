<?php

namespace App\Http\Requests;

use App\Room;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreStamp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $room = $this->route('room');

        if ($room->uploader_level === Room::UPLOADER_LEVEL_NOBODY) {
            return false;
        }

        if ($room->uploader_level === Room::UPLOADER_LEVEL_USER_ONLY) {
            if ($this->request->has('user_id') === false) {
                return false;
            }

            $user = User::find($this->request->get('user_id'));

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
            'file' => 'required|image'
        ];
    }
}
