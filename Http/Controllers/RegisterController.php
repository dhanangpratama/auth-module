<?php

namespace Modules\Auth\Http\Controllers;

use Modules\User\Entities\User;
use App\Models\Ktp;
use App\Models\Npwp;
use App\Models\Photo;
use App\Models\Signature;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Veripal, Arr, SEO, Str, DB, Log, App;
use App\Repositories\FailedUserVerificationRepository;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationStep1()
    {
        SEO::setTitle(__("Pendaftaran - Langkah 1"));
        return view('auth.register.step-1');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function postRegistrationStep1(Request $request)
    {
        $data = $request->only(
            'name',
            'sex',
            'dob',
            'birth_place',
            'ktp_no',
            'npwp_no',
            'mobile_phone'
        );

        $validator = Validator::make($data, [
            'name'          => 'required|string|max:255',
            'sex'           => 'required|boolean',
            'dob'           => 'required|date',
            'birth_place'   => 'required|string|max:255',
            'ktp_no'        => 'required|string|max:30',
            'npwp_no'       => 'required|string|max:30',
            'mobile_phone'  => 'required|string|max:15'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        session()->put('registration', $data);
        return redirect()->route('register-step-2');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationStep2()
    {
        if(!session()->has('registration')) {
            return redirect()->route('register-step-1');
        }

        SEO::setTitle(__("Pendaftaran - Langkah 2"));

        return view('auth.register.step-2');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function postRegistrationStep2(Request $request)
    {
        $data = $request->only(
            'state',
            'district',
            'sub_district',
            'address',
            'post_code'
        );
        
        $validator = Validator::make($data, [
            'state'         => 'required|exists:states,id',
            'district'      => 'required|exists:districts,district_number',
            'sub_district'  => 'required|exists:sub_districts,sub_district_number',
            'address'       => 'required|string|max:255',
            'post_code'     => 'required|string|max:7',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        session()->put('registration', session('registration') + $data);
        return redirect()->route('register-step-3');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationStep3()
    {
        if(!session()->has('registration')) {
            return redirect()->route('register-step-1');
        }

        SEO::setTitle(__("Pendaftaran - Langkah 3"));

        return view('auth.register.step-3');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function postRegistrationStep3(Request $request)
    {
        $data = $request->only(
            'email',
            'password',
            'password_confirmation'
        );
        
        $validator = Validator::make($data, [
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data['password'] = Hash::make($data['password']);
        session()->put('registration', session('registration') + $data);
        return redirect()->route('register-step-4');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationStep4()
    {
        if(!session()->has('registration')) {
            return redirect()->route('register-step-1');
        }

        SEO::setTitle(__("Pendaftaran - Langkah 4"));

        return view('auth.register.step-4');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function postRegistrationStep4(Request $request)
    {
        $data = $request->only(
            'ktp_image',
            'npwp_image',
            'photo',
            'signature',
            'agreement'
        );
        
        $validator = Validator::make($data, [
            'ktp_image'     => 'required|image',
            'npwp_image'    => 'required|image',
            'photo'         => 'required|string',
            'signature'     => 'required|string',
            'agreement'     => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        unset($data['agreement']);

        $verifyTryTimes =  config('veripal.verify_time_tries', 3);
        $tmp_path = "tmp" . DIRECTORY_SEPARATOR . randomString();
        $status = 0;

        $photo = explode("base64,", $data['photo']);
        $photo_base64 = $photo[1];

        // verify biometric
        $verifySelfie = Veripal::verifySelfie(['nik' => session('registration.ktp_no'), 'image' => $photo_base64]);

        if ( !Arr::get($verifySelfie, 'status') )
        {
            return redirect()->back()->withInput()->with('status', ['type' => 'danger', 'message' => __('Gagal melakukan verifikasi wajah.')]);
        }

        $selfieScore = Arr::get($verifySelfie, 'response.data.selfie_photo', 0);

        if ( $selfieScore < config('veripal.selfie_match_minimum_score') )
        {
            $failedUserVerification = App::make(FailedUserVerificationRepository::class);

            try
            {
                $failedUserVerification->create([
                    'email' => session('registration.email'),
                    'score' => Arr::get($verifySelfie, 'response.data.selfie_photo', 0),
                    'data' => json_encode(array_merge(session('registration'), ['photo' => (string) $photo_base64])),
                    'properties' => json_encode([
                        'ip_address' => request()->ip()
                    ]),
                    'created_at' => now()
                ]);
            }
            catch ( \Exception $e )
            {
                Log::error($e->getMessage());

                return redirect()->back()->withInput()->with('status', ['type' => 'danger', 'message' => __("Terjadi kesalahan pada server. Mohon untuk mencoba beberapa saat lagi")]);
            }

            $verifyErrorTries = $failedUserVerification->findByEmail(session('registration.email'))->count();

            if ( $verifyErrorTries < $verifyTryTimes )
            {
                return redirect()->back()->withInput()->with('status', ['type' => 'danger', 'message' => __('Foto wajah tidak cocok! Sisa mencoba verifikasi ' .( $verifyTryTimes - $verifyErrorTries) . 'x')]); 
            }
        }
        else
        {
            $status = 1;
        }

        DB::beginTransaction();
        try {
            $photo_filename = Str::uuid() . ".png";
            Storage::put($tmp_path . DIRECTORY_SEPARATOR . "photo" . DIRECTORY_SEPARATOR . $photo_filename, base64_decode($photo_base64));

            if ($request->hasFile('ktp_image')) {
                $ktp_ext = $request->ktp_image->getClientOriginalExtension();
                $ktp_filename = Str::uuid() . ".{$ktp_ext}";
                $ktp_path = $request->ktp_image->storeAs($tmp_path . DIRECTORY_SEPARATOR . "ktp", $ktp_filename);
            }

            if ($request->hasFile('npwp_image')) {
                $npwp_ext = $request->npwp_image->getClientOriginalExtension();
                $npwp_filename = Str::uuid() . ".{$npwp_ext}";
                $npwp_path = $request->npwp_image->storeAs($tmp_path . DIRECTORY_SEPARATOR . "npwp", $npwp_filename);
            }

            $signature = explode("data:image/svg+xml;base64,", $data['signature']);
            $signature_base64 = $signature[1];
            $signature_json = $signature[0];
            $signature_filename = Str::uuid() . ".svg";
            Storage::put($tmp_path . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . $signature_filename, base64_decode($signature_base64));

            event(new Registered($user = User::create(session('registration') + ['status' => $status])->assignRole('user')));
            session()->forget('registration');

            $relative_base_path = "user" . DIRECTORY_SEPARATOR . $user->id;
            if (Storage::move($tmp_path, $relative_base_path)) {
                Ktp::create([
                    'user_id'               => $user->id,
                    'relative_base_path'    => $relative_base_path . DIRECTORY_SEPARATOR . "ktp",
                    'ktp_image_path'        => public_storage_path($relative_base_path . DIRECTORY_SEPARATOR . "ktp" . DIRECTORY_SEPARATOR . $ktp_filename),
                    'ktp_image'             => $ktp_filename
                ]);

                Npwp::create([
                    'user_id'               => $user->id,
                    'relative_base_path'    => $relative_base_path . DIRECTORY_SEPARATOR . "npwp",
                    'npwp_image_path'       => public_storage_path($relative_base_path . DIRECTORY_SEPARATOR . "npwp" . DIRECTORY_SEPARATOR . $npwp_filename),
                    'npwp_image'            => $npwp_filename
                ]);

                Photo::create([
                    'user_id'               => $user->id,
                    'relative_base_path'    => $relative_base_path . DIRECTORY_SEPARATOR . "photo",
                    'photo_image_path'      => public_storage_path($relative_base_path . DIRECTORY_SEPARATOR . "photo" . DIRECTORY_SEPARATOR . $photo_filename),
                    'photo_image'           => $photo_filename,
                    'verify_result'         => $selfieScore
                ]);

                Signature::create([
                    'user_id'               => $user->id,
                    'relative_base_path'    => $relative_base_path . DIRECTORY_SEPARATOR . "signature",
                    'signature_image_path'  => public_storage_path($relative_base_path . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . $signature_filename),
                    'signature_image'       => $signature_filename
                ]);

                activity_log('registrasi pengguna', 'Auth\RegisterController::postRegistrationStep4', $user, $user->id);

                DB::commit();
            }
            else
            {
                throw new \Exception("Failed move {$tmp_path} to {$relative_base_path}", 1);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }

        /**
         * disable auto login after registration
         */
        //$this->guard()->login($user);

        if ( $status === 0 )
            session()->flash('status', ['type' => 'danger', 'message' => __('Verifikasi foto gagal dilakukan. Akun anda belum aktif. Silahkan menghubungi pihak terkait.')]);
        else
            session()->flash('status', ['type' => 'success', 'message' => __('Pendaftaran berhasil. Silahkan log in.')]);

        return $this->registered($request, $user)?: redirect($this->redirectPath());
    }
}
