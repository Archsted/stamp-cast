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
        /** @var Room $room */
        $room = $this->route('room');

        if ($room->uploader_level === Room::UPLOADER_LEVEL_NOBODY) {
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
            'file' => 'required|image'
        ];
    }
}
