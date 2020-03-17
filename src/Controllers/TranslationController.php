<?php namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Auth\Database\Translation;
use Brazzer\Admin\Extension\Translation\TranslationManager;
use Brazzer\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Brazzer\Admin\Layout\Content;

class TranslationController extends BaseController{
    protected $manager;

    public function __construct(TranslationManager $manager){
        $this->manager = $manager;
    }

    public function getIndex($group = null){
        return Admin::content(function(Content $content) use ($group){
            $locales = $this->manager->getLocales();
            $groups = Translation::groupBy('group');
            $excludedGroups = $this->manager->getConfig('exclude_groups');
            if($excludedGroups){
                $groups->whereNotIn('group', $excludedGroups);
            }
            $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
            if($groups instanceof Collection){
                $groups = $groups->all();
            }
            $groups = ['' => 'Choose a group'] + $groups;
            $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();
            $allTranslations = Translation::where('group', $group)->orderBy('key', 'asc')->get();
            $numTranslations = count($allTranslations);
            $translations = [];
            foreach($allTranslations as $translation){
                $translations[$translation->key][$translation->locale] = $translation;
            }
            $content->body(view('admin::translation', [
                'translations'    => $translations,
                'locales'         => $locales,
                'groups'          => $groups,
                'group'           => $group,
                'numTranslations' => $numTranslations,
                'numChanged'      => $numChanged,
                'editUrl'         => route('admin.translation.edit', ['groupKey' => $group]),
                'deleteEnabled'   => $this->manager->getConfig('delete_enabled')
            ]));
        });
    }

    public function getView($group = null){
        return $this->getIndex($group);
    }

    protected function loadLocales(){
        //Set the default locale as the first one.
        $locales = Translation::groupBy('locale')->select('locale')->get()->pluck('locale');
        if($locales instanceof Collection){
            $locales = $locales->all();
        }
        $available_locales = config('app.available_locales');
        if($available_locales) {
            $locales = array_merge($available_locales,$locales);
        }
        return array_unique($locales);
    }

    public function postAdd($group = null){
        $keys = explode("\n", request()->get('keys'));
        foreach($keys as $key){
            $key = trim($key);
            if($group && $key){
                $this->manager->missingKey('*', $group, $key);
            }
        }
        return redirect()->back();
    }

    public function postEdit($group = null){
        if(!in_array($group, $this->manager->getConfig('exclude_groups'))){
            $name = request()->get('name');
            $value = request()->get('value');
            list($locale, $key) = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group'  => $group,
                'key'    => $key,
            ]);
            $translation->value = (string)$value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
            return array('status' => 'ok');
        }
    }

    public function postDelete($group = null, $key){
        if(!in_array($group, $this->manager->getConfig('exclude_groups')) && $this->manager->getConfig('delete_enabled')){
            Translation::where('group', $group)->where('key', $key)->delete();
            return ['status' => 'ok'];
        }
    }

    public function postImport(Request $request){
        $replace = $request->get('replace', false);
        $counter = $this->manager->importTranslations($replace);
        return [
            'status'  => 'ok',
            'counter' => $counter
        ];
    }

    public function postFind(){
        $numFound = $this->manager->findTranslations();
        return [
            'status'  => 'ok',
            'counter' => (int)$numFound
        ];
    }

    public function postPublish($group = null){
        $json = false;
        if($group === '_json'){
            $json = true;
        }
        $this->manager->exportTranslations($group, $json);

        return ['status' => 'ok'];
    }

    public function postAddGroup(Request $request){
        $group = str_replace(".", '', $request->input('new-group'));
        if($group){
            return redirect(route('admin.translation.view', $group));
        }else{
            return redirect()->back();
        }
    }

    public function postAddLocale(Request $request){
        $locales = $this->manager->getLocales();
        $newLocale = str_replace([], '-', trim($request->input('new-locale')));
        if(!$newLocale || in_array($newLocale, $locales)){
            return redirect()->back();
        }
        $this->manager->addLocale($newLocale);
        return redirect()->back();
    }

    public function postRemoveLocale(Request $request){
        foreach($request->input('remove-locale', []) as $locale => $val){
            $this->manager->removeLocale($locale);
        }
        return redirect()->back();
    }
}
