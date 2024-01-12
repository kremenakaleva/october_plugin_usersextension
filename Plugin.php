<?php namespace Pensoft\Usersextension;

use Backend;
use Illuminate\Support\Facades\DB;
use RainLab\User\Models\User;
use System\Classes\PluginBase;

/**
 * Usersextension Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Usersextension',
            'description' => 'No description provided yet...',
            'author'      => 'Pensoft',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        User::extend(function ($model) {
            if(\App::runningInBackend()) {

            }else{

                    $model->rules['email'] = 'required|between:6,255|email|isunique:users,email';
                    \Validator::extend('isunique', function ($attribute, $value, $parameters, $validator) {
                        $query = DB::table($parameters[0]);
                        $column = $query->getGrammar()->wrap($parameters[1]);
                        return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->count();
                    });
                    \Validator::replacer('isunique', function ($message, $attribute, $rule, $parameters) {
                        return 'The email has already been taken.';
                    });



            }

            $model->bindEvent('model.beforeSave', function() use ($model) {
                $model->email = strtolower(trim($model->email));
            });

        });

        \Event::listen('rainlab.user.beforeAuthenticate', function($component, $credentials) {
            $login = array_get($credentials, 'login');
            $password = array_get($credentials, 'password');
            $login = strtolower(trim($login));
//            // No such user exists
//            if (!$user = Auth::findUserByLogin($login)) {
//                return;
//            }

        });

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Pensoft\Usersextension\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'pensoft.usersextension.some_permission' => [
                'tab' => 'Usersextension',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'usersextension' => [
                'label'       => 'Usersextension',
                'url'         => Backend::url('pensoft/usersextension/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pensoft.usersextension.*'],
                'order'       => 500,
            ],
        ];
    }


}
