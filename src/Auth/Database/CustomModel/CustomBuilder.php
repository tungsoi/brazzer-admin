<?php

namespace Auth\Database\CustomModel;

class CustomBuilder extends \Illuminate\Database\Eloquent\Builder{
    protected $relation_api = ['info'];

    protected function eagerLoadRelation(array $models, $name, \Closure $constraints){
        $relation = $this->getRelation($name);
        $relation->addEagerConstraints($models);
        $initRelation = $relation->initRelation($models, $name);
        if($this->relation_api && in_array($name, $this->relation_api)){
            $localKeyName = $relation->getLocalKeyName();
            $foreignKeyName = $relation->getForeignKeyName();
            if($initRelation){
                $list_id = [];
                foreach($initRelation as $model){
                    if($model && isset($model->$localKeyName)){
                        $id = $model->$localKeyName;
                        if($id != '' && !in_array($id, $list_id)){
                            $list_id[] = $id;
                        }
                    }
                }
                if($list_id){
                    $results = $relation->getRelated()->findWhere([$foreignKeyName => ['whereIn' => implode(',', $list_id)]], false);
                    foreach($initRelation as $model){
                        if($model && isset($model->$localKeyName)){
                            foreach($results as $result){
                                if(isset($result->$foreignKeyName) && $result->$foreignKeyName == $model->$localKeyName){
                                    $model->setRelation($name, $result);
                                }
                            }
                        }
                    }
                }
            }
            return $initRelation;
        }else{
            $getEager = $relation->getEager();
        }
        $constraints($relation);
        return $relation->match($initRelation, $getEager, $name);
    }
}