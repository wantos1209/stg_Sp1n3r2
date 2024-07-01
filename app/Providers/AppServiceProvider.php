<?php

namespace App\Providers;

use App\Models\EventTemp;
use App\Models\Voucher;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $totalSpin = Voucher::join('spinner_generatevoucher', 'spinner_voucher.genvoucherid', '=', 'spinner_generatevoucher.id')
            ->where(function ($query) {
                $query->where('spinner_voucher.status_transfer', '=', null)
                    ->orWhere('spinner_voucher.status_transfer', '=', 0);
            })
            ->where('spinner_voucher.balance_kredit', '=', 0)
            ->where('spinner_generatevoucher.isdemo', 0)
            ->count();

        $totalPemilu = EventTemp::where('jenis_event', 0)->where('isklaim', 1)->where('status', 0)->where('vote',  0)->count();

        $totalAppImlek = EventTemp::where('jenis_event', 1)->where('isklaim', 0)->where('status', 0)->count();
        $totalProImlek = EventTemp::where('jenis_event', 1)->where('isklaim', 1)->where('status', 0)->where('vote', '!=', 0)->count();

        $totalNewYear = EventTemp::where('jenis_event', 2)->where('isklaim', 1)->where('status', 0)->count();

        View::share('totalnote', 0);
        View::share('totalPemilu', $totalPemilu);
        View::share('totalSpin', $totalSpin);
        View::share('totalAppImlek', $totalAppImlek);
        View::share('totalProImlek', $totalProImlek);
        View::share('totalNewYear', $totalNewYear);
    }
}
