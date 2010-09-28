<?php
class CityRoute extends CakeRoute {
    public function parse($url) {
        $params = parent::parse($url);
        if (empty($params)) {
            return false;
        }
        Configure::load('prefecture');
        Configure::load('city');
        $prefectures = Configure::read('prefecture');
        $cities = Configure::read('city');
        if(in_array($params['prefecture'], $prefectures)){
            if(isset($params['city'])){
                if(in_array($params['city'], $cities))
                    return $params;
                return false;
            }
            return $params;
        }
        return false;
    }
}