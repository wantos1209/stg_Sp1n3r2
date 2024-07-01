<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LinkSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apiUrl = 'https://l4soyk0.com/api/datawebsite/';

        $bearerToken = 'youk1llmyfvcking3x';

        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer $bearerToken"
            ]
        ]);

        $response = file_get_contents($apiUrl, false, $context);

        if ($response !== false) {
            $data = json_decode($response, true);
            $data = $data["data"];
            return view('linksettings.index', [
                'data' => $data,
                'title' => 'Link Settings'
            ]);
        } else {
            return response()->json(['error' => 'API request failed'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('linksettings.create', [
            'title' => 'Jenis Voucher'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username_shorten' => 'required',
            'link_awal' => 'required',
            'link_hasil' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        } else {
            try {
                LinkSettings::create($request->all());
                return response()->json([
                    'message' => 'Data berhasil disimpan.',
                ]);
            } catch (\Exception $e) {
                return response()->json(['errors' => ['Terjadi kesalahan saat menyimpan data.']]);
            }
        }

        return response()->json([
            'message' => 'Data berhasil disimpan.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LinkSettings $LinkSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $var1 = str_replace("&", " ", $id);
        $var2 = explode("values[]=", $var1);
        $var3 = array_slice($var2, 1);
        $var4 = str_replace(" ", "", $var3);

        if (!empty($var4)) {
            $id = $var4;
            foreach ($id as $index => $ids) {
                $linksettings[$index] = $this->fetchData($ids);
            }
        } else {
            $linksettings = [$this->fetchData($id)];
        }

        return view('linksettings.update', [
            'title' => 'Jenis Voucher',
            'data' => $linksettings,
            'disabled' => ''
        ]);
    }

    function fetchData($id)
    {
        $apiUrl = 'https://l4soyk0.com/api/datawebsite/' . $id;

        $bearerToken = 'youk1llmyfvcking3x';

        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer $bearerToken"
            ]
        ]);

        $response = file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            return $response;
        } else {
            // $response = $response["data"];
            $response =  json_decode($response, true);
            $response = $response["data"];
            return $response;
        }
    }

    public function views($id)
    {
        $var1 = str_replace("&", " ", $id);
        $var2 = explode("values[]=", $var1);
        $var3 = array_slice($var2, 1);
        $var4 = str_replace(" ", "", $var3);

        if (!empty($var4)) {
            $id = $var4;
            foreach ($id as $index => $ids) {
                $linksettings[$index] = $this->fetchData($ids);
            }
        } else {
            $linksettings = [$this->fetchData($id)];
        }

        return view('linksettings.update', [
            'title' => 'Jenis Voucher',
            'data' => $linksettings,
            'disabled' => 'disabled'
        ]);
    }


    public function data($id)
    {
        $data = LinkSettings::find($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $ids = $request->id;
        $linkalternatif1 = $request->linkalternatif1;
        $livechat = $request->livechat;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer youk1llmyfvcking3x'
        );

        $successCount = 0;

        foreach ($ids as $index => $id) {
            $data = [
                'linkalternatif1' => $linkalternatif1[$index],
                'livechat' => $livechat[$index]
            ];

            $url = 'https://l4soyk0.com/api/datawebsite/' . $id;

            $data_string = json_encode($data);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);

            if (curl_error($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                $successCount++;
            }

            curl_close($ch);
        }

        if ($successCount > 0) {
            return response()->json(['success' => $successCount . ' item berhasil diupdate!']);
        } else {
            return response()->json(['error' => 'Tidak ada item yang berhasil diupdate.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('values');

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $id) {
            $LinkSettings = LinkSettings::findOrFail($id);
            $LinkSettings->delete();
        }

        return response()->json(['success' => 'Data berhasil dihapus!']);
    }
}
