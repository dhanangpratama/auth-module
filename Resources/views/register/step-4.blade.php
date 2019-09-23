@extends('layouts.app')

@section('content')
<div class="container register-step-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __("Pendaftaran") }} 4/4</div>

                <div class="card-body">
                    {!! Form::open(['method' => 'post', 'url' => route('register-step-4'), 'autocomplete' => 'off', 'files' => true]) !!}

                        <div class="form-group row">
                            {{ Form::label('ktp_image', 'Foto eKTP', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::file('ktp_image', ['class' => 'form-control' . ($errors->has('ktp_image') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('ktp_image'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('ktp_image') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('npwp_image', 'Foto NPWP', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::file('npwp_image', ['class' => 'form-control' . ($errors->has('npwp_image') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('npwp_image'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('npwp_image') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('photo', 'Foto Wajah', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-8">
                                <video width="640" height="480" id="webcam" autoplay></video>
                                <canvas width="640" height="480" id="face" style="display: none"></canvas>
                                <br>
                                <button type="button" class="btn btn-info" id="capture">Ambil Foto Wajah</button>
                                {{ Form::hidden('photo') }}

                                @if ($errors->has('photo'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('photo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('signature', 'Tanda Tangan', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-8">
                                <canvas style="width:400px; height:200px; border:1px solid rgba(0,0,0,.125);" id="signature-canvas"></canvas>
                                <br>
                                <button type="button" class="btn btn-info" id="save-signature">Simpan</button>
                                <button type="button" class="btn btn-info" id="clear-signature">Hapus</button>
                                {{ Form::hidden('signature') }}

                                @if ($errors->has('signature'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('signature') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('agreement', 'Persetujuan', ['class' => 'col-md-4 text-md-right']) }}

                            <div class="col-md-8 form-check">
                                <input type="checkbox" name="agreement" value="1" class="form-check-input" required> Saya telah membaca dan ...

                                @if ($errors->has('agreement'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('agreement') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    window.onload = function () {
        var webcam = document.getElementById('webcam');
        var face = document.getElementById('face');
        var mediaStream = null;
        var i = 0;

        // Get access to the camera!
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Not adding `{ audio: true }` since we only want video now
            navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                //video.src = window.URL.createObjectURL(stream);
                mediaStream = stream;
                webcam.srcObject = stream;
                webcam.play();
            })
            .catch(function(error) {
                console.log("Something went wrong!");
            });
        } else {
            alert("Unable to access webcam");
        }

        $('#capture').click(function(e) {
            face.getContext('2d').drawImage(webcam, 0, 0, 640, 480);
            webcam.style.display = "none";
            $(this).hide();
            document.getElementById('photo').value = face.toDataURL();
            face.style.display = "inline";

            mediaStream.getVideoTracks().forEach(function (track) {
                track.stop();
            });
        });

        var sigCanvas = document.getElementById("signature-canvas");
        var signaturePad = new SignaturePad.default(sigCanvas);
        
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            sigCanvas.width = sigCanvas.offsetWidth * ratio;
            sigCanvas.height = sigCanvas.offsetHeight * ratio;
            sigCanvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }
        
        window.onresize = resizeCanvas;
        resizeCanvas();

        $('#clear-signature').click(function(e) {
            signaturePad.clear();
            $('#signature').val('');
        });

        $('#save-signature').click(function(e) {
            if (signaturePad.isEmpty()) {
                alert("Silahkan bubuhkan tanda tangan terlebih dahulu.");
            } else {
                $('#signature').val(JSON.stringify(signaturePad.toData()) + signaturePad.toDataURL('image/svg+xml'));
                $('#clear-signature').hide();
                $('#save-signature').hide();
            }
        });
    }
</script>
@endpush