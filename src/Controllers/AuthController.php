<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Auth\Database\Administrator;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller{

    protected $loginView = 'admin::login';

    public function getLogin(){
        if($this->guard()->check()){
            return redirect($this->redirectPath());
        }
        return view($this->loginView);
    }

    public function postLogin(Request $request){
        if(config('admin.login.azure')){
            if(substr_count($request->input('email'), '@brazzerairways.com')){
                return redirect(route('admin.login'))->withErrors([
                    'error' => 'Bạn hãy đăng nhập với Microsoft Office 365',
                ]);
            }
        }
        $this->loginValidator($request->all())->validate();
        $credentials = $request->only([
            $this->username(),
            'password'
        ]);
        $remember = $request->get('remember', false);

        if($this->guard()->attempt($credentials, $remember)){
            return $this->sendLoginResponse($request);
        }
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            $this->username() => 'required',
            'password'        => 'required',
        ]);
    }

    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(config('admin.route.prefix'));
    }

    public function loginas(Request $request, Content $content){
        $user_id = $request->id;
        if($user_id == Admin::user()->id){
            admin_toastr('Bạn dang đăng nhập tài khoản này.', 'success');
            return redirect($this->redirectPath());
        }else{
            if(Admin::user()->isAdministrator()){
                $user = Administrator::find($user_id);
                if($user && $this->guard()->login($user)){
                    admin_toastr('Đăng nhập bằng tài khoản ' . $user->name . ' thành công.', 'success');
                    return redirect($this->redirectPath());
                }
            }
        }
        admin_toastr('Đăng nhập bằng tài khoản mới lỗi.', 'error');
    }

    public function getSetting(Content $content){
        $form = $this->settingForm();
        $form->tools(function(Form\Tools $tools){
            $tools->disableList();
        });
        return $content->header(trans('admin.user_setting'))->body($form->edit(Admin::user()->id));
    }

    public function putSetting(){
        return $this->settingForm()->update(Admin::user()->id);
    }

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        $class = config('admin.database.users_model');
        $form = new Form(new $class);
        $form->display('email', trans('admin.email'));
        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        if(config('admin.login.email')){
            $form->password('password', trans('admin.password'))->rules('confirmed|required');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')->default(function($form){
                return $form->model()->password;
            });
        }
        $form->setAction(admin_base_path('auth/setting'));
        $form->ignore(['password_confirmation']);
        $form->saving(function(Form $form){
            if($form->password && $form->model()->password != $form->password){
                $form->password = bcrypt($form->password);
            }
        });
        $form->saved(function(){
            admin_toastr(trans('admin.update_succeeded'));
            return redirect(admin_base_path('auth/setting'));
        });

        return $form;
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('admin.login_successful'));

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'email';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();/*Khong duoc sua Auth::guard() thanh Admin::guard()*/
    }
}
