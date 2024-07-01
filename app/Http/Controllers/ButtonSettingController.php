<?php

namespace App\Http\Controllers;

use App\Models\ButtonSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ButtonSettingController extends Controller
{

    public function index()
    {
        $buttonsettings = ButtonSetting::get()->toArray();
        return view('buttonsetting.index', [
            'title' => 'List Button Setting',
            'data' => $buttonsettings
        ]);
    }

    public function create()
    {
        return view('buttonsetting.create', [
            'title' => 'Create Button Setting'
        ]);
    }

    public function view($id)
    {
        try {
            if (!is_array($id)) {
                $ids = [$id];
            }

            $response = ButtonSetting::whereIn('id', $ids)->get()->toArray();

            return view('buttonsetting.view', [
                'title' => 'View Button Setting',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }


    public function edit($id)
    {
        try {
            $pattern = '/values\[\]=\s*(\d+)/';
            preg_match_all($pattern, $id, $matches);
            $ids = $matches[1];

            if (empty($ids)) {
                $ids = [$id];
            }

            $response = ButtonSetting::whereIn('id', $ids)->get()->toArray();
            return view('buttonsetting.update', [
                'title' => 'View Button Setting',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function update(Request $request)
    {
        try {

            $ids = $request->id;
            $title = $request->title;
            $url = $request->url;

            foreach ($ids as $i => $id) {
                ButtonSetting::where('id', $id)->update([
                    'title' => $title[$i],
                    'url' => $url[$i]
                ]);
            }

            return redirect('/buttonsetting/index')->with('success', 'Data berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request)
    {

        try {
            $ids = $request->values;
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            ButtonSetting::whereIn('id', $ids)->delete();

            return 'Success';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
