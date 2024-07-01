<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ButtonSetting;
use App\Models\Event;
use App\Models\EventTemp;
use App\Models\Hadiah;
use App\Models\ListPrize;
use App\Models\Website;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewYearController extends Controller
{

    public function l21newyear($jenis_event, $website, $deviceid, $ip = "")
    {
        $dataWebsite = Website::where('nama', $website)->first();
        $linkButton = ButtonSetting::first();

        $livechat = $dataWebsite->livechat;
        $whatsapp = $dataWebsite->livechat;
        $button_title = $linkButton->title;
        $button_url = $linkButton->url;
        // $livechat = '';
        // $button_title = '';
        // $button_url = '';

        $data = $this->createEvent($jenis_event, $website, $deviceid, $ip);

        $alldata = Event::where('jenis_event', $jenis_event)->where('isklaim', 1)->where('status', 1)->orderBy('created_at', 'DESC')->get()->toArray();

        $data = $data->toArray();
        if ($data['prize_id'] == '1') {
            $gambar = "nmax-l21.png";
        } else if ($data['prize_id'] == '2') {
            $gambar = "vario-l21.png";
        } else if ($data['prize_id'] == '3') {
            $gambar = "laptop-l21.png";
        } else if ($data['prize_id'] == '4') {
            $gambar = "hp-l21.png";
        } else if ($data['prize_id'] == '5') {
            $gambar = "tv-l21.png";
        } else if ($data['prize_id'] == '6') {
            $gambar = "voucher-l21.png";
        }

        if (isset($gambar)) {
            $pathgambar = '/asset-newyear/img/pemenang/' . $gambar;
        } else {
            $pathgambar = '';
        }

        if ($data) {
            if ($data["isklaim"] == 1) {
                return view('frontnewyear.indexhalaman', [
                    'title' => 'Hadiah',
                    'alldata' => $alldata,
                    'data' => $data,
                    'datenow' => date('Y-m-d'),
                    'livechat' => $livechat,
                    'whatsapp' => $whatsapp,
                    'button_title' => $button_title,
                    'button_url' => $button_url,
                    'pathgambar' => $pathgambar,
                    'jenis_event' => $jenis_event
                ]);
            } else {
                return view('frontnewyear.index', [
                    'title' => 'Hadiah',
                    'data' => $data,
                    'livechat' => $livechat,
                    'whatsapp' => $whatsapp,
                    'button_title' => $button_title,
                    'button_url' => $button_url,
                    'pathgambar' => '',
                    'jenis_event' => $jenis_event
                ]);
            }
        } else {
            return view('frontnewyear.index', [
                'title' => 'Hadiah',
                'data' => $data,
                'livechat' => $livechat,
                'whatsapp' => $whatsapp,
                'button_title' => $button_title,
                'button_url' => $button_url,
                'pathgambar' => $pathgambar,
                'jenis_event' => $jenis_event
            ]);
        }
    }

    public function l21newyear_update($jenis_event, $website, $deviceid, $username)
    {
        $username = strtolower($username);
        $result = $this->putData($jenis_event, $website, $deviceid, $username);

        if (!$result) {
            return $result;
        } else {
            $data = $this->updateklaim($jenis_event, $website, $deviceid);
            return $data;
        }
    }

    function putData($jenis_event, $website, $deviceid, $username)
    {
        $updateData = Event::where('jenis_event', $jenis_event)->where('website', $website)->where('androidid', $deviceid)->first();

        if ($updateData) {
            $updateData->update([
                'username' => $username
            ]);
            $updateDataTemp = EventTemp::where('jenis_event', $jenis_event)->where('website', $website)->where('androidid', $deviceid)->first();
            $updateDataTemp->update([
                'username' => $username
            ]);
        }
        return $updateData;
    }


    private function createEvent($jenis_event, $website, $androidid, $ip = "")
    {
        $data = Event::where('jenis_event', $jenis_event)->where('website', $website)->where('androidid', $androidid)->first();

        if ($data) {
            $data->androidid_user = "guest_" . substr($data->androidid, -5);
            $data->website = $website;
            $data->prize =  $data->prize_id != 0 ? ListPrize::where('id', $data->prize_id)->first()->nama : '0';

            return $data;
        } else {

            $kode = $this->generateKey($jenis_event, $website, 6);
            $totalBudget = Budget::where('jenis_event', $jenis_event)->first()->budget;
            $hadiah = Hadiah::get();
            $hadiahRandom = $this->getRewardByPercentage($hadiah);

            $totalPengeluaran = Event::where('jenis_event', $jenis_event)->sum('hadiah');

            if ($totalPengeluaran > $totalBudget) {
                return response()->json(['message' => 'Jumlah voucher sudah limit!', 'data' => null]);
            }

            $allrequest = [
                'jenis_event' => $jenis_event,
                'website' => $website,
                'androidid' => $androidid,
                'kode' => $kode,
                'ip' => $ip,
                'hadiah' => $hadiahRandom,
                'status' => '0',
                'prize_id' => '0',
                'isklaim' => '1'
            ];

            $data = Event::create($allrequest);
            if ($data) {
                // Create the EventTemp record with the same ID
                $dataTemp = new EventTemp($allrequest);
                $dataTemp->id = $data->id; // Set the same ID as Event
                $dataTemp->save();
            }

            $data->androidid_user = "guest_" . substr($data->androidid, -5);
            $data->website = $website;
            $data->prize = $data->prize_id != 0 ? ListPrize::where('id', $data->prize_id)->first()->nama : '0';
            $data->isklaim = 0;

            return $data;
        }
    }


    private function generateKey($jenis_event, $website, $length)
    {
        $characters = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $result = '';
            $maxIndex = strlen($characters) - 1;

            for ($i = 0; $i < $length; $i++) {
                $randomIndex = mt_rand(0, $maxIndex);
                $result .= $characters[$randomIndex];
            }

            $checkkode = Event::where('jenis_event', $jenis_event)->where('website', $website)->where('kode', $result)->first();
        } while (!is_null($checkkode));

        return $result;
    }

    function getRewardByPercentage($rewards)
    {
        $rewards = $rewards->toArray();

        // Total persentase dari semua hadiah
        $totalPercentage = array_sum(array_column($rewards, 'persentase'));

        // Menghasilkan angka acak antara 0 dan total persentase
        $randomNumber = mt_rand(0, $totalPercentage);

        // Inisialisasi variabel untuk menyimpan hadiah yang dipilih
        $selectedReward = null;

        // Iterasi melalui setiap hadiah
        foreach ($rewards as $reward) {
            // Mengurangkan persentase hadiah saat ini dari angka acak
            $randomNumber -= $reward['persentase'];

            // Jika angka acak kurang dari atau sama dengan 0, maka hadiah ini dipilih
            if ($randomNumber <= 0) {
                $selectedReward = $reward;
                break;
            }
        }

        return $selectedReward['hadiah'];
    }

    private function updateklaim($jenis_event, $website, $androidid)
    {
        try {
            $event = Event::where('jenis_event', $jenis_event)->where('website', $website)->where('androidid', $androidid)->first();

            if ($event) {
                $event->update([
                    'isklaim' => 1,
                    'vote' => 1
                ]);

                return response()->json(['message' => 'Event updated successfully', 'data' => $event]);
            } else {
                return response()->json(['error' => 'Gagal klaim'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
