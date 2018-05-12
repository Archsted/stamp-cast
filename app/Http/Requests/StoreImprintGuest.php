<?php

namespace App\Http\Requests;

use App\Room;
use App\Stamp;
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

        // ルームのアップロード許可レベルが全員不可の場合
        if ($room->uploader_level === Room::UPLOADER_LEVEL_NOBODY) {
            // まず送信しようとしているスタンプの情報を取得する
            $stamp = Stamp::findOrFail($this->request->get('stamp_id'));

            // ルームIDと、スタンプが登録されたルームIDを比較する。
            // 異なる場合、スタンプ帳経由で他ルームのスタンプが送信されたことになるが、
            // 意図しない内容のスタンプが表示されるのを防ぐため、エラーにする。
            // スタンプのルームIDがnullのものは共有スタンプのため無視
            if (!is_null($stamp->room_id) && $stamp->room_id !== $room->id) {
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
