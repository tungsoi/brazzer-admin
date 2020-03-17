<?php

namespace Brazzer\Admin\Auth\Database;

use Brazzer\Admin\Traits\AdminBuilder;
use Brazzer\Admin\Traits\ModelTree;
use Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class ModelApi extends Model
{
    use AdminBuilder, ModelTree {
        ModelTree::boot as treeBoot;
    }
    protected static $route = '';
    protected static $param_only = [];
    protected static $per_page = 20;
    protected static $branchOrder = [];

    public function paginate()
    {
        $perPage = static::$per_page;
        $page = Request::get('page', 1);
        $params = \request()->only(static::$param_only);
        $params['per_page'] =  $perPage;
        $params['page'] =  $page;

        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_paginate_' . md5(http_build_query($params));
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $result = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $result = $model->callGet('', $params);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $result, 5);
            }
        } else {
            $model = new static([]);
            $result = $model->callGet('', $params);
        }
        if (!is_array($result)) {
            $result = json_decode($result, true);
        }

        $data = [];
        $total = 0;
        if ($result) {
            if (isset($result['data'])) {
                $data = $result['data'];
            }
            if (isset($result['total'])) {
                $total = $result['total'];
            }
        }
        $movies = static::hydrate($data);
        $paginator = new LengthAwarePaginator($movies, $total, $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    public static function pluck($value, $key = null, $params = [])
    {
        if (empty($params)) {
            $params = \request()->only(static::$param_only);
        }
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_pluck_' . md5(http_build_query($params));
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $items = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $items = $model->callGet('all', $params);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $items, 5);
            }
        } else {
            $model = new static([]);
            $items = $model->callGet('all', $params);
        }
        return Arr::pluck($items, $value, $key);
    }

    public function allNodes(): array
    {
        $params = \request()->only([
            'title',
            'description',
            'status'
        ]);
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_allNodes_' . md5(http_build_query($params));
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $items = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $items = $model->callGet('all', $params);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $items, 5);
            }
        } else {
            $model = new static([]);
            $items = $model->callGet('all', $params);
        }
        return $items;
    }

    public static function with($relations)
    {
        return new static;
    }

    public static function find($id)
    {
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_find_' . $id;
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $item = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $item = $model->callGet($id);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $item, 5);
            }
        } else {
            $model = new static([]);
            $item = $model->callGet($id);
        }
        if (!is_array($item)) {
            $item = json_decode($item, true);
        }
        $model = new static([]);
        $model->setRawAttributes((array)$item, true);
        return $model;
    }

    public static function findWhere($params, $toArray = false)
    {
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_findWhere_' . md5(http_build_query($params));
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $items = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $items = $model->callGet('all', $params);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $items, 5);
            }
        } else {
            $model = new static([]);
            $items = $model->callGet('all', $params);
        }
        if (!is_array($items)) {
            $items = json_decode($items, true);
        }
        if ($toArray) {
            return $items;
        } else {
            $list_item = new Collection();
            foreach ($items as $item) {
                $model = new static([]);
                $model->setRawAttributes((array)$item, true);
                $list_item->add($model);
            }
            return $list_item;
        }
    }

    public static function findWhereOne($params, $toArray = false)
    {
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_findWhereOne_' . md5(http_build_query($params));
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $items = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $items = $model->callGet('all', $params);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $items, 5);
            }
        } else {
            $model = new static([]);
            $items = $model->callGet('all', $params);
        }
        if (!is_array($items)) {
            $items = json_decode($items, true);
        }
        if ($items && isset($items[0])) {
            $items = $items[0];
        }
        if ($toArray) {
            return $items;
        } else {
            $model = new static([]);
            $model->setRawAttributes((array)$items, true);
            return $model;
        }
    }

    public function findOrFail($id)
    {
        $model_name = (new \ReflectionClass(new static([])))->getShortName();
        $keyCache = $model_name . '_findOrFail_' . $id;
        if (config('admin.model_api.cache')) {
            if (Cache::store(config('admin.model_api.store'))->has($keyCache)) {
                $item = Cache::store(config('admin.model_api.store'))->get($keyCache);
            } else {
                $model = new static([]);
                $item = $model->callGet($id);
                Cache::store(config('admin.model_api.store'))->put($keyCache, $item, 5);
            }
        } else {
            $model = new static([]);
            $item = $model->callGet($id);
        }
        if (!is_array($item)) {
            $item = json_decode($item, true);
        }
        $model = new static([]);
        $model->setRawAttributes((array)$item, true);
        return $model;
    }

    protected static function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));
        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    public static function saveOrder($tree, $parentId = 0)
    {
        if (is_string($tree)) {
            $tree = json_decode($tree, true);
        }
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }
        if ($tree) {
            foreach ($tree as $branch) {
                if (isset($branch['id']) && $branch['id'] > 0) {
                    $node = static::find($branch['id']);
                    if ($node) {
                        $node->parent_id = $parentId;
                        $node->order = static::$branchOrder[$branch['id']];
                        $node->save();
                        if (isset($branch['children'])) {
                            static::saveOrder($branch['children'], $branch['id']);
                        }
                    }
                }
            }
        }
    }

    public static function create($params = [])
    {
        try {
            $result = \Socialite::driver('brazzer')->postData('api/' . static::$route, $params);
            if (!is_array($result)) {
                $result = json_decode($result, true);
            }
            $model = new static([]);
            $model->setRawAttributes((array)$result, true);
            return $model;
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    public function save(array $options = [])
    {
        try {
            if ($this->getAttribute('id')) {
                $data = $this->getAttributes();
                if(!empty($options)){
                    $data = $options;
                }
                $result = \Socialite::driver('brazzer')->putData('api/' . static::$route . '/' . $this->getAttribute('id'), $data);
                $model_name = (new \ReflectionClass(new static([])))->getShortName();
                $id = $this->getAttribute('id');
                $keyCache = $model_name . '_find_' . $id;
                Cache::forget($keyCache);
                $keyCache = $model_name . '_findOrFail_' . $id;
                Cache::forget($keyCache);
            } else {
                $result = \Socialite::driver('brazzer')->postData('api/' . static::$route, $this->getAttributes());
            }
            if (!is_array($result)) {
                $result = json_decode($result, true);
            }
            $model = new static([]);
            $model->setRawAttributes((array)$result, true);
            return $model;
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    protected function callGet($path = '', $params = [])
    {
        try {
            $items = \Socialite::driver('brazzer')->getData('api/' . static::$route . '/' . $path . '?' . http_build_query($params));
        } catch (\Exception $e) {
            \Log::error($e);
            $items = [];
        }
        return $items;
    }
}