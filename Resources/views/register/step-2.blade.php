@extends('layouts.app')

@section('content')
<div class="container register-step-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __("Pendaftaran") }} 2/4</div>

                <div class="card-body">
                    {!! Form::open(['method' => 'post', 'url' => route('register-step-2'), 'autocomplete' => 'off']) !!}
                        <div class="form-group row">
                            {{ Form::label('state', 'Provinsi', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::select('state', ['' => ''], old('state'), ['class' => 'form-control' . ($errors->has('state') ? ' is-invalid' : ''), 'required']); !!}

                                @if ($errors->has('state'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('district', 'Kota/Kabupaten', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::select('district', ['' => ''], old('kabupaten'), ['class' => 'form-control' . ($errors->has('kabupaten') ? ' is-invalid' : ''), 'required']); !!}

                                @if ($errors->has('kabupaten'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('kabupaten') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('sub_district', 'Kecamatan', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::select('sub_district', ['' => ''], old('sub_district'), ['class' => 'form-control' . ($errors->has('sub_district') ? ' is-invalid' : ''), 'required']); !!}

                                @if ($errors->has('sub_district'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('sub_district') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('address', 'Alamat', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('address', old('address'), ['class' => 'form-control' . ($errors->has('address') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            {{ Form::label('post_code', 'Kode Pos', ['class' => 'col-md-4 col-form-label text-md-right']) }}

                            <div class="col-md-6">
                                {!! Form::text('post_code', old('post_code'), ['class' => 'form-control' . ($errors->has('post_code') ? ' is-invalid' : ''), 'required']) !!}

                                @if ($errors->has('post_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('post_code') }}</strong>
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
        (function() {
            $.getJSON('{{ route('api.wilayah.getStates') }}', function(results) {
                $.each(results, function(key, result) {
                    $('<option>', {
                        value: result.id,
                        text: result.label
                    }).appendTo('select[name="state"]');
                });
            });
        })();

        $('select[name="state"]').select2({
            placeholder: "Pilih provinsi"
        });

        $('select[name="state"]').change(function() {
            var district_number = $(this).val();
            $.getJSON('{{ route('api.wilayah.getDistricts') }}/' + district_number, function(results) {
                $('select[name="district"]').empty();
                $('select[name="sub_district"]').empty();

                $('<option>', {
                    value: '',
                    text: ''
                }).appendTo('select[name="district"]');

                $.each(results, function(key, result) {
                    $('<option>', {
                        value: result.district_number,
                        text: result.label
                    }).appendTo('select[name="district"]');
                });
            });
        });

        $('select[name="district"]').select2({
            placeholder: "Pilih kota"
        });

        $('select[name="district"]').change(function() {
            var state_id = $('select[name="state"]').val();
            var district_number = $(this).val();
            $.getJSON('{{ route('api.wilayah.getSubDistricts') }}/' + state_id + '/' + district_number, function(results) {
                $('select[name="sub_district"]').empty();

                $('<option>', {
                    value: '',
                    text: ''
                }).appendTo('select[name="sub_district"]');

                $.each(results, function(key, result) {
                    $('<option>', {
                        value: result.sub_district_number,
                        text: result.label
                    }).appendTo('select[name="sub_district"]');
                });
            });
        });

        $('select[name="sub_district"]').select2({
            placeholder: "Pilih kecamatan"
        });
    }
</script>
@endpush