<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ToolController extends Controller
{
    public function index()
    {
        return view('broadcaster.tools');
    }

    public function download($platform)
    {
        // ツールのパス
        $toolPath = '';

        switch ($platform) {
            case 'win-ia32':
                $toolPath = resource_path('tools/win-ia32/StampCast_1.0.0.zip');
                break;
            case 'win-x64':
                $toolPath = resource_path('tools/win-x64/StampCast_1.0.0.zip');
                break;
            default:
                abort(404);
        }

        if ( File::exists($toolPath) === false ) {
            abort(404);
        }

        return response()->download($toolPath);
    }

    public function toolIndex()
    {
        return view('tools.entrance');
    }

    public function receiver(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN');
        $url = $request->get('url');

        $pattern = '^' . $origin . '/([\d]+)$';

        if (preg_match('#'. $pattern .'#', $url, $matches)) {
            $roomId = $matches[1];

            $room = Room::find($roomId);

            if (is_null($room) === false) {
                return redirect($url . '/broadcaster');
            }
        }

        return redirect()->route('tool_login')->with('message', '不正なURLが指定されています。');
    }
}
