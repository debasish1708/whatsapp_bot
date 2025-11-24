<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\RestaurantCategory;
use App\Models\Role;
use App\Models\SchoolCategory;
use App\Models\User;
Use App\Models\Role as Roles;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Mail\EmailVerification;
use MailerSend\Exceptions\MailerSendException;

class BusinessRegisterController extends Controller
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
    protected $redirectTo = '/home';

    /**
     * Summary of showRegistrationForm
     * @return \Illuminate\Contracts\View\View
     */
    public function showRegistrationForm()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        $schoolCategory=SchoolCategory::all();
        $restaurant_categories = RestaurantCategory::all();
        $roles = Roles::where('slug','<>',UserRole::Admin->value)->get();
        return view(
            'content.business.registration',
            [
                'pageConfigs' => $pageConfigs,
                'schoolCategory'=>$schoolCategory,
                'roles'=>$roles,
                'restaurant_categories'=>$restaurant_categories
            ]
        );
    }

    /**
     * Summary of login
     * @return \Illuminate\Contracts\View\View
     */
    public function login(){
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
    }
    /**
     * Summary of register
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->create($request);

        return redirect()->route('business.login')->with('modal_success', 'Registration successful. Please Verify your email address');
    }

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
        $categoryId = request()->input('category');
        $roleName = Role::where('id', $categoryId)->value('name');
        return Validator::make($data, [
            'business_name' => ['required', 'string', 'max:255'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::exists('roles','id')],
            'school_categories' => [ Rule::requiredIf($roleName === 'School'),'array'],
            'school_categories.*' => [ Rule::requiredIf($roleName === 'School'), 'uuid', Rule::exists('school_categories', 'id')],
            'restaurant_categories' => [Rule::requiredIf($roleName === 'Restaurant'),'array'],
            'restaurant_categories.*' => [Rule::requiredIf($roleName === 'Restaurant'), 'uuid', Rule::exists('restaurant_categories', 'id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['nullable', 'in:accepted,rejected']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(Request $request)
    {
        try{
            $user = null;
            $data = $request->all();
            $data['email'] = strtolower($data['email']);
            DB::transaction (function () use ($data, &$user, $request) {

                $firebaseUser = User::withTrashed()
                                ->whereEmail($data['email'])->exists()
                         ? (new FirebaseService())->getUserByEmail($data['email'])
                         : (new FirebaseService())->createUser($data['email'], $data['password']);

                $user = User::create([
                    'firebase_user_id' => $firebaseUser->uid,
                    'role_id' => strtolower($data['category']),
                    'mobile_number' => null,
                    'name' => $data['business_name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);

                $user->signup()->create([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent() ?? null,
                    'is_opt_privacy_policy' => isset($data['terms']) && $data['terms'] === 'accepted'
                ]);

                $role = $user->role;
                if($role->slug == UserRole::School->value){
                    $school = $user->school()->create([
                        // 'name' => $data['business_name'],
                        'place_id' => $data['place_id'] ?? null,
                    ]);
                    $school->categories()->attach($data['school_categories']);
                }
                if($role->slug == UserRole::Restaurant->value){
                    $restaurant=$user->restaurant()->create([
                        // 'name' => $data['business_name'],
                        'place_id' => $data['place_id'] ?? null
                    ]);
                    $restaurant->categories()->attach($data['restaurant_categories']);
                }
            });

            Mail::to($user->email)->send(new EmailVerification($user));
            info('mail sent');
            return $user;
        } catch(\Kreait\Firebase\Exception\Auth\EmailExists $emailExists){

            info('Email Already Exist: ' . $emailExists->getMessage());
            throw \Illuminate\Validation\ValidationException::withMessages([
              'email' => 'This email already exists in Firebase.',
            ]);
        } catch(MailerSendException $e){
            info('MailerSendException Email Verification: ' . $e->getMessage());
            throw \Illuminate\Validation\ValidationException::withMessages([
              'general' => 'Email sending failed. Please try again later.',
            ]);
        } catch(\Exception $e){
          $firebaseUser = (new FirebaseService())->getUserByEmail($data['email']);
          if($firebaseUser){
            // If the user exists in Firebase, delete it
            (new FirebaseService())->deleteUser($firebaseUser->uid);
          }
          // Log the error message for debugging
            info('Error in registration: ' . $e->getMessage());
          throw \Illuminate\Validation\ValidationException::withMessages([
            'general' => 'An unexpected error occurred during registration.',
          ]);
        }
    }

}
