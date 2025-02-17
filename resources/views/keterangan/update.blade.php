@extends('layouts.index')

@section('container')
    <div class="sec_box hgi-100">
        <form action="/keterangan/update" method="POST" id="form">
            @csrf
            @foreach ($data as $index => $item)
                <div class="sec_form">
                    <div class="sec_head_form">
                        <h3>{{ $title }}</h3>
                        <span>Edit {{ $title }}</span>
                        <input type="hidden" name="id[]" value="{{ $item->id }}" {{ $disabled }}>
                    </div>

                    <div class="list_form">
                        <span class="sec_label">Keterangan</span>
                        <input type="text" id="keterangan" name="keterangan[]" placeholder="Masukkan Keterangan"
                            {{ $disabled }} value="{{ $item->keterangan }}" required>
                    </div>
                </div>
            @endforeach
            <div class="sec_button_form">
                <button class="sec_botton btn_submit" type="submit" id="Contactsubmit" {{ $disabled }}>Submit</button>
                <a href="/keterangan" id="cancel"><button type="button"
                        class="sec_botton btn_cancel">Cancel</button></a>
            </div>
        </form>
    </div>
@endsection
