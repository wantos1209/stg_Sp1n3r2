<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/asset-newyear/css/style.css" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900" />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11"> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Scratch L21</title>
    <style>
        /* @media (max-width: 767px) {
            #scratch {
                width: 100%;
                height: auto;
            }
        } */
    </style>

</head>

<body>
    <div class="container">
        <div class="img_ats img-container3">
            <img src="/asset-newyear/img/pohonnatal23-012.png" alt="" class="zoom-move-image">
        </div>

        <div class="img_ats img-container4">
            <img src="/asset-newyear/img/nataru2023headline-01-01.png" alt="">
        </div>
        <div class="scratch-wrap">
            <div class="chat-bubble">
                <div class="message_chat">
                    <b class="kode"></b>
                </div>
            </div>
            <div class="chat-bubble2">
                <div class="message_chat2">
                    <input type="text" class="message_chat2" name="username" id="username"
                        placeholder="Masukkan Username disini ..." {{-- value="{{ $data['username'] != '' ? $data['username'] : '' }}" --}} {{-- {{ $data['username'] != '' ? 'disabled' : '' }}  --}} />
                </div>
            </div>
            <div class="chat-bubble3">
                <div class="message_chat3">
                    {{-- <b>{{ $data['androidid_user'] }}</b> --}}
                    <b>Semoga Beruntung</b>

                </div>
            </div>
            {{-- @if ($data['isklaim'] != '1') --}}
            <canvas id="scratch" width="571" height="88"></canvas>
            <div class="centr">
                <div class="m-btm">
                    <button class="vertical-shake auto-scratch" id="btnal">Claim Disini</button>
                </div>
            </div>
            {{-- @endif --}}
            <div class="footer">
                <p>PROMO INI BERLAKU UNTUK MEMBER AKTIF LOTTO21<br> (MINIMAL AKTIF 3 BULAN TERAKHIR)</p>
            </div>
        </div>


        <div class="animated-image-container">
            <img src="/asset-newyear/img/salju23-01.png" alt="" class="animated-image">
        </div>
        <div class="animated-image-container2">
            <img src="/asset-newyear/img/salju23-01.png" alt="" class="animated-image2">
        </div>
        <div class="img_bwh">
            <img src="/asset-newyear/img/santanataru23-01.png" alt="">
        </div>
    </div>
    <audio autoplay loop>
        <source src="/asset-newyear/img/songNewYear.mp3" type="audio/mp3">
        Your browser does not support the audio tag.
    </audio>



    <script src="/asset-newyear/js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Set initial kode value
            $('.kode').text("{{ $data['kode'] }}");

            // Click event handler for the button
            $("#btnal").click(function() {
                submit();
            });

            // Keyup event handler for the username input
            $('#username').keyup(function(event) {
                if (event.keyCode === 13) { // Enter key
                    event.preventDefault(); // Prevent the default action of the enter key
                    submit();
                }
            });

            function submit() {
                const imageUrl = 'http://127.0.0.1:8000/asset-newyear/img/santa-grap.png';
                var website = "{{ $data['website'] }}";
                var androidid = "{{ $data['androidid'] }}";
                var jenis_event = "{{ $data['jenis_event'] }}";
                var username = $("#username").val().toUpperCase();
                var kode = '{{ $data['kode'] }}'.toUpperCase();
                var url = "/l21newyear/cek/" + jenis_event + "/" + website + "/" + androidid + "/" + username;

                if (validateUsername(username, kode, imageUrl)) {
                    return;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(response) {
                        handleResponse(response, imageUrl);
                    },
                    error: function(xhr, status, error) {
                        handleError(xhr, status, error, imageUrl);
                    },
                });
            }

            function validateUsername(username, kode, imageUrl) {
                if (username === kode) {
                    showError('Harap masukkan userid yang valid', imageUrl);
                    return true;
                }

                if (username === '') {
                    showError('Username tidak boleh kosong.', imageUrl);
                    return true;
                } else if (username.indexOf(" ") !== -1) {
                    showError('Username tidak boleh mengandung spasi.', imageUrl);
                    return true;
                } else if (username.length < 4) {
                    showError('Username harus minimal 4 karakter.', imageUrl);
                    return true;
                }

                return false;
            }

            function showError(message, imageUrl) {
                Swal.fire({
                    title: 'Error',
                    text: message,
                    icon: 'error',
                    heightAuto: false,
                    imageUrl: imageUrl,
                    imageAlt: 'Santa-l21',
                    customClass: {
                        confirmButton: 'custom-swal-button',
                        icon: 'custom-swal-icon',
                    },
                    confirmButtonText: 'OK'
                });
            }

            function handleResponse(response, imageUrl) {
                console.log(response);
                if (response.error) {
                    showError(response.error, imageUrl);
                } else {
                    Swal.fire({
                        title: 'Klaim Berhasil',
                        html: 'Selamat! Bonus anda dalam proses pengecekan dan akan langsung diproses ke akun anda jika userid anda memenuhi syarat.<br><br> Dan ada juga Doorprize yang akan diundi pada tanggal 16 Januari, jadi jangan sampai ketinggalan ya.',
                        icon: 'success',
                        heightAuto: false,
                        imageUrl: imageUrl,
                        imageAlt: 'Santa-l21',
                        customClass: {
                            confirmButton: 'custom-swal-button',
                            cancelButton: 'custom-swal-button',
                            icon: 'custom-swal-icon',
                        },
                        confirmButtonText: 'OK',
                        showCancelButton: true,
                        cancelButtonText: 'Live Chat',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                    }).then((result) => {
                        handleResult(result, response.data.androidid);
                    });
                }
            }

            function handleResult(result, androidid) {
                if (result.isConfirmed) {
                    window.location.href = androidid;
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    var livechat = "{{ $livechat }}";
                    window.open(livechat, '_blank');
                    window.location.href = androidid;
                } else {
                    window.location.href = androidid;
                }
            }

            function handleError(xhr, status, error, imageUrl) {
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);

                Swal.fire({
                    title: 'Error',
                    text: 'Silahkan hubungi Admin!',
                    icon: 'error',
                    heightAuto: false,
                    imageUrl: imageUrl,
                    imageAlt: 'Santa-l21',
                    customClass: {
                        confirmButton: 'custom-swal-button',
                        icon: 'custom-swal-icon',
                    },
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            var audio = $('audio')[0];

            $(document).click(function() {
                playAudio();
            });

            function playAudio() {
                if (audio.paused) {
                    audio.play();
                }
            }

            playAudio();
        });
    </script>
</body>

</html>
