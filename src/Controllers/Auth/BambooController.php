<?php

namespace Brazzer\Admin\Controllers\Auth;

use Brazzer\Admin\Auth\Database\Administrator;
use Brazzer\Admin\Auth\Database\Role;
use Brazzer\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Socialite;

class BrazzerController extends Controller{
    protected function redirectPath(){
        if(method_exists($this, 'redirectTo')){
            return $this->redirectTo();
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
    }

    public function login(){
        if($this->guard()->check()){
            return redirect($this->redirectPath());
        }
        return Socialite::driver('brazzer')->redirect();
    }

    public function loginCallback(Request $request){
        if(Admin::user()){
            return redirect()->intended($this->redirectPath());
        }else{
            try{
                $user = Socialite::driver('brazzer')->user();
                $authUser = $this->findOrCreateUser($user);
                if($this->guard()->login($authUser, true)){
                    return $this->sendLoginResponse($request);
                }
            }catch(\Exception $e){
                \Log::error($e);
                return redirect(route('admin.login'))->withErrors([
                    'error' => 'Đăng nhập với Brazzer Airways Application bị lỗi không tìm thấy tài khoản!.'
                ]);
            }
        }
        return redirect(route('admin.login'))->withErrors([
            'error' => 'Đăng nhập với Brazzer Airways Application bị lỗi!.'
        ]);
    }

    protected function guard(){
        return Auth::guard();
    }

    protected function sendLoginResponse(Request $request){
        admin_toastr(trans('admin.login_successful'));
        $request->session()->regenerate();
        return redirect()->intended($this->redirectPath());
    }

    public function logout(){
        $this->guard()->logout();
        return redirect(OAuth2::getLogoutUrl($this->redirectPath()));
    }

    private function findOrCreateUser($brazzerUser){
        $token    = [
            'access_token'  => $brazzerUser->token,
            'refresh_token' => $brazzerUser->refreshToken,
            'expires_in'    => $brazzerUser->expiresIn
        ];
        /*$authUser = Administrator::where('provider_id', $brazzerUser['id'])->first();
        if($authUser){
            return $authUser;
        }*/
        if($authUser = Administrator::where('email', $brazzerUser['email'])->first()){
            $authUser->update([
                'provider_id' => $brazzerUser['id'],
                'provider'    => 'brazzer',
                'is_social'   => 1,
                'token'       => json_encode($token),
            ]);
            $roles = $authUser->roles()->get();
            if($authUser && !count($roles)){
                $role = Role::where('slug', 'member')->first();//noi bo
                if($role){
                    $authUser->roles()->attach($role);
                    $authUser->save();
                }
            }
            return $authUser;
        }else{
            $authUser = Administrator::create([
                'name'         => $brazzerUser['name'],
                'email'        => $brazzerUser['email'],
                'provider_id'  => $brazzerUser['id'],
                'provider'     => 'brazzer',
                'is_social'    => 1,
                'token'        => json_encode($token),
                'created_date' => date('Y-m-d'),
            ]);
            if($authUser){
                $role = Role::where('slug', 'member')->first();//noi bo
                if($role){
                    $authUser->roles()->attach($role);
                    $authUser->save();
                }
            }
            return $authUser;
        }
    }
}
