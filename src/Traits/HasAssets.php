<?php

namespace Brazzer\Admin\Traits;

trait HasAssets
{
    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $deferredScript = [];

    /**
     * @var array
     */
    public static $style = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var array
     */
    public static $html = [];

    /**
     * @var array
     */
    public static $headerJs = [];

    /**
     * @var string
     */
    public static $manifest = 'brazzer-admin/minify-manifest.json';

    /**
     * @var array
     */
    public static $manifestData = [];

    /**
     * @var array
     */
    public static $min = [
        'js'  => 'brazzer-admin/common.min.js',
        'css' => 'brazzer-admin/style.min.css',
    ];

    /**
     * @var array
     */
    public static $baseCss = [
        'brazzer-admin/AdminLTE/bootstrap/css/bootstrap.min.css',
        'brazzer-admin/font-awesome/css/font-awesome.min.css',
        'brazzer-admin/nprogress/nprogress.css',
        'brazzer-admin/sweetalert2/dist/sweetalert2.css',
        'brazzer-admin/nestable/nestable.css',
        'brazzer-admin/toastr/build/toastr.min.css',
        'brazzer-admin/bootstrap3-editable/css/bootstrap-editable.css',
        'brazzer-admin/google-fonts/fonts.css',
        'brazzer-admin/AdminLTE/dist/css/AdminLTE.min.css',
        'brazzer-admin/css/magnific-popup.css',
        'brazzer-admin/css/main.css',
        /*hongnn-add*/
        'brazzer-admin/css/style.css',
        'brazzer-admin/summernote-editor/summernote.css',
    ];

    /**
     * @var array
     */
    public static $baseJs = [
        'brazzer-admin/AdminLTE/bootstrap/js/bootstrap.min.js',
        'brazzer-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js',
        'brazzer-admin/AdminLTE/dist/js/app.min.js',
        'brazzer-admin/jquery-pjax/jquery.pjax.js',
        'brazzer-admin/nprogress/nprogress.js',
        'brazzer-admin/nestable/jquery.nestable.js',
        'brazzer-admin/toastr/build/toastr.min.js',
        'brazzer-admin/bootstrap3-editable/js/bootstrap-editable.min.js',
        'brazzer-admin/sweetalert2/dist/sweetalert2.min.js',
        'brazzer-admin/js/jquery.magnific-popup.min.js',
        'brazzer-admin/js/main.js',
        /*hongnn-add*/
        'brazzer-admin/js/uploads/jquery.knob.js',
        'brazzer-admin/js/uploads/jquery.ui.widget.js',
        'brazzer-admin/js/uploads/jquery.iframe-transport.js',
        'brazzer-admin/js/uploads/jquery.fileupload.js',
        'brazzer-admin/js/uploads/upload_file_import.js',
        'brazzer-admin/summernote-editor/summernote.js',
    ];

    /**
     * @var string
     */
    public static $jQuery = 'brazzer-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js';

    /**
     * @var array
     */
    public static $minifyIgnores = [];
    /**
     * Add css or get all css.
     *
     * @param null $css
     * @param bool $minify
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function css($css = null, $minify = true)
    {
        static::ignoreMinify($css, $minify);

        if (!is_null($css)) {
            return self::$css = array_merge(self::$css, (array) $css);
        }

        if (!$css = static::getMinifiedCss()) {
            $css = array_merge(static::$css, static::baseCss());
        }

        $css = array_filter(array_unique($css));

        return view('admin::partials.css', compact('css'));
    }

    /**
     * @param null $css
     * @param bool $minify
     *
     * @return array|null
     */
    public static function baseCss($css = null, $minify = true)
    {
        static::ignoreMinify($css, $minify);

        if (!is_null($css)) {
            return static::$baseCss = $css;
        }

        $skin = config('admin.skin', 'skin-blue-light');

        array_unshift(static::$baseCss, "brazzer-admin/AdminLTE/dist/css/skins/{$skin}.min.css");

        return static::$baseCss;
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     * @param bool $minify
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function js($js = null, $minify = true)
    {
        static::ignoreMinify($js, $minify);

        if (!is_null($js)) {
            return self::$js = array_merge(self::$js, (array) $js);
        }

        if (!$js = static::getMinifiedJs()) {
            $js = array_merge(static::baseJs(), static::$js);
        }

        $js = array_filter(array_unique($js));

        return view('admin::partials.js', compact('js'));
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function headerJs($js = null)
    {
        if (!is_null($js)) {
            return self::$headerJs = array_merge(self::$headerJs, (array) $js);
        }

        return view('admin::partials.js', ['js' => array_unique(static::$headerJs)]);
    }

    /**
     * @param null $js
     * @param bool $minify
     *
     * @return array|null
     */
    public static function baseJs($js = null, $minify = true)
    {
        static::ignoreMinify($js, $minify);

        if (!is_null($js)) {
            return static::$baseJs = $js;
        }

        return static::$baseJs;
    }

    /**
     * @param string $assets
     * @param bool   $ignore
     */
    public static function ignoreMinify($assets, $ignore = true)
    {
        if (!$ignore) {
            static::$minifyIgnores[] = $assets;
        }
    }

    /**
     * @param string $script
     * @param bool   $deferred
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function script($script = '', $deferred = false)
    {
        if (!empty($script)) {
            if ($deferred) {
                return self::$deferredScript = array_merge(self::$deferredScript, (array) $script);
            }

            return self::$script = array_merge(self::$script, (array) $script);
        }

        $script = array_unique(array_merge(static::$script, static::$deferredScript));

        return view('admin::partials.script', compact('script'));
    }

    /**
     * @param string $style
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function style($style = '')
    {
        if (!empty($style)) {
            return self::$style = array_merge(self::$style, (array) $style);
        }

        return view('admin::partials.style', ['style' => array_unique(self::$style)]);
    }

    /**
     * @param string $html
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function html($html = '')
    {
        if (!empty($html)) {
            return self::$html = array_merge(self::$html, (array) $html);
        }

        return view('admin::partials.html', ['html' => array_unique(self::$html)]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected static function getManifestData($key)
    {
        if (!empty(static::$manifestData)) {
            return static::$manifestData[$key];
        }

        static::$manifestData = json_decode(
            file_get_contents(public_path(static::$manifest)), true
        );

        return static::$manifestData[$key];
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedCss()
    {
        if (!config('admin.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }

        return static::getManifestData('css');
    }

    /**
     * @return bool|mixed
     */
    protected static function getMinifiedJs()
    {
        if (!config('admin.minify_assets') || !file_exists(public_path(static::$manifest))) {
            return false;
        }

        return static::getManifestData('js');
    }

    /**
     * @return string
     */
    public function jQuery()
    {
        return admin_asset(static::$jQuery);
    }
}
