<?php

namespace App\Http\Controllers;

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
}
