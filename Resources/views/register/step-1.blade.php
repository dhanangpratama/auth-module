@extends('layouts.app')

@section('content')
<div class="container register-step-1">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __("Pendaftaran") }} 1/4</div>

                <div class="card-body">
                    {!! Form::open(['method' => 'post', 'url' => route('register-step-1'), 'autocomplete' => 'off']) !!}
                        <div class="form-group row">
                            {{ Form::label('name', 'Nama', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('name', old('name'), ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('sex', 'Jenis Kelamin', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::select('sex', ['' => '', '0' => 'Perempuan', '1' => 'Laki-Laki'], old('sex'), ['class' => 'form-control' . ($errors->has('sex') ? ' is-invalid' : ''), 'required']); !!}

                                @if ($errors->has('sex'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sex') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('dob', 'Tanggal Lahir', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::date('dob', old('dob'), ['class' => 'form-control' . ($errors->has('dob') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('dob'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('birth_place', 'Tempat Lahir', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('birth_place', old('birth_place'), ['class' => 'form-control' . ($errors->has('birth_place') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('birth_place'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('birth_place') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('ktp_no', 'No. KTP', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('ktp_no', old('ktp_no'), ['class' => 'form-control' . ($errors->has('ktp_no') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('ktp_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('ktp_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('npwp_no', 'NPWP', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('npwp_no', old('npwp_no'), ['class' => 'form-control' . ($errors->has('npwp_no') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('npwp_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('npwp_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('mobile_phone', 'No. HP', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('mobile_phone', old('mobile_phone'), ['class' => 'form-control' . ($errors->has('mobile_phone') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('mobile_phone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile_phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Selanjutnya</button>
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
            $('select[name="sex"]').select2({
                placeholder: "Pilih jenis kelamin",
                minimumResultsForSearch: -1
            });
        }
    </script>
@endpush