<?php

/**
 * Reports controller.
 *
 * This file will render views from views/DownloadTables/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('CakeEmail', 'Network/Email');
/**
 * Email sender
 */
App::uses('AppController', 'Controller');

/**
 * DownloadTables controller
 *
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ReportsController extends AppController {

    public $uses = array('TravelHotelLookup','Common', 'TravelCity', 'TravelCountry','TravelCountrySupplier', 'TravelLookupContinent', 'TravelLookupValueContractStatus', 'TravelChain',
        'TravelSuburb', 'TravelArea', 'TravelBrand', 'Province', 'TravelHotelRoomSupplier', 'TravelCitySupplier', 'Mappinge', 'TravelSupplier', 'LogCall',
        'TravelActionItemType', 'User', 'DeleteTravelHotelLookup', 'DeleteLogTable', 'DeleteTravelHotelRoomSupplier', 'DeleteTravelCitySupplier', 'DeleteTravelCity',
        'DeleteTravelSuburb', 'DeleteTravelArea', 'DeleteProvince', 'TravelActionItem','TravelCitySuppliers',
        'ProvincePermission');

    public function index() {
        
    }

    public function reports() {


        $TravelCities = array();
        $search_condition = array();
        $country_id = $this->params['named']['country_id'];
        $continent_id = $this->params['named']['continent_id'];
        $province_id = $this->params['named']['province_id'];
        $active = '';
        $city_status = '';
        $wtb_status = '';
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'TravelCountry.id,TravelCountry.country_name', 'conditions' => array( 'TravelCountry.continent_id' => $continent_id,
                    'TravelCountry.country_status' => '1',
                    'TravelCountry.wtb_status' => '1',
                    'TravelCountry.active' => 'TRUE'), 'order' => 'TravelCountry.country_name ASC'));
        
        $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.continent_id' => $continent_id,
                    'Province.status' => '1',
                    'Province.wtb_status' => '1',
                    'Province.active' => 'TRUE',
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));

        if ($this->request->is('post') || $this->request->is('put')) {
            //$country_id = $this->data['Report']['country_id'];
            if (!empty($this->data['Report']['continent_id'])) {
                $continent_id = $this->data['Report']['continent_id'];
                array_push($search_condition, array('TravelCity.continent_id' => $continent_id));
            }
            if (!empty($this->data['Report']['country_id'])) {
                $country_id = $this->data['Report']['country_id'];
                array_push($search_condition, array('TravelCity.country_id' => $country_id));
            }
            if (!empty($this->data['Report']['province_id'])) {
                $province_id = $this->data['Report']['province_id'];
                array_push($search_condition, array('TravelCity.province_id' => $province_id));
            }
            if (!empty($this->data['Report']['active'])) {
                $active = $this->data['Report']['active'];
                array_push($search_condition, array('TravelCity.active' => $active));
            }
            if (!empty($this->data['Report']['city_status'])) {
                $city_status = $this->data['Report']['city_status'];
                array_push($search_condition, array('TravelCity.city_status' => $city_status));
            }
            if (!empty($this->data['Report']['wtb_status'])) {
                $wtb_status = $this->data['Report']['wtb_status'];
                array_push($search_condition, array('TravelCity.wtb_status' => $wtb_status));
            }
        }
        array_push($search_condition, array('TravelCity.country_id' => $country_id));


        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {
            
            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }
        }


        
        $this->TravelCity->bindModel(array(
            'hasMany' => array(
                'TravelHotelRoomSupplier' => array(
                    'className' => 'TravelHotelRoomSupplier',
                    'foreignKey' => 'hotel_city_id',
                    'fields' => 'TravelHotelRoomSupplier.id',
                    'conditions' => array('TravelHotelRoomSupplier.hotel_country_id' => $country_id,
                        'TravelHotelRoomSupplier.hotel_continent_id' => $continent_id,
                        'TravelHotelRoomSupplier.province_id' => $province_id)
                        
                ),
                'TravelCitySuppliers' => array(
                    'className' => 'TravelCitySuppliers',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelCitySuppliers.id',
                    'conditions' => array('TravelCitySuppliers.city_country_id' => $country_id,
                        'TravelCitySuppliers.city_continent_id' => $continent_id,
                        'TravelCitySuppliers.province_id' => $province_id)  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => array('TravelHotelLookup.country_id' => $country_id,
                        'TravelHotelLookup.continent_id' => $continent_id,
                        'TravelHotelLookup.province_id' => $province_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelSuburb' => array(
                    'className' => 'TravelSuburb',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelSuburb.id',
                    'conditions' => array('TravelSuburb.country_id' => $country_id,
                        'TravelSuburb.continent_id' => $continent_id,
                        'TravelSuburb.province_id' => $province_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelArea.id',
                    'conditions' => array('TravelArea.country_id' => $country_id,
                        'TravelArea.continent_id' => $continent_id,
                        'TravelArea.province_id' => $province_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));
        
        



        $TravelCities = $this->TravelCity->find('all', array(
            'conditions' => $search_condition,
            'order' => 'city_name ASC'
        ));

        // pr($TravelCities);

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('TravelCities', 'TravelCountries','Provinces','province_id', 'country_id','continent_id','TravelLookupContinents','wtb_status','active','city_status'));
    }
    
    public function summary_reports() {


        $TravelCities = array();
        $search_condition = array();
        $country_id = '220';
        //$continent_id = $this->params['named']['continent_id'];
        //$province_id = $this->params['named']['province_id'];
        $active = '';
        $city_status = '';
        $wtb_status = '';
        $hotel_count ='';
        $city_mapping_count = '';
        $hotel_mapping_count = '';      
        $area_count = '';
        $suburb_count = '';
        $HotelCon = array();
        $HotelMappingCon = array();
        $CityMappingCon = array();
        $SuburbCon = array();
        $AreaCon = array();
        $CityCon = array();
        $CountryCon = array();
        $CountryMappingCon = array();
        $ProvinceCon = array();

        if ($this->request->is('post') || $this->request->is('put')) {
            //$country_id = $this->data['Report']['country_id'];
         
            if (!empty($this->data['Report']['country_id'])) {
                $country_id = $this->data['Report']['country_id'];
                array_push($search_condition, array('TravelCity.country_id' => $country_id));
            }
       
            if (!empty($this->data['Report']['active'])) {
                $active = $this->data['Report']['active'];
                array_push($search_condition, array('TravelCity.active' => $active));
            }
            if (!empty($this->data['Report']['city_status'])) {
                $city_status = $this->data['Report']['city_status'];
                array_push($search_condition, array('TravelCity.city_status' => $city_status));
            }
            if (!empty($this->data['Report']['wtb_status'])) {
                $wtb_status = $this->data['Report']['wtb_status'];
                array_push($search_condition, array('TravelCity.wtb_status' => $wtb_status));
            }
        }
        //array_push($search_condition, array('TravelCity.country_id' => $country_id));
        array_push($CountryCon, array('TravelCountry.continent_id' => 0));       
        array_push($CountryMappingCon, array('TravelCountrySupplier.country_continent_id' => 0));       
        array_push($ProvinceCon, array('Province.continent_id' => 0));       
        array_push($HotelCon, array('TravelHotelLookup.continent_id' => 0));       
        array_push($HotelMappingCon, array('TravelHotelRoomSupplier.hotel_continent_id' => 0));
        array_push($CityCon, array('TravelCity.continent_id' => 0));
        array_push($CityMappingCon, array('TravelCitySuppliers.city_continent_id' => 0));
        array_push($SuburbCon, array('TravelSuburb.continent_id' => 0));
        array_push($AreaCon, array('TravelArea.continent_id' => 0));


        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {
            
            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }
        }


        $this->TravelLookupContinent->bindModel(array(
            'hasMany' => array(
                'TravelHotelRoomSupplier' => array(
                    'className' => 'TravelHotelRoomSupplier',
                    'foreignKey' => 'hotel_continent_id',
                    'fields' => 'TravelHotelRoomSupplier.id',
                    'conditions' => array(
                        //'TravelHotelRoomSupplier.hotel_country_id' => $country_id,
                        //'TravelHotelRoomSupplier.hotel_continent_id' => $continent_id,
                        //'TravelHotelRoomSupplier.province_id' => $province_id
                )
                        
                ),
                'TravelCitySupplier' => array(
                    'className' => 'TravelCitySupplier',
                    'foreignKey' => 'city_continent_id',
                    'fields' => 'TravelCitySupplier.id',
                    'conditions' => array(
                        //'TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelCity' => array(
                    'className' => 'TravelCity',
                    'foreignKey' => 'continent_id',
                    'fields' => 'TravelCity.id',
                    'conditions' => array(
                        //'TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'Province' => array(
                    'className' => 'Province',
                    'foreignKey' => 'continent_id',
                    'fields' => 'Province.id',
                    'conditions' => array(
                        //'TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelCountrySupplier' => array(
                    'className' => 'TravelCountrySupplier',
                    'foreignKey' => 'country_continent_id',
                    'fields' => 'TravelCountrySupplier.id',
                    'conditions' => array(
                        //'TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelCountry' => array(
                    'className' => 'TravelCountry',
                    'foreignKey' => 'continent_id',
                    'fields' => 'TravelCountry.id',
                    'conditions' => array(
                        //'TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'continent_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => array(
                        //'TravelHotelLookup.country_id' => $country_id,
                        //'TravelHotelLookup.continent_id' => $continent_id,
                        //'TravelHotelLookup.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelSuburb' => array(
                    'className' => 'TravelSuburb',
                    'foreignKey' => 'continent_id',
                    'fields' => 'TravelSuburb.id',
                    'conditions' => array(
                        //'TravelSuburb.country_id' => $country_id,
                        //'TravelSuburb.continent_id' => $continent_id,
                        //'TravelSuburb.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'continent_id',
                    'fields' => 'TravelArea.id',
                    'conditions' => array(
                        //'TravelArea.country_id' => $country_id,
                        //'TravelArea.continent_id' => $continent_id,
                        //'TravelArea.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));
        
        



        $TravelLookupContinents = $this->TravelLookupContinent->find('all', array(
            'conditions' => $search_condition,
            'order' => 'continent_name ASC'
        ));

        $country_count = $this->TravelCountry->find('count',array('conditions' => $CountryCon));
        $country_mapping_count = $this->TravelCountrySupplier->find('count',array('conditions' => $CountryMappingCon));
        $province_count = $this->Province->find('count',array('conditions' => $ProvinceCon));
         $hotel_count = $this->TravelHotelLookup->find('count',array('conditions' => $HotelCon));
         $city_count = $this->TravelCity->find('count',array('conditions' => $CityCon));
         $city_mapping_count = $this->TravelCitySuppliers->find('count',array('conditions' => $CityMappingCon));
         $hotel_mapping_count = $this->TravelHotelRoomSupplier->find('count',array('conditions' => $HotelMappingCon));
         $area_count = $this->TravelArea->find('count',array('conditions' => $AreaCon));
         $suburb_count = $this->TravelSuburb->find('count',array('conditions' => $SuburbCon));
         
        // pr($TravelCities);

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('TravelCities','country_count','country_mapping_count','province_count','city_count','hotel_count','city_mapping_count','hotel_mapping_count',
                'area_count','suburb_count', 'TravelCountries','Provinces','province_id', 'country_id','continent_id','TravelLookupContinents','wtb_status','active','city_status'));
    }
    
    public function city_reports() {


        $TravelCities = array();
        $search_condition = array();
        $country_id = '220';
        //$continent_id = $this->params['named']['continent_id'];
        //$province_id = $this->params['named']['province_id'];
        $active = '';
        $city_status = '';
        $wtb_status = '';
        $hotel_count ='';
        $city_mapping_count = '';
        $hotel_mapping_count = '';      
        $area_count = '';
        $suburb_count = '';
        $HotelCon = array();
        $HotelMappingCon = array();
        $CityMappingCon = array();
        $SuburbCon = array();
        $AreaCon = array();
        $miss_match = array();
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'TravelCountry.id,TravelCountry.country_name', 'conditions' => array(
                    'TravelCountry.country_status' => '1',
                    'TravelCountry.wtb_status' => '1',
                    'TravelCountry.active' => 'TRUE'), 'order' => 'TravelCountry.country_name ASC'));
        
        $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    //'Province.continent_id' => $continent_id,
                    'Province.status' => '1',
                    'Province.wtb_status' => '1',
                    'Province.active' => 'TRUE',
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));

        if ($this->request->is('post') || $this->request->is('put')) {
            //$country_id = $this->data['Report']['country_id'];
         
            if (!empty($this->data['Report']['country_id'])) {
                $country_id = $this->data['Report']['country_id'];
                array_push($search_condition, array('TravelCity.country_id' => $country_id));
            }
       
            if (!empty($this->data['Report']['active'])) {
                $active = $this->data['Report']['active'];
                array_push($search_condition, array('TravelCity.active' => $active));
            }
            if (!empty($this->data['Report']['city_status'])) {
                $city_status = $this->data['Report']['city_status'];
                array_push($search_condition, array('TravelCity.city_status' => $city_status));
            }
            if (!empty($this->data['Report']['wtb_status'])) {
                $wtb_status = $this->data['Report']['wtb_status'];
                array_push($search_condition, array('TravelCity.wtb_status' => $wtb_status));
            }
        }
        array_push($search_condition, array('TravelCity.country_id' => $country_id));

        array_push($HotelCon, array('TravelHotelLookup.country_id' => $country_id,'TravelHotelLookup.city_id' => 0));       
        array_push($HotelMappingCon, array('TravelHotelRoomSupplier.hotel_country_id' => $country_id,'TravelHotelRoomSupplier.hotel_city_id' => 0));
        array_push($CityMappingCon, array('TravelCitySuppliers.city_country_id' => $country_id,'TravelCitySuppliers.city_id' => 0));
        array_push($SuburbCon, array('TravelSuburb.country_id' => $country_id,'TravelSuburb.city_id' => 0));
        array_push($AreaCon, array('TravelArea.country_id' => $country_id,'TravelArea.city_id' => 0));


        if (count($this->params['pass'])) {
            $search_condition = array();
           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
                
            }                
        } elseif (count($this->params['named'])) {
            $search_condition = array();
            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCity.' . $key => $value));
               
            }
        }


        $this->TravelCity->bindModel(array(
            'hasMany' => array(
                'TravelHotelRoomSupplier' => array(
                    'className' => 'TravelHotelRoomSupplier',
                    'foreignKey' => 'hotel_city_id',
                    'fields' => 'TravelHotelRoomSupplier.id',
                    'conditions' => array('TravelHotelRoomSupplier.hotel_country_id' => $country_id,
                        //'TravelHotelRoomSupplier.hotel_continent_id' => $continent_id,
                        //'TravelHotelRoomSupplier.province_id' => $province_id
                )
                        
                ),
                'TravelCitySuppliers' => array(
                    'className' => 'TravelCitySuppliers',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelCitySuppliers.id',
                    'conditions' => array('TravelCitySuppliers.city_country_id' => $country_id,
                        //'TravelCitySuppliers.city_continent_id' => $continent_id,
                        //'TravelCitySuppliers.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => array('TravelHotelLookup.country_id' => $country_id,
                        //'TravelHotelLookup.continent_id' => $continent_id,
                        //'TravelHotelLookup.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
           
                'TravelSuburb' => array(
                    'className' => 'TravelSuburb',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelSuburb.id',
                    'conditions' => array('TravelSuburb.country_id' => $country_id,
                        //'TravelSuburb.continent_id' => $continent_id,
                        //'TravelSuburb.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'city_id',
                    'fields' => 'TravelArea.id',
                    'conditions' => array('TravelArea.country_id' => $country_id,
                        //'TravelArea.continent_id' => $continent_id,
                        //'TravelArea.province_id' => $province_id
                )  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));
        
        



        $TravelCities = $this->TravelCity->find('all', array(
         
            'conditions' => $search_condition,
            'order' => 'city_name ASC'
        ));
        
        //$log = $this->TravelCity->getDataSource()->getLog(false, false);       
        //debug($log);
        //die;
         $hotel_count = $this->TravelHotelLookup->find('count',array('conditions' => $HotelCon));
         $city_mapping_count = $this->TravelCitySuppliers->find('count',array('conditions' => $CityMappingCon));
         $hotel_mapping_count = $this->TravelHotelRoomSupplier->find('count',array('conditions' => $HotelMappingCon));
         $area_count = $this->TravelArea->find('count',array('conditions' => $AreaCon));
         $suburb_count = $this->TravelSuburb->find('count',array('conditions' => $SuburbCon));
         
         $miss_match = $this->TravelHotelLookup->find
                (
                'all', array
            (
            'fields' => array('COUNT(TravelHotelLookup.city_id) AS cnt'),
            'joins' => array(
                array(
                    'table' => 'travel_cities',
                    'alias' => 'TravelCity',
                    'conditions' => array(
                        'TravelCity.id = TravelHotelLookup.city_id'
                    )
                )
            ),
            'conditions' => array
                (
                'TravelHotelLookup.city_id NOT IN (SELECT id FROM travel_cities WHERE country_id = "' . $country_id . '")',
                'TravelHotelLookup.country_id' => $country_id
            ),
            'group' => 'TravelHotelLookup.city_id',
            'order' => 'TravelHotelLookup.city_name ASC'
                )
        );
         
        // pr($TravelCities);

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('TravelCities','miss_match','hotel_count','city_mapping_count','hotel_mapping_count',
                'area_count','suburb_count', 'TravelCountries','Provinces','province_id', 'country_id','continent_id','TravelLookupContinents','wtb_status','active','city_status'));
    }

    public function province_reports() {


        $TravelCities = array();
        $search_condition = array();
        $TravelCountries = array();
        $HotelCon = array();
        $CityCon = array();
        $CityMappingCon = array();
        $HotelMappingCon = array();
        $AreaCon = array();
        $SuburbCon = array();
        $country_id = '220';
        $continent_id = '2';
        $active = '';
        $status = '';
        $wtb_status = '';
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'TravelCountry.id,TravelCountry.country_name', 'conditions' => array( 'TravelCountry.continent_id' => $continent_id,
                    'TravelCountry.country_status' => '1',
                    'TravelCountry.wtb_status' => '1',
                    'TravelCountry.active' => 'TRUE'), 'order' => 'TravelCountry.country_name ASC'));

        if ($this->request->is('post') || $this->request->is('put')) {
            
            if (!empty($this->data['Report']['continent_id'])) {
                $continent_id = $this->data['Report']['continent_id'];
                array_push($search_condition, array('Province.continent_id' => $continent_id));
                
            }
            if (!empty($this->data['Report']['country_id'])) {
                $country_id = $this->data['Report']['country_id'];
                array_push($search_condition, array('Province.country_id' => $country_id));
                
            }
            if (!empty($this->data['Report']['active'])) {
                $active = $this->data['Report']['active'];
                array_push($search_condition, array('Province.active' => $active));
                array_push($HotelCon, array('TravelHotelLookup.active' => $active));
            }
            if (!empty($this->data['Report']['status'])) {
                $status = $this->data['Report']['status'];
                array_push($search_condition, array('Province.status' => $status));
                array_push($HotelCon, array('TravelHotelLookup.status' => $status));
            }
            if (!empty($this->data['Report']['wtb_status'])) {
                $wtb_status = $this->data['Report']['wtb_status'];
                array_push($search_condition, array('Province.wtb_status' => $wtb_status));
            }
        }
        array_push($search_condition, array('Province.country_id' => $country_id));
        array_push($HotelCon, array('TravelHotelLookup.country_id' => $country_id,'TravelHotelLookup.province_id' => 0));
        array_push($CityCon, array('TravelCity.country_id' => $country_id,'TravelCity.province_id' => 0));
        array_push($HotelMappingCon, array('TravelHotelRoomSupplier.hotel_country_id' => $country_id,'TravelHotelRoomSupplier.province_id' => 0));
        array_push($CityMappingCon, array('TravelCitySuppliers.city_country_id' => $country_id,'TravelCitySuppliers.province_id' => 0));
        array_push($SuburbCon, array('TravelSuburb.country_id' => $country_id,'TravelSuburb.province_id' => 0));
        array_push($AreaCon, array('TravelArea.country_id' => $country_id,'TravelArea.province_id' => 0));



        $this->Province->bindModel(array(
            'hasMany' => array(
                'TravelHotelRoomSupplier' => array(
                    'className' => 'TravelHotelRoomSupplier',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelHotelRoomSupplier.id',
                    'conditions' => array('TravelHotelRoomSupplier.hotel_country_id' => $country_id,
                        'TravelHotelRoomSupplier.hotel_continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelCity' => array(
                    'className' => 'TravelCity',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelCity.id',
                    'conditions' => array('TravelCity.country_id' => $country_id,
                        'TravelCity.continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelCitySuppliers' => array(
                    'className' => 'TravelCitySuppliers',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelCitySuppliers.id',
                    'conditions' => array('TravelCitySuppliers.city_country_id' => $country_id,
                        'TravelCitySuppliers.city_continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levels
                ),
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => array('TravelHotelLookup.country_id' => $country_id,
                        'TravelHotelLookup.continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelSuburb' => array(
                    'className' => 'TravelSuburb',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelSuburb.id',
                    'conditions' => array('TravelSuburb.country_id' => $country_id,
                        'TravelSuburb.continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'province_id',
                    'fields' => 'TravelArea.id',
                    'conditions' => array('TravelArea.country_id' => $country_id,
                        'TravelArea.continent_id' => $continent_id)  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));



        $Provinces = $this->Province->find('all', array(
            'conditions' => $search_condition,
            'order' => 'name ASC'
        ));
        
         $hotel_count = $this->TravelHotelLookup->find('count',array('conditions' => $HotelCon));
         $city_count = $this->TravelCity->find('count',array('conditions' => $CityCon));
         $city_mapping_count = $this->TravelCitySuppliers->find('count',array('conditions' => $CityMappingCon));
         $hotel_mapping_count = $this->TravelHotelRoomSupplier->find('count',array('conditions' => $HotelMappingCon));
         $area_count = $this->TravelArea->find('count',array('conditions' => $AreaCon));
         $suburb_count = $this->TravelSuburb->find('count',array('conditions' => $SuburbCon));

        // pr($TravelCities);
        

        $this->set(compact('Provinces', 'TravelCountries','city_mapping_count','hotel_mapping_count','area_count','suburb_count', 'country_id','city_count','hotel_count','TravelLookupContinents','continent_id','wtb_status','active','status'));
    }

    function delete($id = null) {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $user_id = $this->Auth->user('id');
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');


        $TravelCities = $this->TravelCity->findById($id);
        $city_name = $TravelCities['TravelCity']['city_name'];
         if ($this->Common->checkCityExistsCityMapping($id)) {
                $this->Session->setFlash($city_name.' city can not deleted, '.$city_name.' exists in city mapping table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsSuburb($id)) {
                $this->Session->setFlash($city_name.' city can not deleted, '.$city_name.' exists in suburb table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsArea($id)) {
                $this->Session->setFlash($city_name.' city can not deleted, '.$city_name.' exists in area table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsHotel($id)) {
                $this->Session->setFlash($city_name.' city can not deleted, '.$city_name.' exists in hotel table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }

        $this->request->data['DeleteTravelCity']['id'] = $TravelCities['TravelCity']['id'];
        $this->request->data['DeleteTravelCity']['city_name'] = $TravelCities['TravelCity']['city_name'];
        $this->request->data['DeleteTravelCity']['city_code'] = $TravelCities['TravelCity']['city_code'];
        $this->request->data['DeleteTravelCity']['country_id'] = $TravelCities['TravelCity']['country_id'];
        $this->request->data['DeleteTravelCity']['country_code'] = $TravelCities['TravelCity']['country_code'];
        $this->request->data['DeleteTravelCity']['country_name'] = $TravelCities['TravelCity']['country_name'];
        $this->request->data['DeleteTravelCity']['continent_id'] = $TravelCities['TravelCity']['continent_id'];
        $this->request->data['DeleteTravelCity']['province_id'] = $TravelCities['TravelCity']['province_id'];
        $this->request->data['DeleteTravelCity']['province_name'] = $TravelCities['TravelCity']['province_name'];
        $this->request->data['DeleteTravelCity']['continent_name'] = $TravelCities['TravelCity']['continent_name'];
        $this->request->data['DeleteTravelCity']['city_description'] = $TravelCities['TravelCity']['city_description'];
        $this->request->data['DeleteTravelCity']['top_city'] = $TravelCities['TravelCity']['top_city'];
        $this->request->data['DeleteTravelCity']['active'] = $TravelCities['TravelCity']['active'];
        $this->request->data['DeleteTravelCity']['is_update'] = $TravelCities['TravelCity']['is_update'];
        $this->request->data['DeleteTravelCity']['approved_by'] = $TravelCities['TravelCity']['approved_by'];
        $this->request->data['DeleteTravelCity']['approved_date'] = $TravelCities['TravelCity']['approved_date'];
        $this->request->data['DeleteTravelCity']['created_by'] = $TravelCities['TravelCity']['created_by'];
        $this->request->data['DeleteTravelCity']['created'] = $TravelCities['TravelCity']['created'];
        $this->request->data['DeleteTravelCity']['modified'] = $TravelCities['TravelCity']['modified'];
        $this->request->data['DeleteTravelCity']['wtb_status'] = $TravelCities['TravelCity']['wtb_status'];
        $this->request->data['DeleteTravelCity']['city_status'] = $TravelCities['TravelCity']['city_status'];

        if ($this->TravelCity->delete($id)) {

            $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="City">
                                                      <CityId>' . $id . '</CityId>            
                                                  </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


            $log_call_screen = 'Delete - City';

            $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
            $client = new SoapClient(null, array(
                'location' => $location_URL,
                'uri' => '',
                'trace' => 1,
            ));

            try {
                $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                $xml_arr = $this->xml2array($order_return);
                //echo htmlentities($xml_string);
                //pr($xml_arr);
                //die;

                if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                    $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                    $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                    $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                } else {

                    $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                    $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                    $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                    $xml_error = 'TRUE';
                }
            } catch (SoapFault $exception) {
                var_dump(get_class($exception));
                var_dump($exception);
            }


            $this->request->data['LogCall']['log_call_nature'] = 'Production';
            $this->request->data['LogCall']['log_call_type'] = 'Outbound';
            $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
            $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
            $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
            $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
            $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
            $this->request->data['LogCall']['log_call_by'] = $user_id;
            $this->LogCall->create();
            $this->LogCall->save($this->request->data['LogCall']);
            $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

            $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
            $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
            if ($xml_error == 'TRUE') {
                $Email = new CakeEmail();

                $Email->viewVars(array(
                    'request_xml' => trim($xml_string),
                    'respon_message' => $log_call_status_message,
                    'respon_code' => $log_call_status_code,
                ));

                $to = 'biswajit@wtbglobal.com';
                $cc = 'infra@sumanus.com';

                $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
            }



            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            $this->request->data['DeleteTravelCity']['log_id'] = $LogId;
            $this->DeleteTravelCity->create();
            $this->DeleteTravelCity->save($this->request->data['DeleteTravelCity']);
            $this->Session->setFlash($message, 'success');

            $this->redirect(array('action' => 'reports'));
        } else {
            $this->Session->setFlash('Unable to delete City.', 'failure');
            $this->redirect(array('action' => 'reports'));
        }
    }

    public function mismatch_hotel() {

        $TravelCities = array();
        $country_id = '220';

        if ($this->request->is('post') || $this->request->is('put')) {
            $country_id = $this->data['Report']['country_id'];
        }




        $this->TravelHotelLookup->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier'))
        );


        //SELECT `city_name`,count(`id`) FROM `travel_hotel_lookups` WHERE `country_id`='220' and `city_id` not in(select id from travel_cities where country_id='220') group by `city_id` 
//$TravelHotelLookups = $this->TravelHotelLookup->query("SELECT `city_name`,count(`id`) FROM `travel_hotel_lookups` WHERE `country_id`='220' and `city_id` not in(select id from travel_cities where country_id='220') group by `city_id` ");

        $TravelHotelLookups = $this->TravelHotelLookup->find
                (
                'all', array
            (
            'fields' => array('TravelHotelLookup.city_name', 'TravelHotelLookup.city_id', 'TravelHotelLookup.country_id', 'TravelHotelLookup.city_code', 'TravelCity.city_name', 'TravelCity.country_name', 'TravelCity.continent_name', 'TravelHotelLookup.country_name', 'COUNT(TravelHotelLookup.city_id) AS cnt'),
            'joins' => array(
                array(
                    'table' => 'travel_cities',
                    'alias' => 'TravelCity',
                    'conditions' => array(
                        'TravelCity.id = TravelHotelLookup.city_id'
                    )
                )
            ),
            'conditions' => array
                (
                'TravelHotelLookup.city_id NOT IN (SELECT id FROM travel_cities WHERE country_id = "' . $country_id . '")',
                'TravelHotelLookup.country_id' => $country_id
            ),
            'group' => 'TravelHotelLookup.city_id',
            'order' => 'TravelHotelLookup.city_name ASC'
                )
        );

//pr($TravelHotelLookups);
        //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
        //debug($log);
        //pr($TravelHotelLookups);
        //die;
        /*
          $TravelCities = $this->TravelCity->find('all', array(
          'conditions' => array('TravelCity.country_id' => $country_id),
          'fields' => array('id', 'city_name','country_name','city_code')
          ));
         * 
         */

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('TravelCountries', 'country_id', 'TravelHotelLookups'));
    }
    
    public function mismatch_country() {

        $TravelCities = array();
        $country_id = '220';

        if ($this->request->is('post') || $this->request->is('put')) {
            $country_id = $this->data['Report']['country_id'];
        }




        $this->TravelHotelLookup->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier'))
        );


        //SELECT `city_name`,count(`id`) FROM `travel_hotel_lookups` WHERE `country_id`='220' and `city_id` not in(select id from travel_cities where country_id='220') group by `city_id` 
//$TravelHotelLookups = $this->TravelHotelLookup->query("SELECT `city_name`,count(`id`) FROM `travel_hotel_lookups` WHERE `country_id`='220' and `city_id` not in(select id from travel_cities where country_id='220') group by `city_id` ");

        $TravelHotelLookups = $this->TravelHotelLookup->find
                (
                'all', array
            (
            'fields' => array('TravelHotelLookup.city_name', 'TravelHotelLookup.city_id', 'TravelHotelLookup.country_id', 'TravelHotelLookup.city_code', 'TravelCity.city_name', 'TravelCity.country_name', 'TravelCity.continent_name', 'TravelHotelLookup.country_name', 'COUNT(TravelHotelLookup.city_id) AS cnt'),
            'joins' => array(
                array(
                    'table' => 'travel_cities',
                    'alias' => 'TravelCity',
                    'conditions' => array(
                        'TravelCity.id = TravelHotelLookup.city_id'
                    )
                )
            ),
            'conditions' => array
                (
                'TravelHotelLookup.city_id IN (SELECT id FROM travel_cities WHERE country_id = "' . $country_id . '")',
                'TravelHotelLookup.country_id !=' => $country_id
            ),
            'group' => 'TravelHotelLookup.city_id',
            'order' => 'TravelHotelLookup.city_name ASC'
                )
        );

//pr($TravelHotelLookups);
        //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
        //debug($log);
        //pr($TravelHotelLookups);
        //die;
        /*
          $TravelCities = $this->TravelCity->find('all', array(
          'conditions' => array('TravelCity.country_id' => $country_id),
          'fields' => array('id', 'city_name','country_name','city_code')
          ));
         * 
         */

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('TravelCountries', 'country_id', 'TravelHotelLookups'));
    }

    public function hotel_summary() {


        // $city_id = $this->Auth->user('city_id');
        $user_id = $this->Auth->user('id');
        $search_condition = array();
        $hotel_name = '';
        $continent_id = '';
        $country_id = '';
        $city_id = '';
        $suburb_id = '';
        $area_id = '';
        $chain_id = '';
        $brand_id = '';
        $status = '';
        $wtb_status = '';
        $active = '';
        $province_id = '';
        $TravelCities = array();
        $TravelCountries = array();
        $TravelSuburbs = array();
        $TravelAreas = array();
        $TravelChains = array();
        $TravelBrands = array();
        $Provinces = array();


        if ($this->request->is('post') || $this->request->is('put')) {
            // pr($this->request);
            //die;
            if (!empty($this->data['TravelHotelLookup']['hotel_name'])) {
                $hotel_name = $this->data['TravelHotelLookup']['hotel_name'];
                array_push($search_condition, array('OR' => array('TravelHotelLookup.id' . ' LIKE' => $hotel_name, 'TravelHotelLookup.hotel_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.hotel_code' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.country_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.city_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.area_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%")));
            }
            if (!empty($this->data['TravelHotelLookup']['continent_id'])) {
                $continent_id = $this->data['TravelHotelLookup']['continent_id'];
                array_push($search_condition, array('TravelHotelLookup.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id, country_name', 'conditions' => array('TravelCountry.continent_id' => $continent_id,
                        ), 'order' => 'country_name ASC'));
            }

            if (!empty($this->data['TravelHotelLookup']['country_id'])) {
                $country_id = $this->data['TravelHotelLookup']['country_id'];
                $province_id = $this->data['TravelHotelLookup']['province_id'];
                array_push($search_condition, array('TravelHotelLookup.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id, city_name', 'conditions' => array('TravelCity.province_id' => $province_id,
                       ), 'order' => 'city_name ASC'));
            }
            if (!empty($this->data['TravelHotelLookup']['province_id'])) {

                array_push($search_condition, array('TravelHotelLookup.province_id' => $province_id));
                $Provinces = $this->Province->find('list', array(
                    'conditions' => array(
                        'Province.country_id' => $country_id,
                        'Province.continent_id' => $continent_id,
                    
                    ),
                    'fields' => array('Province.id', 'Province.name'),
                    'order' => 'Province.name ASC'
                ));
            }
            if (!empty($this->data['TravelHotelLookup']['city_id'])) {
                $city_id = $this->data['TravelHotelLookup']['city_id'];
                array_push($search_condition, array('TravelHotelLookup.city_id' => $city_id));
                $TravelSuburbs = $this->TravelSuburb->find('list', array(
                    'conditions' => array(
                        'TravelSuburb.country_id' => $country_id,
                        'TravelSuburb.city_id' => $city_id,
                       
                    ),
                    'fields' => 'TravelSuburb.id, TravelSuburb.name',
                    'order' => 'TravelSuburb.name ASC'
                ));
            }
            if (!empty($this->data['TravelHotelLookup']['suburb_id'])) {
                $suburb_id = $this->data['TravelHotelLookup']['suburb_id'];
                array_push($search_condition, array('TravelHotelLookup.suburb_id' => $suburb_id));
                $TravelAreas = $this->TravelArea->find('list', array(
                    'conditions' => array(
                        'TravelArea.suburb_id' => $suburb_id,
                     
                    ),
                    'fields' => 'TravelArea.id, TravelArea.area_name',
                    'order' => 'TravelArea.area_name ASC'
                ));
            }


            if (!empty($this->data['TravelHotelLookup']['area_id'])) {
                $area_id = $this->data['TravelHotelLookup']['area_id'];
                array_push($search_condition, array('TravelHotelLookup.area_id' => $area_id));
            }
            if (!empty($this->data['TravelHotelLookup']['chain_id'])) {
                $chain_id = $this->data['TravelHotelLookup']['chain_id'];
                array_push($search_condition, array('TravelHotelLookup.chain_id' => $chain_id));
                $TravelBrands = $this->TravelBrand->find('list', array(
                    'conditions' => array(
                        'TravelBrand.brand_chain_id' => $chain_id,
                        
                    ),
                    'fields' => 'TravelBrand.id, TravelBrand.brand_name',
                    'order' => 'TravelBrand.brand_name ASC'
                ));
                $TravelBrands = array('1' => 'No Brand') + $TravelBrands;
            }
            if (!empty($this->data['TravelHotelLookup']['brand_id'])) {
                $brand_id = $this->data['TravelHotelLookup']['brand_id'];
                array_push($search_condition, array('TravelHotelLookup.brand_id' => $brand_id));
            }
            if (!empty($this->data['TravelHotelLookup']['status'])) {
                $status = $this->data['TravelHotelLookup']['status'];
                array_push($search_condition, array('TravelHotelLookup.status' => $status));
            }
            if (!empty($this->data['TravelHotelLookup']['wtb_status'])) {
                $wtb_status = $this->data['TravelHotelLookup']['wtb_status'];
                array_push($search_condition, array('TravelHotelLookup.wtb_status' => $wtb_status));
            }
            if (!empty($this->data['TravelHotelLookup']['active'])) {
                $active = $this->data['TravelHotelLookup']['active'];
                array_push($search_condition, array('TravelHotelLookup.active' => $active));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['hotel_name'])) {
                $hotel_name = $this->request->params['named']['hotel_name'];
                array_push($search_condition, array('OR' => array('TravelHotelLookup.hotel_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.hotel_code' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.country_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.city_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%", 'TravelHotelLookup.area_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_name))) . "%")));
            }

            if (!empty($this->request->params['named']['continent_id'])) {
                $continent_id = $this->request->params['named']['continent_id'];
                array_push($search_condition, array('TravelHotelLookup.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id, country_name', 'conditions' => array('TravelCountry.continent_id' => $continent_id,
                        'TravelCountry.country_status' => '1',
                        'TravelCountry.wtb_status' => '1',
                        'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));
            }

            if (!empty($this->request->params['named']['country_id'])) {
                $country_id = $this->request->params['named']['country_id'];
                $province_id = $this->request->params['named']['province_id'];
                array_push($search_condition, array('TravelHotelLookup.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id, city_name', 'conditions' => array('TravelCity.province_id' => $province_id,
                       ), 'order' => 'city_name ASC'));
            }
            if (!empty($this->request->params['named']['province_id'])) {

                array_push($search_condition, array('TravelHotelLookup.province_id' => $province_id));
                $Provinces = $this->Province->find('list', array(
                    'conditions' => array(
                        'Province.country_id' => $country_id,
                        'Province.continent_id' => $continent_id,
                        
                    ),
                    'fields' => array('Province.id', 'Province.name'),
                    'order' => 'Province.name ASC'
                ));
            }

            if (!empty($this->request->params['named']['city_id'])) {
                $city_id = $this->request->params['named']['city_id'];
                array_push($search_condition, array('TravelHotelLookup.city_id' => $city_id));
                $TravelSuburbs = $this->TravelSuburb->find('list', array(
                    'conditions' => array(
                        'TravelSuburb.country_id' => $country_id,
                        'TravelSuburb.city_id' => $city_id,
                 
                    ),
                    'fields' => 'TravelSuburb.id, TravelSuburb.name',
                    'order' => 'TravelSuburb.name ASC'
                ));
            }

            if (!empty($this->request->params['named']['suburb_id'])) {
                $suburb_id = $this->request->params['named']['suburb_id'];
                array_push($search_condition, array('TravelHotelLookup.suburb_id' => $suburb_id));
                $TravelAreas = $this->TravelArea->find('list', array(
                    'conditions' => array(
                        'TravelArea.suburb_id' => $suburb_id,
                        
                    ),
                    'fields' => 'TravelArea.id, TravelArea.area_name',
                    'order' => 'TravelArea.area_name ASC'
                ));
            }
            if (!empty($this->request->params['named']['area_id'])) {
                $area_id = $this->request->params['named']['area_id'];
                array_push($search_condition, array('TravelHotelLookup.area_id' => $area_id));
            }
            if (!empty($this->request->params['named']['chain_id'])) {
                $chain_id = $this->request->params['named']['chain_id'];
                array_push($search_condition, array('TravelHotelLookup.chain_id' => $chain_id));
                $TravelBrands = $this->TravelBrand->find('list', array(
                    'conditions' => array(
                        'TravelBrand.brand_chain_id' => $chain_id,
                        
                    ),
                    'fields' => 'TravelBrand.id, TravelBrand.brand_name',
                    'order' => 'TravelBrand.brand_name ASC'
                ));
                $TravelBrands = array('1' => 'No Brand') + $TravelBrands;
            }
            if (!empty($this->request->params['named']['brand_id'])) {
                $brand_id = $this->request->params['named']['brand_id'];
                array_push($search_condition, array('TravelHotelLookup.brand_id' => $brand_id));
            }
            if (!empty($this->request->params['named']['status'])) {
                $status = $this->request->params['named']['status'];
                array_push($search_condition, array('TravelHotelLookup.status' => $status));
            }
            if (!empty($this->request->params['named']['wtb_status'])) {
                $wtb_status = $this->request->params['named']['wtb_status'];
                array_push($search_condition, array('TravelHotelLookup.wtb_status' => $wtb_status));
            }
            if (!empty($this->request->params['named']['active'])) {
                $active = $this->request->params['named']['active'];
                array_push($search_condition, array('TravelHotelLookup.active' => $active));
            }
        }


        $this->TravelHotelLookup->bindModel(array(
            'hasMany' => array(
                'HotelPending' => array(
                    'className' => 'TravelActionItem',
                    'foreignKey' => 'hotel_id',
                    'fields' => 'HotelPending.id',
                    'conditions' => array('HotelPending.level_id' => '7', 'HotelPending.action_item_active' => 'Yes', 'HotelPending.next_action_by != "NULL"')
                ),
            /*
              'MappingPending' => array(
              'className' => 'TravelActionItem',
              'fields' => 'MappingPending.id',
              'foreignKey' => false,
              'conditions' => array('MappingPending.hotel_supplier_id' => '{$ __cakeID__ $}','MappingPending.level_id' => '4','MappingPending.action_item_active' => 'Yes', 'MappingPending.next_action_by != NULL')  // 1 for client table of  lookup_value_activity_levels
              ),

              'MappingPending' => array(
              'className' => 'TravelActionItem',
              'foreignKey' => false,
              'fields' => 'MappingPending.id',
              'conditions' => array('MappingPending.level_id' => '4', 'MappingPending.action_item_active' => 'Yes'),
              'finderQuery'   => 'select * from travel_hotel_room_suppliers as `Action` where
              `Action`.`hotel_id` = {$__cakeID__$} ',

              ),
             * 
             */
            )
        ));
        
        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelHotelLookup.' . $key => $value));
                $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                $conSuburbArry = array('TravelSuburb.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelHotelLookup.' . $key => $value));
                $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                $conSuburbArry = array('TravelSuburb.'.$key => $value);
            }
        }



        /*
          if (count($this->params['pass'])) {


          $aaray = explode(':', $this->params['pass'][0]);
          $field = $aaray[0];
          $value = $aaray[1];
          array_push($search_condition, array('TravelHotelLookup.' . $field . ' LIKE' => '%' . $value . '%')); // when builder is approve/pending
          }

          elseif(count($this->params['named'])){
          foreach($this->params['named'] as $key=>$val){
          array_push($search_condition, array('TravelHotelLookup.' .$key.' LIKE' => '%'.$val.'%')); // when builder is approve/pending
          }
          }
         * 
         */
        //array_push($search_condition, array('TravelHotelLookup.country_id' => '220'));

        $this->paginate['order'] = array('TravelHotelLookup.city_code' => 'asc');
        $this->set('TravelHotelLookups', $this->paginate("TravelHotelLookup", $search_condition));

        //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
        //debug($log);
        //die;



        if (!isset($this->passedArgs['hotel_name']) && empty($this->passedArgs['hotel_name'])) {
            if(isset($this->data['TravelHotelLookup']['hotel_name']) && !empty($this->data['TravelHotelLookup']['hotel_name'])) 
                $this->passedArgs['hotel_name'] =  $this->data['TravelHotelLookup']['hotel_name'];
        }
        if (!isset($this->passedArgs['continent_id']) && empty($this->passedArgs['continent_id'])) {
            if(isset($this->data['TravelHotelLookup']['continent_id']) && !empty($this->data['TravelHotelLookup']['continent_id'])) 
                $this->passedArgs['continent_id'] =  $this->data['TravelHotelLookup']['continent_id'];
        }
        if (!isset($this->passedArgs['country_id']) && empty($this->passedArgs['country_id'])) {
            if(isset($this->data['TravelHotelLookup']['country_id']) && !empty($this->data['TravelHotelLookup']['country_id'])) 
                $this->passedArgs['country_id'] =  $this->data['TravelHotelLookup']['country_id'];
        }
        if (!isset($this->passedArgs['province_id']) && empty($this->passedArgs['province_id'])) {
             if(isset($this->data['TravelHotelLookup']['province_id']) && !empty($this->data['TravelHotelLookup']['province_id'])) 
                 $this->passedArgs['province_id'] = $this->data['TravelHotelLookup']['province_id'];
        }
        if (!isset($this->passedArgs['city_id']) && empty($this->passedArgs['city_id'])) {
            if(isset($this->data['TravelHotelLookup']['city_id']) && !empty($this->data['TravelHotelLookup']['city_id'])) 
                $this->passedArgs['city_id'] =  $this->data['TravelHotelLookup']['city_id'];
        }
        if (!isset($this->passedArgs['suburb_id']) && empty($this->passedArgs['suburb_id'])) {
            if(isset($this->data['TravelHotelLookup']['suburb_id']) && !empty($this->data['TravelHotelLookup']['suburb_id'])) 
                $this->passedArgs['suburb_id'] = $this->data['TravelHotelLookup']['suburb_id'];
        }
        if (!isset($this->passedArgs['area_id']) && empty($this->passedArgs['area_id'])) {
            if(isset($this->data['TravelHotelLookup']['area_id']) && !empty($this->data['TravelHotelLookup']['area_id'])) 
                $this->passedArgs['area_id'] =  $this->data['TravelHotelLookup']['area_id'];
        }
        if (!isset($this->passedArgs['chain_id']) && empty($this->passedArgs['chain_id'])) {
            if(isset($this->data['TravelHotelLookup']['chain_id']) && !empty($this->data['TravelHotelLookup']['chain_id'])) 
                $this->passedArgs['chain_id'] =  $this->data['TravelHotelLookup']['chain_id'];
        }
        if (!isset($this->passedArgs['brand_id']) && empty($this->passedArgs['brand_id'])) {
            if(isset($this->data['TravelHotelLookup']['brand_id']) && !empty($this->data['TravelHotelLookup']['brand_id'])) 
                $this->passedArgs['brand_id'] =  $this->data['TravelHotelLookup']['brand_id'];
        }
        if (!isset($this->passedArgs['status']) && empty($this->passedArgs['status'])) {
            if(isset($this->data['TravelHotelLookup']['status']) && !empty($this->data['TravelHotelLookup']['status'])) 
                $this->passedArgs['status'] =  $this->data['TravelHotelLookup']['status'];
        }
        if (!isset($this->passedArgs['wtb_status']) && empty($this->passedArgs['wtb_status'])) {
            if(isset($this->data['TravelHotelLookup']['wtb_status']) && !empty($this->data['TravelHotelLookup']['wtb_status'])) 
                $this->passedArgs['wtb_status'] =  $this->data['TravelHotelLookup']['wtb_status'];
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
             if(isset($this->data['TravelHotelLookup']['active']) && !empty($this->data['TravelHotelLookup']['active'])) 
                 $this->passedArgs['active'] = $this->data['TravelHotelLookup']['active'] ;
        }



        if (!isset($this->data) && empty($this->data)) {
            $this->data['TravelHotelLookup']['hotel_name'] = $this->passedArgs['hotel_name'];
            $this->data['TravelHotelLookup']['continent_id'] = $this->passedArgs['continent_id'];
            $this->data['TravelHotelLookup']['country_id'] = $this->passedArgs['country_id'];
            $this->data['TravelHotelLookup']['province_id'] = $this->passedArgs['province_id'];
            $this->data['TravelHotelLookup']['city_id'] = $this->passedArgs['city_id'];
            $this->data['TravelHotelLookup']['suburb_id'] = $this->passedArgs['suburb_id'];
            $this->data['TravelHotelLookup']['area_id'] = $this->passedArgs['area_id'];
            $this->data['TravelHotelLookup']['chain_id'] = $this->passedArgs['chain_id'];
            $this->data['TravelHotelLookup']['brand_id'] = $this->passedArgs['brand_id'];
            $this->data['TravelHotelLookup']['status'] = $this->passedArgs['status'];
            $this->data['TravelHotelLookup']['wtb_status'] = $this->passedArgs['wtb_status'];
            $this->data['TravelHotelLookup']['active'] = $this->passedArgs['active'];
        }

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array(), 'order' => 'continent_name ASC'));
        $TravelLookupValueContractStatuses = $this->TravelLookupValueContractStatus->find('list', array('fields' => 'id, value', 'order' => 'value ASC'));
        $TravelChains = $this->TravelChain->find('list', array('fields' => 'id,chain_name', 'conditions' => array(), 'order' => 'chain_name ASC'));
        $TravelChains = array('1' => 'No Chain') + $TravelChains;
        
        $TravelAreas = $this->TravelArea->find('list', array(
                    'conditions' => array(
                       
                    ),
                    'fields' => 'TravelArea.id, TravelArea.area_name',
                    'order' => 'TravelArea.area_name ASC'
                ));
        
        $TravelSuburbs = $this->TravelSuburb->find('list', array(
                    'conditions' => array(
                       
                    ),
                    'fields' => 'TravelSuburb.id, TravelSuburb.name',
                    'order' => 'TravelSuburb.name ASC'
                ));
        
        $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                   
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));
        
        $TravelCities = $this->TravelCity->find('list', array('fields' => 'id, city_name', 'conditions' => array(
                        ), 'order' => 'city_name ASC'));
        
         $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id, country_name', 'conditions' => array(
                        ), 'order' => 'country_name ASC'));
         
         $TravelBrands = $this->TravelBrand->find('list', array(
                    'conditions' => array(
                      
                    ),
                    'fields' => 'TravelBrand.id, TravelBrand.brand_name',
                    'order' => 'TravelBrand.brand_name ASC'
                ));
        //$TravelActionItems = $this->TravelActionItem->find('count', array('conditions' => array('level_id' => '4', 'action_item_active' => 'Yes', 'hotel_id' => 'TRUE'), 'order' => 'continent_name ASC'));

        $this->set(compact('hotel_name', 'continent_id', 'country_id', 'city_id', 'suburb_id', 'area_id', 'TravelChains', 'status', 'active', 
                'chain_id', 'brand_id', 'wtb_status', 'TravelCountries', 'TravelCities', 'TravelSuburbs', 'TravelAreas', 
                'TravelChains', 'TravelBrands', 'TravelLookupValueContractStatuses', 'TravelLookupContinents', 'Provinces', 'province_id'));
    }
    
    public function support_hotel_summary($id=null, $type=null) {


        // $city_id = $this->Auth->user('city_id');
        $user_id = $this->Auth->user('id');
        $search_condition = array();
        $TravelHotelLookups = array();
        $condition = array();
        $dataConArray = array();
        $DuplicateData = array();
        $display = 'FALSE';

        $this->TravelHotelLookup->bindModel(array(
            'hasMany' => array(
                'HotelPending' => array(
                    'className' => 'TravelActionItem',
                    'foreignKey' => 'hotel_id',
                    'fields' => 'HotelPending.id',
                    'conditions' => array('HotelPending.level_id' => '7', 'HotelPending.action_item_active' => 'Yes', 'HotelPending.next_action_by != "NULL"')
                ),
        
            )
        ));
        
        if($id){
            $display = 'TRUE';
            array_push($search_condition, array('TravelHotelLookup.id' => $id));
            $TravelHotelLookup = $this->TravelHotelLookup->findById($id);
            $continent_id = $TravelHotelLookup['TravelHotelLookup']['continent_id'];
            $country_id = $TravelHotelLookup['TravelHotelLookup']['country_id'];
            $province_id = $TravelHotelLookup['TravelHotelLookup']['province_id'];
            $city_id = $TravelHotelLookup['TravelHotelLookup']['city_id'];
            $hotel_name = $TravelHotelLookup['TravelHotelLookup']['hotel_name'];
            
            for ($indexOfFirstLetter = 0; $indexOfFirstLetter <= strlen($hotel_name); $indexOfFirstLetter++) {
                for ($indexOfLastLetter = $indexOfFirstLetter + 1; $indexOfLastLetter <= strlen($hotel_name); $indexOfLastLetter++) {
                    $new_arr[] = substr($hotel_name, $indexOfFirstLetter, 4);
                  
                    if (strlen($new_arr[$indexOfFirstLetter]) == '4') {
                        array_push($condition, array("TravelHotelLookup.hotel_name LIKE '%$new_arr[$indexOfFirstLetter]%'"));
                    }
                  
                    $indexOfFirstLetter++;
                }
            }
            
            
            array_push($dataConArray, array('OR' => $condition, 'TravelHotelLookup.country_id' => $country_id, 'TravelHotelLookup.city_id' => $city_id, 'TravelHotelLookup.province_id' => $province_id, 'TravelHotelLookup.city_id' => $city_id, 'TravelHotelLookup.id !=' => $id));
            
            $this->paginate['order'] = array('TravelHotelLookup.hotel_name' => 'asc');
            $this->set('DuplicateData', $this->paginate("TravelHotelLookup", $dataConArray));
            
        }
        else{
            if (count($this->params['pass'])) {

               foreach ($this->params['pass'] as $key => $value) {
                    array_push($search_condition, array('TravelHotelLookup.' . $key => $value));
                    $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                    $conSuburbArry = array('TravelSuburb.'.$key => $value);
                }                
            } elseif (count($this->params['named'])) {

                foreach ($this->params['named'] as $key => $value) {
                    array_push($search_condition, array('TravelHotelLookup.' . $key => $value));
                    $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                    $conSuburbArry = array('TravelSuburb.'.$key => $value);
                }
            }
        }
        
   
       
        //array_push($search_condition, array('TravelHotelLookup.country_id' => '220'));
        $TravelHotelLookups = $this->TravelHotelLookup->find('all',array('conditions' => $search_condition));
        $this->paginate['order'] = array('TravelHotelLookup.city_code' => 'asc');
        $this->set('TravelHotelLookups', $this->paginate("TravelHotelLookup", $search_condition));
        
        $this->set(compact('id','TravelHotelLookups','display','user_id'));
        //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
       // debug($log);
        //die;

    }

    public function hotel_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $user_id = $this->Auth->user('id');
        $xml_error = 'FALSE';
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
        $save = array();
        $message = '';

        foreach ($this->data['TravelHotelLookup']['check'] as $val) {


            $TravelHotelLookups = $this->TravelHotelLookup->findById($val);

            $HotelId = $TravelHotelLookups['TravelHotelLookup']['id'];
            $HotelCode = $TravelHotelLookups['TravelHotelLookup']['hotel_code'];
            $HotelName = $TravelHotelLookups['TravelHotelLookup']['hotel_name'];
            $AreaId = $TravelHotelLookups['TravelHotelLookup']['area_id'];
            $AreaName = $TravelHotelLookups['TravelHotelLookup']['area_name'];
            $AreaCode = $TravelHotelLookups['TravelHotelLookup']['area_code'];
            $SuburbId = $TravelHotelLookups['TravelHotelLookup']['suburb_id'];
            $SuburbName = $TravelHotelLookups['TravelHotelLookup']['suburb_name'];
            $CityId = $TravelHotelLookups['TravelHotelLookup']['city_id'];
            $CityName = $TravelHotelLookups['TravelHotelLookup']['city_name'];
            $CityCode = $TravelHotelLookups['TravelHotelLookup']['city_code'];
            $CountryId = $TravelHotelLookups['TravelHotelLookup']['country_id'];
            $CountryName = $TravelHotelLookups['TravelHotelLookup']['country_name'];
            $CountryCode = $TravelHotelLookups['TravelHotelLookup']['country_code'];
            $ContinentId = $TravelHotelLookups['TravelHotelLookup']['continent_id'];
            $ContinentName = $TravelHotelLookups['TravelHotelLookup']['continent_name'];
            $ContinentCode = $TravelHotelLookups['TravelHotelLookup']['continent_code'];
            $BrandId = $TravelHotelLookups['TravelHotelLookup']['brand_id'];
            $BrandName = $TravelHotelLookups['TravelHotelLookup']['brand_name'];
            $ChainId = $TravelHotelLookups['TravelHotelLookup']['chain_id'];
            $ChainName = $TravelHotelLookups['TravelHotelLookup']['chain_name'];
            $HotelComment = $TravelHotelLookups['TravelHotelLookup']['hotel_comment'];
            $Star = $TravelHotelLookups['TravelHotelLookup']['star'];
            $Keyword = $TravelHotelLookups['TravelHotelLookup']['keyword'];
            $StandardRating = $TravelHotelLookups['TravelHotelLookup']['standard_rating'];
            $HotelRating = $TravelHotelLookups['TravelHotelLookup']['hotel_rating'];
            $FoodRating = $TravelHotelLookups['TravelHotelLookup']['food_rating'];
            $ServiceRating = $TravelHotelLookups['TravelHotelLookup']['service_rating'];
            $LocationRating = $TravelHotelLookups['TravelHotelLookup']['location_rating'];
            $ValueRating = $TravelHotelLookups['TravelHotelLookup']['value_rating'];
            $OverallRating = $TravelHotelLookups['TravelHotelLookup']['overall_rating'];
            $HotelImage1 = $TravelHotelLookups['TravelHotelLookup']['hotel_img1'];
            $HotelImage2 = $TravelHotelLookups['TravelHotelLookup']['hotel_img2'];
            $HotelImage3 = $TravelHotelLookups['TravelHotelLookup']['hotel_img3'];
            $HotelImage4 = $TravelHotelLookups['TravelHotelLookup']['hotel_img4'];
            $HotelImage5 = $TravelHotelLookups['TravelHotelLookup']['hotel_img5'];
            $HotelImage6 = $TravelHotelLookups['TravelHotelLookup']['hotel_img6'];
            $Logo = $TravelHotelLookups['TravelHotelLookup']['logo'];
            $Logo1 = $TravelHotelLookups['TravelHotelLookup']['logo1'];
            $BusinessCenter = $TravelHotelLookups['TravelHotelLookup']['business_center'];
            $MeetingFacilities = $TravelHotelLookups['TravelHotelLookup']['meeting_facilities'];
            $DiningFacilities = $TravelHotelLookups['TravelHotelLookup']['dining_facilities'];
            $BarLounge = $TravelHotelLookups['TravelHotelLookup']['bar_lounge'];
            $FitnessCenter = $TravelHotelLookups['TravelHotelLookup']['fitness_center'];
            $Pool = $TravelHotelLookups['TravelHotelLookup']['pool'];
            $Golf = $TravelHotelLookups['TravelHotelLookup']['golf'];
            $Tennis = $TravelHotelLookups['TravelHotelLookup']['tennis'];
            $Kids = $TravelHotelLookups['TravelHotelLookup']['kids'];
            $Handicap = $TravelHotelLookups['TravelHotelLookup']['handicap'];
            $URLHotel = $TravelHotelLookups['TravelHotelLookup']['url_hotel'];
            $Address = $TravelHotelLookups['TravelHotelLookup']['address'];
            $PostCode = $TravelHotelLookups['TravelHotelLookup']['post_code'];
            $NoRoom = $TravelHotelLookups['TravelHotelLookup']['no_room'];
            $Active = $TravelHotelLookups['TravelHotelLookup']['active'];
            $ReservationEmail = $TravelHotelLookups['TravelHotelLookup']['reservation_email'];
            $ReservationContact = $TravelHotelLookups['TravelHotelLookup']['reservation_contact'];
            $EmergencyContactName = $TravelHotelLookups['TravelHotelLookup']['emergency_contact_name'];
            $ReservationDeskNumber = $TravelHotelLookups['TravelHotelLookup']['reservation_desk_number'];
            $EmergencyContactNumber = $TravelHotelLookups['TravelHotelLookup']['emergency_contact_number'];
            $GPSPARAM1 = $TravelHotelLookups['TravelHotelLookup']['gps_prm_1'];
            $GPSPARAM2 = $TravelHotelLookups['TravelHotelLookup']['gps_prm_2'];
            $ProvinceId = $TravelHotelLookups['TravelHotelLookup']['province_id'];
            $ProvinceName = $TravelHotelLookups['TravelHotelLookup']['province_name'];
            $TopHotel = $TravelHotelLookups['TravelHotelLookup']['top_hotel'];
            $is_update = $TravelHotelLookups['TravelHotelLookup']['is_updated'];
            $ApprovedBy = $TravelHotelLookups['TravelHotelLookup']['approved_by'];
            $ApprovedDate = $TravelHotelLookups['TravelHotelLookup']['approved_date'];
            $CreatedBy = $TravelHotelLookups['TravelHotelLookup']['created_by'];
            $Created = $TravelHotelLookups['TravelHotelLookup']['created'];
            $Modified = $TravelHotelLookups['TravelHotelLookup']['modified'];
            
            /*
             * Hotel exists in other tables
             */
            
            if ($this->Common->checkHotelExistsHotelMapping($HotelId)) {
                $this->Session->setFlash($HotelName.' hotel can not deleted, '.$HotelName.' exists in hotel mapping table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'hotel_summary'));
            }
          

            $save[] = array('DeleteTravelHotelLookup' => array(
                    'id' => $HotelId,
                    'hotel_code' => $HotelCode,
                    'hotel_name' => $HotelName,
                    'area_id' => $AreaId,
                    'area_name' => $AreaName,
                    'area_code' => $AreaCode,
                    'suburb_id' => $SuburbId,
                    'suburb_name' => $SuburbName,
                    'city_id' => $CityId,
                    'city_name' => $CityName,
                    'city_code' => $CityCode,
                    'country_id' => $CountryId,
                    'country_name' => $CountryName,
                    'country_code' => $CountryCode,
                    'continent_id' => $ContinentId,
                    'continent_name' => $ContinentName,
                    'continent_code' => $ContinentCode,
                    'brand_id' => $BrandId,
                    'brand_name' => $BrandName,
                    'chain_id' => $ChainId,
                    'chain_name' => $ChainName,
                    'hotel_comment' => $HotelComment,
                    'star' => $Star,
                    'keyword' => $Keyword,
                    'standard_rating' => $StandardRating,
                    'hotel_rating' => $HotelRating,
                    'food_rating' => $FoodRating,
                    'service_rating' => $ServiceRating,
                    'location_rating' => $LocationRating,
                    'value_rating' => $ValueRating,
                    'overall_rating' => $OverallRating,
                    'hotel_img1' => $HotelImage1,
                    'hotel_img2' => $HotelImage2,
                    'hotel_img3' => $HotelImage3,
                    'hotel_img4' => $HotelImage4,
                    'hotel_img5' => $HotelImage5,
                    'hotel_img6' => $HotelImage6,
                    'logo' => $Logo,
                    'logo1' => $Logo1,
                    'business_center' => $BusinessCenter,
                    'meeting_facilities' => $MeetingFacilities,
                    'dining_facilities' => $DiningFacilities,
                    'bar_lounge' => $BarLounge,
                    'fitness_center' => $FitnessCenter,
                    'pool' => $Pool,
                    'golf' => $Golf,
                    'tennis' => $Tennis,
                    'kids' => $Kids,
                    'handicap' => $Handicap,
                    'url_hotel' => $URLHotel,
                    'address' => $Address,
                    'post_code' => $PostCode,
                    'no_room' => $NoRoom,
                    'active' => $Active,
                    'reservation_email' => $ReservationEmail,
                    'reservation_contact' => $ReservationContact,
                    'emergency_contact_name' => $EmergencyContactName,
                    'reservation_desk_number' => $ReservationDeskNumber,
                    'emergency_contact_number' => $EmergencyContactNumber,
                    'gps_prm_1' => $GPSPARAM1,
                    'gps_prm_2' => $GPSPARAM2,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'top_hotel' => $TopHotel,
                    'is_updated' => $is_update,
                    'approved_by' => $ApprovedBy,
                    'approved_date' => $ApprovedDate,
                    'created_by' => $CreatedBy,
                    'created' => $Created,
                    'modified' => $Modified,
            ));


            if ($this->TravelHotelLookup->delete($val)) {

                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="Hotel">
                                                      <HotelId>' . $val . '</HotelId>            
                                                  </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - Hotel';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }


                $flag = 1;
            }
        }



        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelHotelLookup']['log_id'] = $LogId;
            }
            $this->DeleteTravelHotelLookup->create();
            $this->DeleteTravelHotelLookup->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete Hotel.', 'failure');
        }

        return $this->redirect(array('controller' => 'reports', 'action' => 'hotel_summary'));
    }

    public function city_edit($id = null, $mode = 1) {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $user_id = $this->Auth->user('id');
        $xml_error = 'FALSE';

        $this->set(compact('mode'));
        if (!$id) {
            throw new NotFoundException(__('Invalid City'));
        }

        $TravelCities = $this->TravelCity->findById($id);

        if (!$TravelCities) {
            throw new NotFoundException(__('Invalid City'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->TravelCity->set($this->data);
            if ($this->TravelCity->validates() == true) {

                $this->TravelCity->id = $id;
                if ($this->TravelCity->save($this->request->data)) {
                    $CityId = $id;
                    $CityName = $this->data['TravelCity']['city_name'];
                    $CityCode = $this->data['TravelCity']['city_code'];
                    $CountryId = $this->data['TravelCity']['country_id'];
                    $CountryCode = $this->data['TravelCity']['country_code'];
                    $CountryName = $this->data['TravelCity']['country_name'];
                    $ContinentId = $this->data['TravelCity']['continent_id'];
                    $ProvinceId = $this->data['TravelCity']['province_id'];
                    $ProvinceName = $this->data['TravelCity']['province_name'];
                    $ContinentName = $this->data['TravelCity']['continent_name'];
                    $CityDescription = $this->data['TravelCity']['city_description'];
                    $TopCity = strtolower($this->data['TravelCity']['top_city']);
                    $Active = strtolower($this->data['TravelCity']['active']);
                    $CityStatus = $this->data['TravelCity']['city_status'];
                    $Status = $CityStatus ? 'true' : 'false';
                    $is_update = $TravelCities['TravelCity']['is_update'];
                    if ($is_update == 'Y')
                        $actiontype = 'Update';
                    else
                        $actiontype = 'AddNew';
                    $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');

                    $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_City</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="' . $actiontype . '">
                                                                <CityId>' . $CityId . '</CityId>
                                                                <CityCode><![CDATA[' . $CityCode . ']]></CityCode>
                                                                <CityName><![CDATA[' . $CityName . ']]></CityName>
                                                                <CountryId>' . $CountryId . '</CountryId>
                                                                <CountryCode><![CDATA[' . $CountryCode . ']]></CountryCode>
                                                                <CountryName><![CDATA[' . $CountryName . ']]></CountryName>
                                                                <ContinentId>' . $ContinentId . '</ContinentId>
                                                                <ContinentCode>NA</ContinentCode>
                                                                <ContinentName><![CDATA[' . $ContinentName . ']]></ContinentName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName><![CDATA[' . $ProvinceName . ']]></ProvinceName>
                                                                <RegionId></RegionId>
                                                                <CityNameJP></CityNameJP>
                                                                <CityNameFR></CityNameFR>
                                                                <CityNameDE>NA</CityNameDE>
                                                                <CityNameCN>NA</CityNameCN>
                                                                <CityNameCNT>NA</CityNameCNT>
                                                                <CityNameIT>NA</CityNameIT>
                                                                <CityNameES>NA</CityNameES>
                                                                <CityNameKR>NA</CityNameKR>
                                                                <CityNameTEMP1>NA</CityNameTEMP1>
                                                                <CityNameTEMP2>NA</CityNameTEMP2>
                                                                <CityNameTEMP3>NA</CityNameTEMP3>
                                                                <City_Keyword>NA</City_Keyword>
                                                                <CityURL>NA</CityURL>
                                                                <CityNameURL>NA</CityNameURL>
                                                                <CityURLTEMP1>NA</CityURLTEMP1>
                                                                <CityURLTEMP2>NA</CityURLTEMP2>
                                                                <CityURLTEMP3>NA</CityURLTEMP3>
                                                                <CityDomainName>NA</CityDomainName>
                                                                <CityTitle>NA</CityTitle>
                                                                <CityDescription><![CDATA[' . $CityDescription . ']]></CityDescription>
                                                                <CityKeyword>NA</CityKeyword>
                                                                <ActiveMap>false</ActiveMap>
                                                                <ActiveGuide>false</ActiveGuide>
                                                                <IsUpdated>0</IsUpdated>
                                                                <PFTActive>1</PFTActive>
                                                                <SSActive>true</SSActive>
                                                                <TopDestination>true</TopDestination>
                                                                <StateCode>NA</StateCode>
                                                                <IsXML>true</IsXML>
                                                                <Active>' . $Active . '</Active>
                                                                <TopCity>' . $TopCity . '</TopCity>
                                                                <Status>' . $Status . '</Status>
                                                                <WtbStatus>false</WtbStatus>
                                                                <ApprovedBy>0</ApprovedBy>
                                                                <ApprovedDate>1111-01-01T00:00:00</ApprovedDate>
                                                                <CreatedBy>' . $user_id . '</CreatedBy>
                                                                <CreatedDate>' . $CreatedDate . '</CreatedDate>
                                                            </ResourceDetailsData>
                         
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';


                    $log_call_screen = 'City - Edit';

                    $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                    $client = new SoapClient(null, array(
                        'location' => $location_URL,
                        'uri' => '',
                        'trace' => 1,
                    ));

                    try {
                        $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                        $xml_arr = $this->xml2array($order_return);
                        //   echo htmlentities($xml_string);
                        //   pr($xml_arr);
                        //   die;

                        if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_CITY']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_CITY']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_CITY']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                            $xml_msg = "Foreign record has been successfully updated [Code:$log_call_status_code]";
                            $this->TravelCity->updateAll(array('TravelCity.wtb_status' => "'1'", 'TravelCity.is_update' => "'Y'"), array('TravelCity.id' => $CityId));
                        } else {

                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_CITY']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_CITY']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                            $xml_msg = "There was a problem with foreign record updation [Code:$log_call_status_code]";
                            $this->TravelCity->updateAll(array('TravelCity.wtb_status' => "'2'"), array('TravelCity.id' => $CityId));
                            $xml_error = 'TRUE';
                        }
                    } catch (SoapFault $exception) {
                        var_dump(get_class($exception));
                        var_dump($exception);
                    }


                    $this->request->data['LogCall']['log_call_nature'] = 'Production';
                    $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                    $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                    $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                    $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                    $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                    $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                    $this->request->data['LogCall']['log_call_by'] = $user_id;
                    $this->LogCall->create();
                    $this->LogCall->save($this->request->data['LogCall']);
                    $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                    $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                    if ($xml_error == 'TRUE') {
                        $Email = new CakeEmail();

                        $Email->viewVars(array(
                            'request_xml' => trim($xml_string),
                            'respon_message' => $log_call_status_message,
                            'respon_code' => $log_call_status_code,
                        ));

                        $to = 'biswajit@wtbglobal.com';
                        $cc = 'infra@sumanus.com';

                        $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                    }
                    /*                     * *
                     * City mapping table
                     * 
                     */
                    $xml_error = 'FALSE';

                    $arrs = $this->TravelCitySupplier->find('all', array('conditions' => array('TravelCitySupplier.city_id' => $CityId)));
                    if (count($arrs) > 0) {
                        foreach ($arrs as $val) {
                            $Id = $val['TravelCitySupplier']['id'];
                            $this->request->data['TravelCitySupplier']['city_name'] = "'" . $CityName . "'";
                            $this->request->data['TravelCitySupplier']['city_country_id'] = "'" . $CountryId . "'";
                            $this->request->data['TravelCitySupplier']['city_country_name'] = "'" . $CountryName . "'";
                            $this->request->data['TravelCitySupplier']['city_continent_id'] = "'" . $ContinentId . "'";
                            $this->request->data['TravelCitySupplier']['city_continent_name'] = "'" . $ContinentName . "'";
                            $this->request->data['TravelCitySupplier']['province_id'] = "'" . $ProvinceId . "'";
                            $this->request->data['TravelCitySupplier']['province_name'] = "'" . $ProvinceName . "'";
                            $this->request->data['TravelCitySupplier']['supplier_coutry_code'] = "'" . $CountryCode . "'";
                            $this->request->data['TravelCitySupplier']['pf_city_code'] = "'" . $CityCode . "'";
                            $CityMappingName = strtoupper('[SUPP/CITY] | ' . $val['TravelCitySupplier']['supplier_code'] . ' | ' . $CountryCode . ' | ' . $CityCode . ' - ' . $CityName);
                            $this->request->data['TravelCitySupplier']['city_mapping_name'] = "'" . $CityMappingName . "'";
                            $this->TravelCitySupplier->updateAll($this->request->data['TravelCitySupplier'], array('TravelCitySupplier.id' => $Id));


                            $country_code = $CountryCode;
                            $city_code = $CityCode;
                            $SupplierCode = $val['TravelCitySupplier']['supplier_code'];
                            $Active = strtolower($val['TravelCitySupplier']['active']);
                            $Excluded = strtolower($val['TravelCitySupplier']['excluded']);
                            $SupplierCountryCode = $val['TravelCitySupplier']['supplier_coutry_code'];
                            $SupplierCityCode = $val['TravelCitySupplier']['supplier_city_code'];
                            $CityContinentName = $ContinentName;
                            $CityContinentId = $ContinentId;
                            $CityCountryName = $CountryName;
                            $CityCountryId = $CountryId;

                            $CityId = $CityId;

                            $CitySupplierStatus = $val['TravelCitySupplier']['city_supplier_status'];
                            if ($CitySupplierStatus)
                                $CitySupplierStatus = 'true';
                            else
                                $CitySupplierStatus = 'false';
                            $ApprovedBy = $val['TravelCitySupplier']['approved_by'];
                            $CreatedBy = $val['TravelCitySupplier']['created_by'];
                            $app_date = explode(' ', $val['TravelCitySupplier']['approved_date']);
                            $ApprovedDate = $app_date[0] . 'T' . $app_date[1];
                            $date = explode(' ', $val['TravelCitySupplier']['created']);
                            $created = $date[0] . 'T' . $date[1];
                            $is_update = $val['TravelCitySupplier']['is_update'];


                            if ($is_update == 'Y') {
                                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_CityMapping</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="Update">
                                                                <Id>' . $Id . '</Id>
                                                                <CityCode><![CDATA[' . $CityCode . ']]></CityCode>
                                                                <CityName><![CDATA[' . $CityName . ']]></CityName>
                                                                <CityId>' . $CityId . '</CityId>                                
                                                                <SupplierCode><![CDATA[' . $SupplierCode . ']]></SupplierCode>
                                                                <SupplierCityCode><![CDATA[' . $SupplierCityCode . ']]></SupplierCityCode>
                                                                <PFCityCode><![CDATA[' . $city_code . ']]></PFCityCode>
                                                                <CityMappingName><![CDATA[' . $CityMappingName . ']]></CityMappingName>
                                                                <CityCountryCode><![CDATA[' . $country_code . ']]></CityCountryCode>
                                                                <CityCountryId>' . $CityCountryId . '</CityCountryId>
                                                                <CityCountryName><![CDATA[' . $CityCountryName . ']]></CityCountryName>
                                                                <CityContinentId>' . $CityContinentId . '</CityContinentId>
                                                                <CityContinentName><![CDATA[' . $CityContinentName . ']]></CityContinentName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName><![CDATA[' . $ProvinceName . ']]></ProvinceName>
                                                                <CitySupplierStatus>' . $CitySupplierStatus . '</CitySupplierStatus>
                                                                <SupplierCountryCode>' . $SupplierCountryCode . '</SupplierCountryCode>
                                                                <WtbStatus>false</WtbStatus>
                                                                <Active>' . $Active . '</Active>
                                                                <Excluded>' . $Excluded . '</Excluded>                             
                                                                <ApprovedBy>' . $ApprovedBy . '</ApprovedBy>
                                                                <ApprovedDate>' . $ApprovedDate . '</ApprovedDate>
                                                                <CreatedBy>' . $CreatedBy . '</CreatedBy>
                                                                <CreatedDate>' . $created . '</CreatedDate> 
                                                            </ResourceDetailsData>              
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';

                                $log_call_screen = 'Edit - City Mapping';
                                $RESOURCEDATA = 'RESOURCEDATA_CITYMAPPING';

                                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');

                                $client = new SoapClient(null, array(
                                    'location' => $location_URL,
                                    'uri' => '',
                                    'trace' => 1,
                                ));

                                try {
                                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);
//Get response from here
                                    $xml_arr = $this->xml2array($order_return);

                                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                                        $xml_msg = "Foreign record has been successfully created [Code:$log_call_status_code]";
                                        $this->TravelCitySupplier->updateAll(array('wtb_status' => "'1'", 'is_update' => "'Y'"), array('id' => $id));
                                    } else {

                                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                                        $xml_msg = "There was a problem with foreign record creation [Code:$log_call_status_code]";
                                        $this->TravelCitySupplier->updateAll(array('wtb_status' => "'2'"), array('id' => $id));
                                        $xml_error = 'TRUE';
                                    }
                                } catch (SoapFault $exception) {
                                    var_dump(get_class($exception));
                                    var_dump($exception);
                                }


                                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                                $this->request->data['LogCall']['log_call_by'] = $user_id;
                                $this->LogCall->create();
                                $this->LogCall->save($this->request->data['LogCall']);
                                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                                if ($xml_error == 'TRUE') {
                                    $Email = new CakeEmail();

                                    $Email->viewVars(array(
                                        'request_xml' => trim($xml_string),
                                        'respon_message' => $log_call_status_message,
                                        'respon_code' => $log_call_status_code,
                                    ));

                                    $to = 'biswajit@wtbglobal.com';
                                    $cc = 'infra@sumanus.com';

                                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                                }
                            }
                        }
                    }


                    $message = 'Local record has been successfully updated.<br />' . $xml_msg;
                    $this->Session->setFlash($message, 'success');
                } else {
                    $this->Session->setFlash('Unable to update City.', 'failure');
                }
                $this->redirect(array('action' => 'reports'));
            }
        }

        $TravelCitySuppliers = $this->TravelCitySupplier->find('all', array('conditions' => array('TravelCitySupplier.city_id' => $id)));
        //pr($TravelCitySuppliers);

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $TravelCities['TravelCity']['continent_id'], 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
        $this->set(compact('TravelCountries', 'TravelCitySuppliers'));

        $Provinces = $this->Province->find('list', array(
            'conditions' => array(
                'Province.continent_id' => $TravelCities['TravelCity']['continent_id'],
                'Province.country_id' => $TravelCities['TravelCity']['country_id'],
                'Province.status' => '1',
                'Province.wtb_status' => '1',
                'Province.active' => 'TRUE'
            ),
            'fields' => array('Province.id', 'Province.name'),
            'order' => 'Province.name ASC'
        ));

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents', 'Provinces'));


        $this->request->data = $TravelCities;
    }

    public function city_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
        foreach ($this->data['TravelCity']['check'] as $val) {

            $TravelCities = $this->TravelCity->findById($val);

            $CityId = $TravelCities['TravelCity']['id'];
            $CityName = $TravelCities['TravelCity']['city_name'];
            $CityCode = $TravelCities['TravelCity']['city_code'];
            $CountryId = $TravelCities['TravelCity']['country_id'];
            $CountryCode = $TravelCities['TravelCity']['country_code'];
            $CountryName = $TravelCities['TravelCity']['country_name'];
            $ContinentId = $TravelCities['TravelCity']['continent_id'];
            $ProvinceId = $TravelCities['TravelCity']['province_id'];
            $ProvinceName = $TravelCities['TravelCity']['province_name'];
            $ContinentName = $TravelCities['TravelCity']['continent_name'];
            $CityDescription = $TravelCities['TravelCity']['city_description'];
            $TopCity = $TravelCities['TravelCity']['top_city'];
            $Active = $TravelCities['TravelCity']['active'];
            $is_update = $TravelCities['TravelCity']['is_update'];
            $approved_by = $TravelCities['TravelCity']['approved_by'];
            $approved_date = $TravelCities['TravelCity']['approved_date'];
            $created_by = $TravelCities['TravelCity']['created_by'];
            $created = $TravelCities['TravelCity']['created'];
            $modified = $TravelCities['TravelCity']['modified'];
            $WtbStatus = $TravelCities['TravelCity']['wtb_status'];
            $CityStatus = $TravelCities['TravelCity']['city_status'];
            
            if ($this->Common->checkCityExistsCityMapping($CityId)) {
                $this->Session->setFlash($CityName.' city can not deleted, '.$CityName.' exists in city mapping table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsSuburb($id)) {
                $this->Session->setFlash($CityName.' city can not deleted, '.$CityName.' exists in suburb table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsArea($id)) {
                $this->Session->setFlash($CityName.' city can not deleted, '.$CityName.' exists in area table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }
            elseif($this->Common->checkCityExistsHotel($id)) {
                $this->Session->setFlash($CityName.' city can not deleted, '.$CityName.' exists in hotel table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'city_reports'));
            }

            $save[] = array('DeleteTravelCity' => array(
                    'id' => $CityId,
                    'city_name' => $CityName,
                    'city_code' => $CityCode,
                    'country_id' => $CountryId,
                    'country_code' => $CountryCode,
                    'country_name' => $CountryName,
                    'continent_id' => $ContinentId,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'continent_name' => $ContinentName,
                    'city_description' => $CityDescription,
                    'top_city' => $TopCity,
                    'active' => $Active,
                    'is_update' => $is_update,
                    'city_status' => $CityStatus,
                    'wtb_status' => $WtbStatus,
                    'created' => $created,
                    'modified' => $modified,
            ));


            if ($this->TravelCity->delete($val)) {
                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="City">
                                                      <CityId>' . $val . '</CityId>            
                                                  </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - City';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelCity']['log_id'] = $LogId;
            }
            $this->DeleteTravelCity->create();
            $this->DeleteTravelCity->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete city.', 'failure');
        }
        return $this->redirect(array('controller' => 'reports', 'action' => 'reports'));
    }

    public function hotel_mapping_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
        foreach ($this->data['TravelHotelRoomSupplier']['check'] as $val) {

            $TravelHotelRoomSuppliers = $this->TravelHotelRoomSupplier->findById($val);

            $HotelSupplierId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['id'];
            $country_code = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_code'];
            $hotel_code = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_code'];
            $city_code = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_code'];
            $SupplierCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_code'];
            $Active = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['active'];
            $Excluded = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['excluded'];
            $SupplierCountryCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_item_code4'];
            $SupplierCityCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_item_code3'];
            $SupplierHotelCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_item_code1'];
            $HotelName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_name'];
            $CityId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_id'];
            $CityName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_name'];
            $SuburbId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_suburb_id'];
            $SuburbName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_suburb_name'];
            $AreaId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_area_id'];
            $AreaName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_area_name'];
            $BrandId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_brand_id'];
            $BrandName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_brand_name'];
            $ChainId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_chain_id'];
            $ChainName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_chain_name'];
            $CountryId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_id'];
            $CountryName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_name'];
            $ContinentId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_continent_id'];
            $ContinentName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_continent_name'];
            $ApprovedBy = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['approved_by'];
            $CreatedBy = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['created_by'];
            $app_date = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['approved_date'];
            $ProvinceId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['province_id'];
            $ProvinceName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['province_name'];
            $is_update = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['is_update'];
            $WtbStatus = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['wtb_status'];
            $created = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['created'];
            $modified = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['modified'];

            $save[] = array('DeleteTravelHotelRoomSupplier' => array(
                    'id' => $HotelSupplierId,
                    'hotel_country_code' => $country_code,
                    'hotel_code' => $hotel_code,
                    'hotel_city_code' => $city_code,
                    'supplier_code' => $SupplierCode,
                    'active' => $Active,
                    'excluded' => $Excluded,
                    'supplier_item_code4' => $SupplierCountryCode,
                    'supplier_item_code3' => $SupplierCityCode,
                    'supplier_item_code1' => $SupplierHotelCode,
                    'hotel_name' => $HotelName,
                    'hotel_city_id' => $CityId,
                    'hotel_city_name' => $CityName,
                    'hotel_suburb_id' => $SuburbId,
                    'hotel_suburb_name' => $SuburbName,
                    'hotel_area_id' => $AreaId,
                    'hotel_area_name' => $AreaName,
                    'hotel_brand_id' => $BrandId,
                    'hotel_brand_name' => $BrandName,
                    'hotel_chain_id' => $ChainId,
                    'hotel_chain_name' => $ChainName,
                    'hotel_country_id' => $CountryId,
                    'hotel_country_name' => $CountryName,
                    'hotel_continent_id' => $ContinentId,
                    'hotel_continent_name' => $ContinentName,
                    'approved_by' => $ApprovedBy,
                    'created_by' => $CreatedBy,
                    'approved_date' => $app_date,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'is_update' => $is_update,
                    'wtb_status' => $WtbStatus,
                    'created' => $created,
                    'modified' => $modified,
            ));




            if ($this->TravelHotelRoomSupplier->delete($val)) {
                $this->Mappinge->deleteAll(array('Mappinge.hotel_supplier_id' => $val), false);
                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="HotelRoomSupplier">
                                                      <HotelRoomSupplierId>' . $val . '</HotelRoomSupplierId>
                                                  </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - Hotel Mapping';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelHotelRoomSupplier']['log_id'] = $LogId;
            }
            $this->DeleteTravelHotelRoomSupplier->create();
            $this->DeleteTravelHotelRoomSupplier->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete Hotel mapping.', 'failure');
        }

        return $this->redirect(array('controller' => 'reports', 'action' => 'hotel_mapping_list'));
    }

    public function city_mapping_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;

        foreach ($this->data['TravelCitySupplier']['check'] as $val) {

            $TravelCitySuppliers = $this->TravelCitySupplier->findById($val);

            $CitySupplierId = $TravelCitySuppliers['TravelCitySupplier']['id'];
            $country_code = $TravelCitySuppliers['TravelCitySupplier']['city_country_code'];
            $city_code = $TravelCitySuppliers['TravelCitySupplier']['pf_city_code'];
            $SupplierCode = $TravelCitySuppliers['TravelCitySupplier']['supplier_code'];
            $Active = $TravelCitySuppliers['TravelCitySupplier']['active'];
            $Excluded = $TravelCitySuppliers['TravelCitySupplier']['excluded'];
            $SupplierCountryCode = $TravelCitySuppliers['TravelCitySupplier']['supplier_coutry_code'];
            $SupplierCityCode = $TravelCitySuppliers['TravelCitySupplier']['supplier_city_code'];
            $CityContinentName = $TravelCitySuppliers['TravelCitySupplier']['city_continent_name'];
            $CityContinentId = $TravelCitySuppliers['TravelCitySupplier']['city_continent_id'];
            $CityCountryName = $TravelCitySuppliers['TravelCitySupplier']['city_country_name'];
            $CityCountryId = $TravelCitySuppliers['TravelCitySupplier']['city_country_id'];
            $CityMappingName = $TravelCitySuppliers['TravelCitySupplier']['city_mapping_name'];
            $CityName = $TravelCitySuppliers['TravelCitySupplier']['city_name'];
            $CityId = $TravelCitySuppliers['TravelCitySupplier']['city_id'];
            $CitySupplierStatus = $TravelCitySuppliers['TravelCitySupplier']['city_supplier_status'];
            $ApprovedBy = $TravelCitySuppliers['TravelCitySupplier']['approved_by'];
            $CreatedBy = $TravelCitySuppliers['TravelCitySupplier']['created_by'];
            $ProvinceId = $TravelCitySuppliers['TravelCitySupplier']['province_id'];
            $ProvinceName = $TravelCitySuppliers['TravelCitySupplier']['province_name'];
            $app_date = $TravelCitySuppliers['TravelCitySupplier']['approved_date'];
            $created = $TravelCitySuppliers['TravelCitySupplier']['created'];
            $modified = $TravelCitySuppliers['TravelCitySupplier']['modified'];
            $is_update = $TravelCitySuppliers['TravelCitySupplier']['is_update'];
            $WtbStatus = $TravelCitySuppliers['TravelCitySupplier']['wtb_status'];

            $save[] = array('DeleteTravelCitySupplier' => array(
                    'id' => $CitySupplierId,
                    'city_country_code' => $country_code,
                    'pf_city_code' => $city_code,
                    'supplier_code' => $SupplierCode,
                    'active' => $Active,
                    'excluded' => $Excluded,
                    'supplier_coutry_code' => $SupplierCountryCode,
                    'supplier_city_code' => $SupplierCityCode,
                    'city_continent_name' => $CityContinentName,
                    'city_continent_id' => $CityContinentId,
                    'city_country_name' => $CityCountryName,
                    'city_country_id' => $CityCountryId,
                    'city_mapping_name' => $CityMappingName,
                    'city_name' => $CityName,
                    'city_id' => $CityId,
                    'city_supplier_status' => $CitySupplierStatus,
                    'approved_by' => $ApprovedBy,
                    'approved_date' => $app_date,
                    'created_by' => $CreatedBy,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'is_update' => $is_update,
                    'wtb_status' => $WtbStatus,
                    'created' => $created,
                    'modified' => $modified,
            ));



            if ($this->TravelCitySupplier->delete($val)) {
                $this->Mappinge->deleteAll(array('Mappinge.city_supplier_id' => $val), false);
                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="CitySupplier">
                                                        <CitySupplierId>' . $val . '</CitySupplierId>            
                                                    </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - City Mapping';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelCitySupplier']['log_id'] = $LogId;
            }
            $this->DeleteTravelCitySupplier->create();
            $this->DeleteTravelCitySupplier->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete City mapping.', 'failure');
        }

        return $this->redirect(array('controller' => 'reports', 'action' => 'city_mapping_list'));
    }

    public function suburb_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
        foreach ($this->data['TravelSuburb']['check'] as $val) {

            $TravelSuburbs = $this->TravelSuburb->findById($val);

            $SuburbId = $TravelSuburbs['TravelSuburb']['id'];
            $SuburbName = $TravelSuburbs['TravelSuburb']['name'];
            $CityId = $TravelSuburbs['TravelSuburb']['city_id'];
            $CityName = $TravelSuburbs['TravelSuburb']['city_name'];
            $CountryId = $TravelSuburbs['TravelSuburb']['country_id'];
            $CountryName = $TravelSuburbs['TravelSuburb']['country_name'];
            $ContinentId = $TravelSuburbs['TravelSuburb']['continent_id'];
            $ContinentName = $TravelSuburbs['TravelSuburb']['continent_name'];
            $SuburbDescription = $TravelSuburbs['TravelSuburb']['description'];
            $Active = $TravelSuburbs['TravelSuburb']['active'];
            $TopNeighborhood = $TravelSuburbs['TravelSuburb']['top_neighborhood'];
            $SuburbStatus = $TravelSuburbs['TravelSuburb']['status'];
            $ProvinceId = $TravelSuburbs['TravelSuburb']['province_id'];
            $ProvinceName = $TravelSuburbs['TravelSuburb']['province_name'];
            $WtbStatus = $TravelSuburbs['TravelSuburb']['wtb_status'];
            $is_update = $TravelSuburbs['TravelSuburb']['is_update'];
            $created_by = $TravelSuburbs['TravelSuburb']['created_by'];
            $created = $TravelSuburbs['TravelSuburb']['created'];
            $modified = $TravelSuburbs['TravelSuburb']['modified'];
            $approved_by = $TravelSuburbs['TravelSuburb']['approved_by'];
            $approved_date = $TravelSuburbs['TravelSuburb']['approved_date'];
            
            if ($this->Common->checkSuburbExistsHotel($SuburbId)) {
                $this->Session->setFlash($SuburbName.' suburb can not deleted, '.$SuburbName.' exists in hotel table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'suburb_list'));
            }
            elseif($this->Common->checkSuburbExistsArea($SuburbId)) {
                $this->Session->setFlash($SuburbName.' suburb can not deleted, '.$SuburbName.' exists in area table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'suburb_list'));
            }


            $save[] = array('DeleteTravelSuburb' => array(
                    'id' => $SuburbId,
                    'name' => $SuburbName,
                    'city_id' => $CityId,
                    'city_name' => $CityName,
                    'country_id' => $CountryId,
                    'country_name' => $CountryName,
                    'continent_id' => $ContinentId,
                    'continent_name' => $ContinentName,
                    'description' => $SuburbDescription,
                    'active' => $Active,
                    'top_neighborhood' => $TopNeighborhood,
                    'status' => $SuburbStatus,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'is_update' => $is_update,
                    'wtb_status' => $WtbStatus,
                    'approved_date' => $approved_date,
                    'approved_by' => $approved_by,
                    'created_by' => $created_by,
                    'created' => $created,
                    'modified' => $modified,
            ));


            if ($this->TravelSuburb->delete($val)) {

                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="Suburb">
                                                        <SuburbId>' . $val . '</SuburbId>            
                                                    </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - Suburb';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelSuburb']['log_id'] = $LogId;
            }
            $this->DeleteTravelSuburb->create();
            $this->DeleteTravelSuburb->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete suburb.', 'failure');
        }

        return $this->redirect(array('controller' => 'reports', 'action' => 'suburb_list'));
    }

    public function area_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
       
        foreach ($this->data['TravelArea']['check'] as $val) {

            $TravelAreas = $this->TravelArea->findById($val);

            $AreaId = $TravelAreas['TravelArea']['id'];
           
            $AreaName = $TravelAreas['TravelArea']['area_name'];
            $SuburbId = $TravelAreas['TravelArea']['suburb_id'];
            $SuburbName = $TravelAreas['TravelArea']['suburb_name'];
            $CityId = $TravelAreas['TravelArea']['city_id'];
            $CityName = $TravelAreas['TravelArea']['city_name'];
            $CountryId = $TravelAreas['TravelArea']['country_id'];
            $CountryName = $TravelAreas['TravelArea']['country_name'];
            $ContinentId = $TravelAreas['TravelArea']['continent_id'];
            $ContinentName = $TravelAreas['TravelArea']['continent_name'];
            $AreaDescription = $TravelAreas['TravelArea']['area_description'];
            $TopArea = $TravelAreas['TravelArea']['top_area'];
            $ProvinceId = $TravelAreas['TravelArea']['province_id'];
            $ProvinceName = $TravelAreas['TravelArea']['province_name'];
            $is_update = $TravelAreas['TravelArea']['is_update'];
            $AreaStatus = $TravelAreas['TravelArea']['area_status'];
            $WtbStatus = $TravelAreas['TravelArea']['wtb_status'];
            $Active = $TravelAreas['TravelArea']['area_active'];
            $approved_by = $TravelAreas['TravelArea']['approved_by'];
            $approved_date = $TravelAreas['TravelArea']['approved_date'];
            $created_by = $TravelAreas['TravelArea']['created_by'];
            $created = $TravelAreas['TravelArea']['created'];
            $modified = $TravelAreas['TravelArea']['modified'];

           
            
            if ($this->Common->checkAreaExistsHotel($AreaId)) {
                $this->Session->setFlash($AreaName.' area not deleted, '.$AreaName.' exists in hotel table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'area_list'));
            }
            
            
            //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
        //debug($log);
        //die;

            $save[] = array('DeleteTravelArea' => array(
                    'id' => $AreaId,
                    'area_name' => $AreaName,
                    'suburb_id' => $SuburbId,
                    'suburb_name' => $SuburbName,
                    'city_id' => $CityId,
                    'city_name' => $CityName,
                    'country_id' => $CountryId,
                    'country_name' => $CountryName,
                    'continent_id' => $ContinentId,
                    'continent_name' => $ContinentName,
                    'area_description' => $SuburbDescription,
                    'active' => $Active,
                    'top_area' => $TopArea,
                    'area_status' => $AreaStatus,
                    'province_id' => $ProvinceId,
                    'province_name' => $ProvinceName,
                    'is_update' => $is_update,
                    'wtb_status' => $WtbStatus,
                    'approved_date' => $approved_date,
                    'approved_by' => $approved_by,
                    'created_by' => $created_by,
                    'created' => $created,
                    'modified' => $modified,
            ));

            

            if ($this->TravelArea->delete($val)) {
              
                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                    <ResourceDetailsData srno="1" lookuptype="Area">
                                                        <AreaId>' . $val . '</AreaId>            
                                                    </ResourceDetailsData>
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - Area';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteTravelArea']['log_id'] = $LogId;
            }
            $this->DeleteTravelArea->create();
            $this->DeleteTravelArea->saveMany($save);

            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete area.', 'failure');
        }
        return $this->redirect(array('controller' => 'reports', 'action' => 'area_list'));
    }

    public function hotel_mapping_list() {

        $dummy_status = $this->Auth->user('dummy_status');
        $role_id = $this->Session->read("role_id");
        $search_condition = array();


        $search = '';
        $supplier_code = '';
        $country_wtb_code = '';
        $city_wtb_code = '';
        $hotel_wtb_code = '';
        $status = '';
        $active = '';
        $exclude = '';
        $mapping_type = '';
        $wtb_status = '';
        $province_id = '';
        $TravelCities = array();
        $TravelHotelLookups = array();
        $Provinces = array();


        if ($this->request->is('post') || $this->request->is('put')) {

            if (!empty($this->data['TravelHotelRoomSupplier']['search'])) {
                $search = $this->data['TravelHotelRoomSupplier']['search'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_mapping_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%"));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['active'])) {
                $active = $this->data['TravelHotelRoomSupplier']['active'];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.active' => $active, 'TravelHotelRoomSupplier.active' => $active, 'TravelHotelRoomSupplier.active' => $active)));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['wtb_status'])) {
                $wtb_status = $this->data['TravelHotelRoomSupplier']['wtb_status'];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.wtb_status' => $wtb_status, 'TravelHotelRoomSupplier.wtb_status' => $wtb_status, 'TravelHotelRoomSupplier.wtb_status' => $wtb_status)));
            }

            if (!empty($this->data['TravelHotelRoomSupplier']['supplier_code'])) {
                $supplier_code = $this->data['TravelHotelRoomSupplier']['supplier_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }

            if (!empty($this->data['TravelHotelRoomSupplier']['hotel_country_code'])) {
                $country_wtb_code = $this->data['TravelHotelRoomSupplier']['hotel_country_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'city_code, city_name', 'conditions' => array('TravelCity.country_code LIKE ' => '%' . trim($this->data['TravelHotelRoomSupplier']['hotel_country_code']) . '%',
                        'TravelCity.city_status' => '1',
                        'TravelCity.wtb_status' => '1',
                        'TravelCity.active' => 'TRUE'), 'order' => 'city_name ASC'));

                // $TravelCities = $this->TravelCity->find('list', array('fields' => 'city_code, city_name', 'conditions' => array('country_code LIKE ' => '%' . trim($this->data['TravelHotelRoomSupplier']['country_wtb_code']) . '%', 'city_status' => '0'), 'order' => 'city_name ASC'));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['hotel_city_code'])) {
                $city_wtb_code = $this->data['TravelHotelRoomSupplier']['hotel_city_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_city_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($city_wtb_code))) . "%"));
                $TravelHotelLookups = $this->TravelHotelLookup->find('list', array('fields' => 'hotel_code, hotel_name', 'conditions' => array('city_code LIKE' => '%' . trim($this->data['TravelHotelRoomSupplier']['hotel_city_code']) . '%', 'active' => 'TRUE'), 'order' => 'hotel_name ASC'));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['hotel_code'])) {
                $hotel_wtb_code = $this->data['TravelHotelRoomSupplier']['hotel_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_wtb_code))) . "%"));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['province_id'])) {
                $province_id = $this->data['TravelHotelRoomSupplier']['province_id'];
                array_push($search_condition, array('OR' => array('TravelHotelRoomSupplier.province_id' => $province_id, 'TravelHotelRoomSupplier.province_id' => $province_id)));
            }

            if (!empty($this->data['TravelHotelRoomSupplier']['status'])) {
                $status = $this->data['TravelHotelRoomSupplier']['status'];
                array_push($search_condition, array('TravelHotelRoomSupplier.status' => $status));
            }
            if (!empty($this->data['TravelHotelRoomSupplier']['exclude'])) {
                $exclude = $this->data['TravelHotelRoomSupplier']['exclude'];
                array_push($search_condition, array('TravelHotelRoomSupplier.exclude' => $exclude));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['search '])) {
                $search = $this->request->params['named']['search '];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.country_name LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%", 'TravelHotelRoomSupplier.city_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%", 'TravelHotelRoomSupplier.hotel_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%")));
            }
            if (!empty($this->request->params['named']['active '])) {
                $active = $this->request->params['named']['active '];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.active' => $active, 'TravelHotelRoomSupplier.active' => $active, 'TravelHotelRoomSupplier.active' => $active)));
            }
            if (!empty($this->request->params['named']['province_id '])) {
                $province_id = $this->request->params['named']['province_id '];
                array_push($search_condition, array('OR' => array('TravelHotelRoomSupplier.province_id' => $province_id, 'TravelHotelRoomSupplier.province_id' => $province_id)));
            }
            if (!empty($this->request->params['named']['wtb_status '])) {
                $wtb_status = $this->request->params['named']['wtb_status '];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.wtb_status' => $wtb_status, 'TravelHotelRoomSupplier.wtb_status' => $wtb_status, 'TravelHotelRoomSupplier.wtb_status' => $wtb_status)));
            }

            if (!empty($this->request->params['named']['supplier_code'])) {
                $supplier_code = $this->request->params['named']['supplier_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }
            if (!empty($this->request->params['named']['hotel_country_code'])) {
                $country_wtb_code = $this->request->params['named']['hotel_country_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'city_code, city_name', 'conditions' => array('TravelCity.country_code LIKE ' => '%' . trim($country_wtb_code) . '%',
                        'TravelCity.city_status' => '1',
                        'TravelCity.wtb_status' => '1',
                        'TravelCity.active' => 'TRUE'), 'order' => 'city_name ASC'));
            }
            if (!empty($this->request->params['named']['hotel_city_code'])) {
                $city_wtb_code = $this->request->params['named']['hotel_city_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_city_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($city_wtb_code))) . "%"));
                $TravelHotelLookups = $this->TravelHotelLookup->find('list', array('fields' => 'hotel_code, hotel_name', 'conditions' => array('city_code LIKE' => '%' . trim($this->request->params['named']['hotel_city_code']) . '%', 'active' => 'TRUE'), 'order' => 'hotel_name ASC'));
            }
            if (!empty($this->request->params['named']['hotel_code'])) {
                $hotel_wtb_code = $this->request->params['named']['hotel_code'];
                array_push($search_condition, array('TravelHotelRoomSupplier.hotel_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($hotel_wtb_code))) . "%"));
            }
            if (!empty($this->request->params['named']['status'])) {
                $status = $this->request->params['named']['status'];
                array_push($search_condition, array('TravelHotelRoomSupplier.status' => $status));
            }
            if (!empty($this->request->params['named']['exclude'])) {
                $exclude = $this->request->params['named']['exclude'];
                array_push($search_condition, array('TravelHotelRoomSupplier.exclude' => $exclude));
            }
        }

        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelHotelRoomSupplier.' . $key => $value));
                //$conHotelArry = array('TravelHotelLookup.'.$key => $value);
                //$conSuburbArry = array('TravelSuburb.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelHotelRoomSupplier.' . $key => $value));
                //$conHotelArry = array('TravelHotelLookup.'.$key => $value);
                //$conSuburbArry = array('TravelSuburb.'.$key => $value);
            }
        }

        $this->paginate['order'] = array('TravelHotelRoomSupplier.hotel_city_id' => 'asc');
        $this->set('TravelHotelRoomSuppliers', $this->paginate("TravelHotelRoomSupplier", $search_condition));

        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'supplier_code, supplier_name', 'conditions' => array('active' => 'TRUE'), 'order' => 'supplier_name ASC'));
        $this->set(compact('TravelSuppliers'));

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array(
                'TravelCountry.country_status' => '1',
                'TravelCountry.wtb_status' => '1',
                'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array('country_status' => '1'), 'order' => 'country_name ASC'));
        $this->set(compact('TravelCountries'));


        $this->set(compact('TravelCities'));


        $this->set(compact('TravelHotelLookups'));


        if (!isset($this->passedArgs['search']) && empty($this->passedArgs['search'])) {
            $this->passedArgs['search'] = (isset($this->data['TravelHotelRoomSupplier']['search'])) ? $this->data['TravelHotelRoomSupplier']['search'] : '';
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
            $this->passedArgs['active'] = (isset($this->data['TravelHotelRoomSupplier']['active'])) ? $this->data['TravelHotelRoomSupplier']['active'] : '';
        }
        if (!isset($this->passedArgs['wtb_status']) && empty($this->passedArgs['wtb_status'])) {
            $this->passedArgs['wtb_status'] = (isset($this->data['TravelHotelRoomSupplier']['wtb_status'])) ? $this->data['TravelHotelRoomSupplier']['wtb_status'] : '';
        }
        if (!isset($this->passedArgs['supplier_code']) && empty($this->passedArgs['supplier_code'])) {
            $this->passedArgs['supplier_code'] = (isset($this->data['TravelHotelRoomSupplier']['supplier_code'])) ? $this->data['TravelHotelRoomSupplier']['supplier_code'] : '';
        }
        if (!isset($this->passedArgs['hotel_country_code']) && empty($this->passedArgs['hotel_country_code'])) {
            $this->passedArgs['hotel_country_code'] = (isset($this->data['TravelHotelRoomSupplier']['hotel_country_code'])) ? $this->data['TravelHotelRoomSupplier']['hotel_country_code'] : '';
        }
        if (!isset($this->passedArgs['hotel_city_code']) && empty($this->passedArgs['hotel_city_code'])) {
            $this->passedArgs['hotel_city_code'] = (isset($this->data['TravelHotelRoomSupplier']['hotel_city_code'])) ? $this->data['TravelHotelRoomSupplier']['hotel_city_code'] : '';
        }
        if (!isset($this->passedArgs['hotel_code']) && empty($this->passedArgs['hotel_code'])) {
            $this->passedArgs['hotel_code'] = (isset($this->data['TravelHotelRoomSupplier']['hotel_code'])) ? $this->data['TravelHotelRoomSupplier']['hotel_code'] : '';
        }
        if (!isset($this->passedArgs['status']) && empty($this->passedArgs['status'])) {
            $this->passedArgs['status'] = (isset($this->data['TravelHotelRoomSupplier']['status'])) ? $this->data['TravelHotelRoomSupplier']['status'] : '';
        }
        if (!isset($this->passedArgs['exclude']) && empty($this->passedArgs['exclude'])) {
            $this->passedArgs['exclude'] = (isset($this->data['TravelHotelRoomSupplier']['exclude'])) ? $this->data['TravelHotelRoomSupplier']['exclude'] : '';
        }
        if (!isset($this->passedArgs['province_id']) && empty($this->passedArgs['province_id'])) {
            $this->passedArgs['province_id'] = (isset($this->data['TravelHotelRoomSupplier']['province_id'])) ? $this->data['TravelHotelRoomSupplier']['province_id'] : '';
        }



        if (!isset($this->data) && empty($this->data)) {
            $this->data['TravelHotelRoomSupplier']['search'] = $this->passedArgs['search'];
            $this->data['TravelHotelRoomSupplier']['active'] = $this->passedArgs['active'];
            $this->data['TravelHotelRoomSupplier']['wtb_status'] = $this->passedArgs['wtb_status'];
            $this->data['TravelHotelRoomSupplier']['supplier_code'] = $this->passedArgs['supplier_code'];
            $this->data['TravelHotelRoomSupplier']['hotel_country_code'] = $this->passedArgs['hotel_country_code'];
            $this->data['TravelHotelRoomSupplier']['hotel_city_code'] = $this->passedArgs['hotel_city_code'];
            $this->data['TravelHotelRoomSupplier']['hotel_code'] = $this->passedArgs['hotel_code'];
            $this->data['TravelHotelRoomSupplier']['status'] = $this->passedArgs['status'];
            $this->data['TravelHotelRoomSupplier']['exclude'] = $this->passedArgs['exclude'];
            $this->data['TravelHotelRoomSupplier']['province_id'] = $this->passedArgs['province_id'];
        }
        $TravelActionItemTypes = $this->TravelActionItemType->find('list', array('fields' => 'id, value', 'order' => 'value ASC'));

        $this->set(compact('search', 'TravelActionItemTypes', 'supplier_code', 'country_wtb_code', 'wtb_status', 'city_wtb_code', 'active', 'hotel_wtb_code', 'status', 'exclude', 'TravelMappingTypes', 'mapping_type', 'province_id', 'Provinces'));
    }

    public function city_mapping_list() {

        $dummy_status = $this->Auth->user('dummy_status');
        $role_id = $this->Session->read("role_id");
        $search_condition = array();
        $search = '';
        $supplier_code = '';
        $country_wtb_code = '';
        $city_wtb_code = '';
        $hotel_wtb_code = '';
        $status = '';
        $active = '';
        $exclude = '';
        $mapping_type = '';
        $wtb_status = '';
        $province_id = '';
        $TravelCities = array();
        $TravelHotelLookups = array();
        $Provinces = array();


        if ($this->request->is('post') || $this->request->is('put')) {

            if (!empty($this->data['TravelCitySupplier']['search'])) {
                $search = $this->data['TravelCitySupplier']['search'];
                array_push($search_condition, array('TravelCitySupplier.city_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%"));
            }
            if (!empty($this->data['TravelCitySupplier']['active'])) {
                $active = $this->data['TravelCitySupplier']['active'];
                array_push($search_condition, array('TravelCitySupplier.active' => $active));
            }
            if (!empty($this->data['TravelCitySupplier']['wtb_status'])) {
                $wtb_status = $this->data['TravelCitySupplier']['wtb_status'];
                array_push($search_condition, array('TravelCitySupplier.wtb_status' => $wtb_status));
            }

            if (!empty($this->data['TravelCitySupplier']['supplier_code'])) {
                $supplier_code = $this->data['TravelCitySupplier']['supplier_code'];
                array_push($search_condition, array('TravelCitySupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }

            if (!empty($this->data['TravelCitySupplier']['city_country_code'])) {
                $country_wtb_code = $this->data['TravelCitySupplier']['city_country_code'];
                array_push($search_condition, array('TravelCitySupplier.city_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'city_code, city_name', 'conditions' => array('TravelCity.country_code LIKE ' => '%' . trim($this->data['TravelCitySupplier']['city_country_code']) . '%',
                        'TravelCity.city_status' => '1',
                        'TravelCity.wtb_status' => '1',
                        'TravelCity.active' => 'TRUE'), 'order' => 'city_name ASC'));
            }
            if (!empty($this->data['TravelCitySupplier']['city_wtb_code'])) {
                $city_wtb_code = $this->data['TravelCitySupplier']['city_wtb_code'];
                array_push($search_condition, array('TravelCitySupplier.city_wtb_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($city_wtb_code))) . "%"));
            }

            if (!empty($this->data['TravelCitySupplier']['province_id'])) {
                $province_id = $this->data['TravelCitySupplier']['province_id'];
                array_push($search_condition, array('OR' => array('TravelCitySupplier.province_id' => $province_id)));
            }

            if (!empty($this->data['TravelCitySupplier']['city_supplier_status'])) {
                $status = $this->data['TravelCitySupplier']['city_supplier_status'];
                array_push($search_condition, array('TravelCitySupplier.city_supplier_status' => $status));
            }
            if (!empty($this->data['TravelCitySupplier']['exclude'])) {
                $exclude = $this->data['TravelCitySupplier']['exclude'];
                array_push($search_condition, array('TravelCitySupplier.exclude' => $exclude));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['search '])) {
                $search = $this->request->params['named']['search '];
                array_push($search_condition, array('TravelCitySupplier.city_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%"));
            }
            if (!empty($this->request->params['named']['active '])) {
                $active = $this->request->params['named']['active '];
                array_push($search_condition, array('TravelCitySupplier.active' => $active));
            }
            if (!empty($this->request->params['named']['province_id '])) {
                $province_id = $this->request->params['named']['province_id '];
                array_push($search_condition, array('OR' => array('TravelCitySupplier.province_id' => $province_id)));
            }
            if (!empty($this->request->params['named']['wtb_status '])) {
                $wtb_status = $this->request->params['named']['wtb_status '];
                array_push($search_condition, array('TravelCitySupplier.wtb_status' => $wtb_status));
            }

            if (!empty($this->request->params['named']['supplier_code'])) {
                $supplier_code = $this->request->params['named']['supplier_code'];
                array_push($search_condition, array('TravelCitySupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }

            if (!empty($this->request->params['named']['city_country_code'])) {
                $country_wtb_code = $this->request->params['named']['city_country_code'];
                array_push($search_condition, array('TravelCitySupplier.city_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'city_code, city_name', 'conditions' => array('TravelCity.country_code LIKE ' => '%' . trim($country_wtb_code) . '%',
                        'TravelCity.city_status' => '1',
                        'TravelCity.wtb_status' => '1',
                        'TravelCity.active' => 'TRUE'), 'order' => 'city_name ASC'));
            }
            if (!empty($this->request->params['named']['city_wtb_code'])) {
                $city_wtb_code = $this->request->params['named']['city_wtb_code'];
                array_push($search_condition, array('TravelCitySupplier.city_wtb_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($city_wtb_code))) . "%"));
            }

            if (!empty($this->request->params['named']['city_supplier_status'])) {
                $status = $this->request->params['named']['city_supplier_status'];
                array_push($search_condition, array('TravelCitySupplier.city_supplier_status' => $status));
            }
            if (!empty($this->request->params['named']['exclude'])) {
                $exclude = $this->request->params['named']['exclude'];
                array_push($search_condition, array('TravelCitySupplier.exclude' => $exclude));
            }
        }

        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCitySupplier.' . $key => $value));
                //$conArry = array('TravelHotelLookup.'.$key => $value);
                //$conAreaArry = array('TravelArea.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCitySupplier.' . $key => $value));
                //$conArry = array('TravelHotelLookup.'.$key => $value);
                //$conAreaArry = array('TravelArea.'.$key => $value);
            }
        }

        $this->paginate['order'] = array('TravelCitySupplier.city_id' => 'asc');
        $this->set('TravelCitySuppliers', $this->paginate("TravelCitySupplier", $search_condition));


        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'supplier_code, supplier_name', 'conditions' => array('active' => 'TRUE'), 'order' => 'supplier_name ASC'));
        $this->set(compact('TravelSuppliers'));

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array(
                'TravelCountry.country_status' => '1',
                'TravelCountry.wtb_status' => '1',
                'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array('country_status' => '1'), 'order' => 'country_name ASC'));
        $this->set(compact('TravelCountries'));


        $this->set(compact('TravelCities'));



        if (!isset($this->passedArgs['search']) && empty($this->passedArgs['search'])) {
            $this->passedArgs['search'] = (isset($this->data['TravelCitySupplier']['search'])) ? $this->data['TravelCitySupplier']['search'] : '';
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
            $this->passedArgs['active'] = (isset($this->data['TravelCitySupplier']['active'])) ? $this->data['TravelCitySupplier']['active'] : '';
        }
        if (!isset($this->passedArgs['wtb_status']) && empty($this->passedArgs['wtb_status'])) {
            $this->passedArgs['wtb_status'] = (isset($this->data['TravelCitySupplier']['wtb_status'])) ? $this->data['TravelCitySupplier']['wtb_status'] : '';
        }
        if (!isset($this->passedArgs['supplier_code']) && empty($this->passedArgs['supplier_code'])) {
            $this->passedArgs['supplier_code'] = (isset($this->data['TravelCitySupplier']['supplier_code'])) ? $this->data['TravelCitySupplier']['supplier_code'] : '';
        }
        if (!isset($this->passedArgs['city_country_code']) && empty($this->passedArgs['city_country_code'])) {
            $this->passedArgs['city_country_code'] = (isset($this->data['TravelCitySupplier']['city_country_code'])) ? $this->data['TravelCitySupplier']['city_country_code'] : '';
        }
        if (!isset($this->passedArgs['city_wtb_code']) && empty($this->passedArgs['city_wtb_code'])) {
            $this->passedArgs['city_wtb_code'] = (isset($this->data['TravelCitySupplier']['city_wtb_code'])) ? $this->data['TravelCitySupplier']['city_wtb_code'] : '';
        }
        if (!isset($this->passedArgs['city_supplier_status']) && empty($this->passedArgs['city_supplier_status'])) {
            $this->passedArgs['city_supplier_status'] = (isset($this->data['TravelCitySupplier']['city_supplier_status'])) ? $this->data['TravelCitySupplier']['city_supplier_status'] : '';
        }
        if (!isset($this->passedArgs['exclude']) && empty($this->passedArgs['exclude'])) {
            $this->passedArgs['exclude'] = (isset($this->data['TravelCitySupplier']['exclude'])) ? $this->data['TravelCitySupplier']['exclude'] : '';
        }
        if (!isset($this->passedArgs['province_id']) && empty($this->passedArgs['province_id'])) {
            $this->passedArgs['province_id'] = (isset($this->data['TravelCitySupplier']['province_id'])) ? $this->data['TravelCitySupplier']['province_id'] : '';
        }



        if (!isset($this->data) && empty($this->data)) {
            $this->data['TravelCitySupplier']['search'] = $this->passedArgs['search'];
            $this->data['TravelCitySupplier']['active'] = $this->passedArgs['active'];
            $this->data['TravelCitySupplier']['wtb_status'] = $this->passedArgs['wtb_status'];
            $this->data['TravelCitySupplier']['city_country_code'] = $this->passedArgs['city_country_code'];
            $this->data['TravelCitySupplier']['city_wtb_code'] = $this->passedArgs['city_wtb_code'];
            $this->data['TravelCitySupplier']['city_supplier_status'] = $this->passedArgs['city_supplier_status'];
            $this->data['TravelCitySupplier']['exclude'] = $this->passedArgs['exclude'];
            $this->data['TravelCitySupplier']['province_id'] = $this->passedArgs['province_id'];
        }
        $TravelActionItemTypes = $this->TravelActionItemType->find('list', array('fields' => 'id, value', 'order' => 'value ASC'));

        $this->set(compact('search', 'supplier_code', 'country_wtb_code', 'wtb_status', 'city_wtb_code', 'active', 'TravelActionItemTypes', 'status', 'exclude', 'TravelMappingTypes', 'mapping_type', 'province_id', 'Provinces'));
    }

    public function suburb_list() {

        $dummy_status = $this->Auth->user('dummy_status');
        $condition_dummy_status = array('dummy_status' => $dummy_status);
        $search_condition = array();
        $TravelCities = array();
        $TravelCountries = ARRAY();
        $conArry =array();
        $name = '';
        $city_id = $this->params['named']['city_id'];
        $country_id = $this->params['named']['country_id'];
        $continent_id = $this->params['named']['continent_id'];
        $province_id = $this->params['named']['province_id'];
        $top_neighborhood = '';
        $active = '';
        $status = '';
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'TravelCountry.id,TravelCountry.country_name', 'conditions' => array( 'TravelCountry.continent_id' => $continent_id,
                    ), 'order' => 'TravelCountry.country_name ASC'));
        
        $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.continent_id' => $continent_id,
                
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));
        $TravelCities = $this->TravelCity->find('list', array(
                'conditions' => array(
                    'TravelCity.province_id' => $province_id,                 
                ),
                'fields' => array('TravelCity.id','TravelCity.city_name'),
                'order' => 'TravelCity.city_name ASC'
            ));

        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->data['TravelSuburb']['name'])) {
                $name = $this->data['TravelSuburb']['name'];
                array_push($search_condition, array("TravelSuburb.name LIKE '%$name%'"));
            }
            if (!empty($this->data['TravelSuburb']['continent_id'])) {
                $continent_id = $this->data['TravelSuburb']['continent_id'];
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
                array_push($search_condition, array('TravelSuburb.continent_id' => $continent_id));
            }

            if (!empty($this->data['TravelSuburb']['country_id'])) {
                $country_id = $this->data['TravelSuburb']['country_id'];
                array_push($search_condition, array('TravelSuburb.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $country_id, 'city_status' => 1, 'active' => 'TRUE'), 'order' => 'city_name ASC'));
            }

            if (!empty($this->data['TravelSuburb']['city_id'])) {
                $city_id = $this->data['TravelSuburb']['city_id'];
                array_push($search_condition, array('TravelSuburb.city_id' => $city_id));
            }
            if (!empty($this->data['TravelSuburb']['top_neighborhood'])) {
                $top_neighborhood = $this->data['TravelSuburb']['top_neighborhood'];
                array_push($search_condition, array('TravelSuburb.top_neighborhood' => $top_neighborhood));
            }
            if (!empty($this->data['TravelSuburb']['active'])) {
                $active = $this->data['TravelSuburb']['active'];
                array_push($search_condition, array('TravelSuburb.active' => $active));
            }
            if (!empty($this->data['TravelSuburb']['status'])) {
                $status = $this->data['TravelSuburb']['status'];
                array_push($search_condition, array('TravelSuburb.status' => $status));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['name'])) {
                $name = $this->request->params['named']['name'];
                array_push($search_condition, array("TravelSuburb.name LIKE '%$name%'"));
            }
            if (!empty($this->request->params['named']['continent_id'])) {
                $continent_id = $this->request->params['named']['continent_id'];
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
                array_push($search_condition, array('TravelSuburb.continent_id' => $continent_id));
            }

            if (!empty($this->request->params['named']['country_id'])) {
                $country_id = $this->request->params['named']['country_id'];
                array_push($search_condition, array('TravelSuburb.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $country_id, 'city_status' => 0), 'order' => 'city_name ASC'));
            }

            if (!empty($this->request->params['named']['city_id'])) {
                $city_id = $this->request->params['named']['city_id'];
                array_push($search_condition, array('TravelSuburb.city_id' => $city_id));
            }
            if (!empty($this->request->params['TravelSuburb']['top_neighborhood'])) {
                $top_neighborhood = $this->request->params['TravelSuburb']['top_neighborhood'];
                array_push($search_condition, array('TravelSuburb.top_neighborhood' => $top_neighborhood));
            }
            if (!empty($this->request->params['TravelSuburb']['active'])) {
                $active = $this->request->params['TravelSuburb']['active'];
                array_push($search_condition, array('TravelSuburb.active' => $active));
            }
            if (!empty($this->request->params['TravelSuburb']['status'])) {
                $status = $this->request->params['TravelSuburb']['status'];
                array_push($search_condition, array('TravelSuburb.status' => $status));
            }
        }

        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelSuburb.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelSuburb.' . $key => $value));
                $conArry = array('TravelHotelLookup.'.$key => $value);
                $conAreaArry = array('TravelArea.'.$key => $value);
            }
        }


        $this->TravelSuburb->bindModel(array(
            'hasMany' => array(
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'suburb_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => $conArry  // 1 for client table of  lookup_value_activity_levelsv
                ),
                'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'suburb_id',
                    'fields' => 'TravelArea.id',
                    'conditions' => $conAreaArry  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));
        //pr($search_condition);
        //die;
        
        $TravelSuburbs = $this->TravelSuburb->find('all', array(
            'conditions' => $search_condition,
            'order' => 'name ASC'
        ));


        //$this->paginate['order'] = array('TravelSuburb.name' => 'asc');
        //$this->set('TravelSuburbs', $this->paginate("TravelSuburb", $search_condition));

        //$log = $this->TravelSuburb->getDataSource()->getLog(false, false);       
        //debug($log);
        //die;


        if (!isset($this->passedArgs['name']) && empty($this->passedArgs['name'])) {
            $this->passedArgs['name'] = (isset($this->data['TravelSuburb']['name'])) ? $this->data['TravelSuburb']['name'] : '';
        }
        if (!isset($this->passedArgs['continent_id']) && empty($this->passedArgs['continent_id'])) {
            $this->passedArgs['continent_id'] = (isset($this->data['TravelSuburb']['continent_id'])) ? $this->data['TravelSuburb']['continent_id'] : '';
        }
        if (!isset($this->passedArgs['country_id']) && empty($this->passedArgs['country_id'])) {
            $this->passedArgs['country_id'] = (isset($this->data['TravelSuburb']['country_id'])) ? $this->data['TravelSuburb']['country_id'] : '';
        }
        if (!isset($this->passedArgs['city_id']) && empty($this->passedArgs['city_id'])) {
            $this->passedArgs['city_id'] = (isset($this->data['TravelSuburb']['city_id'])) ? $this->data['TravelSuburb']['city_id'] : '';
        }
        if (!isset($this->passedArgs['top_neighborhood']) && empty($this->passedArgs['top_neighborhood'])) {
            $this->passedArgs['top_neighborhood'] = (isset($this->data['TravelSuburb']['top_neighborhood'])) ? $this->data['TravelSuburb']['top_neighborhood'] : '';
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
            $this->passedArgs['active'] = (isset($this->data['TravelSuburb']['active'])) ? $this->data['TravelSuburb']['active'] : '';
        }
        if (!isset($this->passedArgs['status']) && empty($this->passedArgs['status'])) {
            $this->passedArgs['status'] = (isset($this->data['TravelSuburb']['status'])) ? $this->data['TravelSuburb']['status'] : '';
        }
        if (!isset($this->data) && empty($this->data)) {

            $this->data['TravelSuburb']['name'] = $this->passedArgs['name'];
            $this->data['TravelSuburb']['continent_id'] = $this->passedArgs['continent_id'];
            $this->data['TravelSuburb']['country_id'] = $this->passedArgs['country_id'];
            $this->data['TravelSuburb']['active'] = $this->passedArgs['active'];
            $this->data['TravelSuburb']['city_id'] = $this->passedArgs['city_id'];
            $this->data['TravelSuburb']['top_neighborhood'] = $this->passedArgs['top_neighborhood'];
            $this->data['TravelSuburb']['status'] = $this->passedArgs['status'];
        }


        $this->set(compact('TravelCountries','Provinces','TravelSuburbs'));

        //$TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents'));
        $this->set(compact('TravelCities'));

        $this->set(compact('city_id'));
        $this->set(compact('continent_id','province_id'));
        $this->set(compact('country_id'));
        $this->set(compact('name'));
        $this->set(compact('active'));
        $this->set(compact('top_neighborhood'));
        $this->set(compact('status'));
    }

    public function area_list() {


        $search_condition = array();
        $TravelCities = array();
        $TravelSuburbs = array();
        $TravelCountries = array();
        $conHotelArry = array();
        $conSuburbArry = array();
        $area_name = '';
        $country_id = $this->params['named']['country_id'];
        $city_id = $this->params['named']['city_id'];
        $continent_id = $this->params['named']['continent_id'];
        $province_id = $this->params['named']['province_id'];
        $suburb_id = '';
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'TravelCountry.id,TravelCountry.country_name', 'conditions' => array( 'TravelCountry.continent_id' => $continent_id,
                    ), 'order' => 'TravelCountry.country_name ASC'));
        
        $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.continent_id' => $continent_id,
                    
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));
        
        $TravelCities = $this->TravelCity->find('list', array(
                'conditions' => array(
                    'TravelCity.province_id' => $province_id,
                    
                ),
                'fields' => array('TravelCity.id','TravelCity.city_name'),
                'order' => 'TravelCity.city_name ASC'
            ));

        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->data['TravelArea']['area_name'])) {
                $area_name = $this->data['TravelArea']['area_name'];
                array_push($search_condition, array("TravelArea.area_name LIKE '%$area_name%'"));
            }
            if (!empty($this->data['TravelArea']['continent_id'])) {
                $continent_id = $this->data['TravelArea']['continent_id'];
                array_push($search_condition, array('TravelArea.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
            }

            if (!empty($this->data['TravelArea']['country_id'])) {
                $country_id = $this->data['TravelArea']['country_id'];
                array_push($search_condition, array('TravelArea.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $country_id, 'city_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'city_name ASC'));
            }

            if (!empty($this->data['TravelArea']['city_id'])) {
                $city_id = $this->data['TravelArea']['city_id'];
                array_push($search_condition, array('TravelArea.city_id' => $city_id));
                $TravelSuburbs = $this->TravelSuburb->find('list', array('fields' => 'id,name', 'conditions' => array('status' => 1, 'country_id' => $country_id, 'city_id' => $city_id), 'order' => 'name ASC'));
            }
            if (!empty($this->data['TravelArea']['suburb_id'])) {
                $suburb_id = $this->data['TravelArea']['suburb_id'];
                array_push($search_condition, array('TravelArea.suburb_id' => $suburb_id));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['area_name'])) {
                $area_name = $this->request->params['named']['area_name'];
                array_push($search_condition, array("TravelArea.area_name LIKE '%$area_name%'"));
            }

            if (!empty($this->request->params['TravelArea']['continent_id'])) {
                $continent_id = $this->request->params['TravelArea']['continent_id'];
                array_push($search_condition, array('TravelArea.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
            }

            if (!empty($this->request->params['named']['country_id'])) {
                $country_id = $this->request->params['named']['country_id'];
                array_push($search_condition, array('TravelArea.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $country_id, 'city_status' => 0), 'order' => 'city_name ASC'));
            }

            if (!empty($this->request->params['named']['city_id'])) {
                $city_id = $this->request->params['named']['city_id'];
                array_push($search_condition, array('TravelArea.city_id' => $city_id));
                $TravelSuburbs = $this->TravelSuburb->find('list', array('fields' => 'id,name', 'conditions' => array('status' => 1, 'country_id' => $country_id, 'city_id' => $city_id), 'order' => 'name ASC'));
            }
            if (!empty($this->request->params['named']['suburb_id'])) {
                $suburb_id = $this->request->params['named']['suburb_id'];
                array_push($search_condition, array('TravelArea.suburb_id' => $suburb_id));
            }
        }

        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelArea.' . $key => $value));
                $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                $conSuburbArry = array('TravelSuburb.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelArea.' . $key => $value));
                $conHotelArry = array('TravelHotelLookup.'.$key => $value);
                $conSuburbArry = array('TravelSuburb.'.$key => $value);
            }
        }
        
        $this->TravelArea->bindModel(array(
            'hasMany' => array(
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'area_id',
                    'fields' => 'TravelHotelLookup.id',
                    'conditions' => $conHotelArry  // 1 for client table of  lookup_value_activity_levelsv
                ),
            ),
        ));
        

        $TravelAreas = $this->TravelArea->find('all', array(
            'conditions' => $search_condition,
            'order' => 'area_name ASC'
        ));
        
        

        //$this->paginate['order'] = array('TravelArea.name' => 'asc');
        //$this->set('TravelAreas', $this->paginate("TravelArea", $search_condition));





        if (!isset($this->passedArgs['area_name']) && empty($this->passedArgs['area_name'])) {
            $this->passedArgs['area_name'] = (isset($this->data['TravelArea']['area_name'])) ? $this->data['TravelArea']['area_name'] : '';
        }
        if (!isset($this->passedArgs['continent_id']) && empty($this->passedArgs['continent_id'])) {
            $this->passedArgs['continent_id'] = (isset($this->data['TravelArea']['continent_id'])) ? $this->data['TravelArea']['continent_id'] : '';
        }
        if (!isset($this->passedArgs['country_id']) && empty($this->passedArgs['country_id'])) {
            $this->passedArgs['country_id'] = (isset($this->data['TravelArea']['country_id'])) ? $this->data['TravelArea']['country_id'] : '';
        }
        if (!isset($this->passedArgs['city_id']) && empty($this->passedArgs['city_id'])) {
            $this->passedArgs['city_id'] = (isset($this->data['TravelArea']['city_id'])) ? $this->data['TravelArea']['city_id'] : '';
        }
        if (!isset($this->passedArgs['suburb_id']) && empty($this->passedArgs['suburb_id'])) {
            $this->passedArgs['suburb_id'] = (isset($this->data['TravelArea']['suburb_id'])) ? $this->data['TravelArea']['suburb_id'] : '';
        }
        if (!isset($this->data) && empty($this->data)) {

            $this->data['TravelArea']['area_name'] = $this->passedArgs['area_name'];
            $this->data['TravelArea']['continent_id'] = $this->passedArgs['continent_id'];
            $this->data['TravelArea']['country_id'] = $this->passedArgs['country_id'];
            $this->data['TravelArea']['city_id'] = $this->passedArgs['city_id'];
            $this->data['TravelArea']['suburb_id'] = $this->passedArgs['suburb_id'];
        }


        $this->set(compact('TravelCountries','TravelAreas'));
        //$TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents'));
        $this->set(compact('TravelSuburbs'));
        $this->set(compact('TravelCities'));

        $this->set(compact('city_id','province_id','Provinces'));
        $this->set(compact('continent_id'));
        $this->set(compact('country_id'));
        $this->set(compact('area_name'));
        $this->set(compact('suburb_id'));
    }
    
    public function country_list(){
        
        
        $search_condition = array();
        $active = '';
        $country_status = '';
        $wtb_status = '';
        $hotel_count ='';
        $city_mapping_count = '';
        $hotel_mapping_count = '';      
        $area_count = '';
        $suburb_count = '';
        $HotelCon = array();
        $HotelMappingCon = array();
        $CityMappingCon = array();
        $SuburbCon = array();
        $AreaCon = array();
        $CityCon = array();
        $CountryCon = array();
        $CountryMappingCon = array();
        $ProvinceCon = array();
        
          
        array_push($CountryMappingCon, array('TravelCountrySupplier.country_id' => 0));       
        array_push($ProvinceCon, array('Province.country_id' => 0));       
        array_push($HotelCon, array('TravelHotelLookup.country_id' => 0));       
        array_push($HotelMappingCon, array('TravelHotelRoomSupplier.hotel_country_id' => 0));
        array_push($CityCon, array('TravelCity.country_id' => 0));
        array_push($CityMappingCon, array('TravelCitySuppliers.city_country_id' => 0));
        array_push($SuburbCon, array('TravelSuburb.country_id' => 0));
        array_push($AreaCon, array('TravelArea.country_id' => 0));
        
        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCountry.' . $key => $value));
                
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCountry.' . $key => $value));
                
            }
        }
        
       
        $this->TravelCountry->bindModel(array(
            'hasMany' => array(
                'Province' => array(
                    'className' => 'Province',
                    'foreignKey' => 'country_id',
                    'fields' => 'Province.id',                    
                ),
                'TravelCity' => array(
                    'className' => 'TravelCity',
                    'foreignKey' => 'country_id',
                    'fields' => 'TravelCity.id',                    
                ),
                'TravelCitySupplier' => array(
                    'className' => 'TravelCitySupplier',
                    'foreignKey' => 'city_country_id',
                    'fields' => 'TravelCitySupplier.id',                    
                ),
                'TravelCountrySupplier' => array(
                    'className' => 'TravelCountrySupplier',
                    'foreignKey' => 'country_id',
                    'fields' => 'TravelCountrySupplier.id',                    
                ),
                'TravelHotelLookup' => array(
                    'className' => 'TravelHotelLookup',
                    'foreignKey' => 'country_id',
                    'fields' => 'TravelHotelLookup.id',                    
                ),
                'TravelHotelRoomSupplier' => array(
                    'className' => 'TravelHotelRoomSupplier',
                    'foreignKey' => 'hotel_country_id',
                    'fields' => 'TravelHotelRoomSupplier.id',                    
                ),
                'TravelSuburb' => array(
                    'className' => 'TravelSuburb',
                    'foreignKey' => 'country_id',
                    'fields' => 'TravelSuburb.id',                    
                ),
                 'TravelArea' => array(
                    'className' => 'TravelArea',
                    'foreignKey' => 'country_id',
                    'fields' => 'TravelArea.id',                    
                ),
            ),
        ));
      
        

        $TravelCountries = $this->TravelCountry->find('all', array(
            'conditions' => $search_condition,
            'order' => 'country_name ASC'
        ));
        //$log = $this->TravelCountry->getDataSource()->getLog(false, false);       
        //debug($log);
        
  
        $country_mapping_count = $this->TravelCountrySupplier->find('count',array('conditions' => $CountryMappingCon));
        $province_count = $this->Province->find('count',array('conditions' => $ProvinceCon));
         $hotel_count = $this->TravelHotelLookup->find('count',array('conditions' => $HotelCon));
         $city_count = $this->TravelCity->find('count',array('conditions' => $CityCon));
         $city_mapping_count = $this->TravelCitySuppliers->find('count',array('conditions' => $CityMappingCon));
         $hotel_mapping_count = $this->TravelHotelRoomSupplier->find('count',array('conditions' => $HotelMappingCon));
         $area_count = $this->TravelArea->find('count',array('conditions' => $AreaCon));
         $suburb_count = $this->TravelSuburb->find('count',array('conditions' => $SuburbCon));
         
        // pr($TravelCities);

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'order' => 'country_name ASC'));

        $this->set(compact('country_mapping_count','province_count','city_count','hotel_count','city_mapping_count','hotel_mapping_count',
                'area_count','suburb_count', 'TravelCountries','wtb_status','active','country_status'));
  
    }

    function city_mapping_edit($id = null) {

        //$this->layout = '';
        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $user_id = $this->Auth->user('id');
        $xml_msg = '';
        $xml_error = 'FALSE';

        if (!$id) {
            throw new NotFoundException(__('Invalid City Supplier'));
        }

        $TravelCitySuppliers = $this->TravelCitySupplier->findById($id);


        if (!$TravelCitySuppliers) {
            throw new NotFoundException(__('Invalid City Supplier'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $Id = $TravelCitySuppliers['TravelCitySupplier']['id'];
            $country_code = $TravelCitySuppliers['TravelCitySupplier']['city_country_code'];
            $city_code = $TravelCitySuppliers['TravelCitySupplier']['pf_city_code'];
            $SupplierCode = $TravelCitySuppliers['TravelCitySupplier']['supplier_code'];
            $Active = strtolower($this->data['TravelCitySupplier']['active']);
            $Excluded = strtolower($TravelCitySuppliers['TravelCitySupplier']['excluded']);
            $SupplierCountryCode = $TravelCitySuppliers['TravelCitySupplier']['supplier_coutry_code'];
            $SupplierCityCode = $this->data['TravelCitySupplier']['supplier_city_code'];
            $CityContinentName = $TravelCitySuppliers['TravelCitySupplier']['city_continent_name'];
            $CityContinentId = $TravelCitySuppliers['TravelCitySupplier']['city_continent_id'];
            $CityCountryName = $TravelCitySuppliers['TravelCitySupplier']['city_country_name'];
            $CityCountryId = $TravelCitySuppliers['TravelCitySupplier']['city_country_id'];
            $CityMappingName = $TravelCitySuppliers['TravelCitySupplier']['city_mapping_name'];
            $CityName = $TravelCitySuppliers['TravelCitySupplier']['city_name'];
            $CityId = $TravelCitySuppliers['TravelCitySupplier']['city_id'];
            $CitySupplierStatus = 'true';

            $ApprovedBy = $TravelCitySuppliers['TravelCitySupplier']['approved_by'];
            $CreatedBy = $TravelCitySuppliers['TravelCitySupplier']['created_by'];
            $ProvinceId = $this->data['TravelCitySupplier']['province_id'];
            $ProvinceName = $this->data['TravelCitySupplier']['province_name'];
            $app_date = explode(' ', $TravelCitySuppliers['TravelCitySupplier']['approved_date']);
            $ApprovedDate = $app_date[0] . 'T' . $app_date[1];
            $date = explode(' ', $TravelCitySuppliers['TravelCitySupplier']['created']);
            $created = $date[0] . 'T' . $date[1];
            $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
            $is_update = $TravelCitySuppliers['TravelCitySupplier']['is_update'];
            if ($is_update == 'Y')
                $city_actiontype = 'Update';
            else
                $city_actiontype = 'AddNew';

            $WtbStatus = $TravelCitySuppliers['TravelCitySupplier']['wtb_status'];
            if ($WtbStatus)
                $WtbStatus = 'true';
            else
                $WtbStatus = 'false';

            $this->data->request['TravelCitySupplier']['province_id'] = $ProvinceId;
            $this->data->request['TravelCitySupplier']['province_name'] = $ProvinceName;
            $this->data->request['TravelCitySupplier']['supplier_city_code'] = $SupplierCityCode;
            $this->TravelCitySupplier->id = $id;
            if ($this->TravelCitySupplier->save($this->request->data['TravelCitySupplier'])) {


                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_CityMapping</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="' . $city_actiontype . '">
                                                                <Id>' . $Id . '</Id>
                                                                <CityCode><![CDATA[' . $city_code . ']]></CityCode>
                                                                <CityName><![CDATA[' . $CityName . ']]></CityName>
                                                                <CityId>' . $CityId . '</CityId>                                
                                                                <SupplierCode><![CDATA[' . $SupplierCode . ']]></SupplierCode>
                                                                <SupplierCityCode><![CDATA[' . $SupplierCityCode . ']]></SupplierCityCode>
                                                                <PFCityCode><![CDATA[' . $city_code . ']]></PFCityCode>
                                                                <CityMappingName><![CDATA[' . $CityMappingName . ']]></CityMappingName>
                                                                <CityCountryCode><![CDATA[' . $country_code . ']]></CityCountryCode>
                                                                <CityCountryId>' . $CityCountryId . '</CityCountryId>
                                                                <CityCountryName><![CDATA[' . $CityCountryName . ']]></CityCountryName>
                                                                <CityContinentId>' . $CityContinentId . '</CityContinentId>
                                                                <CityContinentName><![CDATA[' . $CityContinentName . ']]></CityContinentName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName><![CDATA[' . $ProvinceName . ']]></ProvinceName>
                                                                <CitySupplierStatus>' . $CitySupplierStatus . '</CitySupplierStatus>
                                                                <SupplierCountryCode><![CDATA[' . $SupplierCountryCode . ']]></SupplierCountryCode>
                                                                <WtbStatus>false</WtbStatus>
                                                                <Active>' . $Active . '</Active>
                                                                <Excluded>' . $Excluded . '</Excluded>                             
                                                                <ApprovedBy>' . $ApprovedBy . '</ApprovedBy>
                                                                <ApprovedDate>' . $ApprovedDate . '</ApprovedDate>
                                                                <CreatedBy>' . $CreatedBy . '</CreatedBy>
                                                                <CreatedDate>' . $created . '</CreatedDate> 
                                                            </ResourceDetailsData>              
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';

                $log_call_screen = 'Edit - City Mapping';
                $RESOURCEDATA = 'RESOURCEDATA_CITYMAPPING';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');

                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);
//Get response from here
                    $xml_arr = $this->xml2array($order_return);

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully created [Code:$log_call_status_code]";
                        $this->TravelCitySupplier->updateAll(array('wtb_status' => "'1'", 'is_update' => "'Y'"), array('id' => $id));
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record creation [Code:$log_call_status_code]";
                        $this->TravelCitySupplier->updateAll(array('wtb_status' => "'2'"), array('id' => $id));
                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->save($this->request->data['LogCall']);
                $LogId = $this->LogCall->getLastInsertId();
                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                $message = $xml_msg;
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Log Id [' . $LogId . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $this->Session->setFlash($message, 'success');


                //$this->Session->setFlash('Your changes have been submitted. Waiting for approval at the moment...', 'success');
            }
            else
                $this->Session->setFlash('Unable to add Action item.', 'failure');

            $this->redirect(array('controller' => 'reports', 'action' => 'city_mapping_list/' . $CityId));
        }


        $TravelSuppliers = $this->TravelSupplier->find('all', array('fields' => 'supplier_code, supplier_name', 'conditions' => array('active' => 'TRUE'), 'order' => 'supplier_name ASC'));
        $TravelSuppliers = Set::combine($TravelSuppliers, '{n}.TravelSupplier.supplier_code', array('%s - %s', '{n}.TravelSupplier.supplier_code', '{n}.TravelSupplier.supplier_name'));
        $this->set(compact('TravelSuppliers'));

        $TravelCountries = $this->TravelCountry->find('all', array('fields' => 'country_code, country_name', 'conditions' => array('country_code' => $TravelCitySuppliers['TravelCitySupplier']['city_country_code']), 'order' => 'country_name ASC'));
        $TravelCountries = Set::combine($TravelCountries, '{n}.TravelCountry.country_code', array('%s - %s', '{n}.TravelCountry.country_code', '{n}.TravelCountry.country_name'));
        $this->set(compact('TravelCountries'));

        $Provinces = $this->Province->find('list', array(
            'conditions' => array(
                'Province.country_id' => $TravelCitySuppliers['TravelCitySupplier']['city_country_id'],
                'Province.continent_id' => $TravelCitySuppliers['TravelCitySupplier']['city_continent_id'],
                'Province.status' => '1',
                'Province.wtb_status' => '1',
                'Province.active' => 'TRUE',
            ),
            'fields' => array('Province.id', 'Province.name'),
            'order' => 'Province.name ASC'
        ));

        $TravelCities = $this->TravelCity->find('all', array('fields' => 'city_code, city_name', 'conditions' => array('city_code' => $TravelCitySuppliers['TravelCitySupplier']['pf_city_code']), 'order' => 'city_name ASC'));
        $TravelCities = Set::combine($TravelCities, '{n}.TravelCity.city_code', array('%s - %s', '{n}.TravelCity.city_code', '{n}.TravelCity.city_name'));
        $this->set(compact('TravelCities', 'Provinces'));


        $this->request->data = $TravelCitySuppliers;
    }

    function hotel_mapping_edit($id = null) {

        $user_id = $this->Auth->user('id');
        $role_id = $this->Session->read("role_id");
        $dummy_status = $this->Auth->user('dummy_status');
        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';

        if (!$id) {
            throw new NotFoundException(__('Invalid Hotel Supplier'));
        }

        $TravelHotelRoomSuppliers = $this->TravelHotelRoomSupplier->findById($id);


        if (!$TravelHotelRoomSuppliers) {
            throw new NotFoundException(__('Invalid Hotel Supplier'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $Id = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['id'];
            $country_code = trim($TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_code']);
            $hotel_code = trim($TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_code']);
            $city_code = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_code'];
            $SupplierCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_code'];
            $Active = strtolower($this->data['TravelHotelRoomSupplier']['active']);
            $Excluded = strtolower($this->data['TravelHotelRoomSupplier']['excluded']);
            $SupplierCountryCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_item_code4'];
            $SupplierCityCode = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['supplier_item_code3'];
            $SupplierHotelCode = $this->data['TravelHotelRoomSupplier']['supplier_item_code1'];
            $HotelName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_name'];
            $CityId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_id'];
            $CityName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_name'];
            $SuburbId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_suburb_id'];
            $SuburbName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_suburb_name'];
            $AreaId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_area_id'];
            $AreaName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_area_name'];
            $BrandId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_brand_id'];
            $BrandName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_brand_name'];
            $ChainId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_chain_id'];
            $ChainName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_chain_name'];
            $CountryId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_id'];
            $CountryName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_name'];
            $ContinentId = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_continent_id'];
            $ContinentName = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_continent_name'];
            $ApprovedBy = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['approved_by'];
            $CreatedBy = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['created_by'];
            $app_date = explode(' ', $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['approved_date']);
            $ApprovedDate = $app_date[0] . 'T' . $app_date[1];
            $ProvinceId = $this->data['TravelHotelRoomSupplier']['province_id'];
            $ProvinceName = $this->data['TravelHotelRoomSupplier']['province_name'];
            $date = explode(' ', $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['created']);
            $created = $date[0] . 'T' . $date[1];
            $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
            $is_update = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['is_update'];

            if ($is_update == 'Y')
                $hotel_actiontype = 'Update';
            else
                $hotel_actiontype = 'AddNew';

            $WtbStatus = $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['wtb_status'];
            if ($WtbStatus)
                $WtbStatus = 'true';
            else
                $WtbStatus = 'false';

            $this->request->data['TravelHotelRoomSupplier']['province_id'] = $this->data['TravelHotelRoomSupplier']['province_id'];
            $this->request->data['TravelHotelRoomSupplier']['province_name'] = $this->data['TravelHotelRoomSupplier']['province_name'];
            $this->request->data['TravelHotelRoomSupplier']['supplier_item_code1'] = $this->data['TravelHotelRoomSupplier']['supplier_item_code1'];

            $this->TravelHotelRoomSupplier->id = $id;
            if ($this->TravelHotelRoomSupplier->save($this->request->data['TravelHotelRoomSupplier'])) {

                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_HotelMapping</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="' . $hotel_actiontype . '">
                                                                <Id>' . $Id . '</Id>
                                                                <HotelCode><![CDATA[' . $hotel_code . ']]></HotelCode>
                                                                <HotelName><![CDATA[' . $HotelName . ']]></HotelName>
                                                                <SupplierCode><![CDATA[' . $SupplierCode . ']]></SupplierCode>
                                                                <WtbStatus>false</WtbStatus>
                                                                <Active>' . $Active . '</Active>
                                                                <Excluded>' . $Excluded . '</Excluded>
                                                                <ContinentId>' . $ContinentId . '</ContinentId>
                                                                <ContinentCode>NA</ContinentCode>
                                                                <ContinentName><![CDATA[' . $ContinentName . ']]></ContinentName>                              
                                                                <CountryId>' . $CountryId . '</CountryId>
                                                                <CountryCode><![CDATA[' . $country_code . ']]></CountryCode>
                                                                <CountryName><![CDATA[' . $CountryName . ']]></CountryName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName><![CDATA[' . $ProvinceName . ']]></ProvinceName>
                                                                <CityId>' . $CityId . '</CityId>
                                                                <CityCode><![CDATA[' . $city_code . ']]></CityCode>
                                                                <CityName><![CDATA[' . $CityName . ']]></CityName>
                                                                <SuburbId>' . $SuburbId . '</SuburbId>
                                                                <SuburbCode>NA</SuburbCode>
                                                                <SuburbName><![CDATA[' . $SuburbName . ']]></SuburbName>
                                                                <AreaId>' . $AreaId . '</AreaId>
                                                                <AreaName><![CDATA[' . $AreaName . ']]></AreaName>
                                                                <BrandId>' . $BrandId . '</BrandId>
                                                                <BrandName><![CDATA[' . $BrandName . ']]></BrandName>
                                                                <ChainId>' . $ChainId . '</ChainId>
                                                                <ChainName><![CDATA[' . $ChainName . ']]></ChainName>    
                                                                <SupplierCountryCode><![CDATA[' . $SupplierCountryCode . ']]></SupplierCountryCode>
                                                                <SupplierCityCode><![CDATA[' . $SupplierCityCode . ']]></SupplierCityCode>
                                                                <SupplierHotelCode>' . $SupplierHotelCode . '</SupplierHotelCode>                              
                                                                <SupplierHotelRoomCode></SupplierHotelRoomCode>
                                                                <SupplierItemCode5></SupplierItemCode5>
                                                                <SupplierItemCode6></SupplierItemCode6>                              
                                                                <SupplierSuburbCode>NA</SupplierSuburbCode>
                                                                <SupplierAreaCode>NA</SupplierAreaCode>                              
                                                                <ApprovedBy>' . $ApprovedBy . '</ApprovedBy>
                                                                <ApprovedDate>' . $ApprovedDate . '</ApprovedDate>
                                                                <CreatedBy>' . $CreatedBy . '</CreatedBy>
                                                                <CreatedDate>' . $created . '</CreatedDate> 
                                                              </ResourceDetailsData>              
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';

                $log_call_screen = 'Edit - Hotel Mapping';
                $RESOURCEDATA = 'RESOURCEDATA_HOTELMAPPING';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');

                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);
//Get response from here
                    $xml_arr = $this->xml2array($order_return);

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully created [Code:$log_call_status_code]";
                        $this->TravelHotelRoomSupplier->updateAll(array('wtb_status' => "'1'", 'is_update' => "'Y'"), array('id' => $id));
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT'][$RESOURCEDATA]['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record creation [Code:$log_call_status_code]";
                        $this->TravelHotelRoomSupplier->updateAll(array('wtb_status' => "'2'"), array('id' => $id));
                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->save($this->request->data['LogCall']);
                $LogId = $this->LogCall->getLastInsertId();
                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                $message = $xml_msg;
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Log Id [' . $LogId . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $this->Session->setFlash($message, 'success');
            }
            else
                $this->Session->setFlash('Unable to add Action item.', 'failure');

            $this->redirect(array('controller' => 'reports', 'action' => 'hotel_mapping_list/' . $CityId));
        }


        $TravelSuppliers = $this->TravelSupplier->find('all', array('fields' => 'supplier_code, supplier_name', 'conditions' => array('active' => 'TRUE'), 'order' => 'supplier_name ASC'));
        $TravelSuppliers = Set::combine($TravelSuppliers, '{n}.TravelSupplier.supplier_code', array('%s - %s', '{n}.TravelSupplier.supplier_code', '{n}.TravelSupplier.supplier_name'));


        $TravelCountries = $this->TravelCountry->find('all', array('fields' => 'country_code, country_name', 'conditions' => array('country_code' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_code']), 'order' => 'country_name ASC'));
        $TravelCountries = Set::combine($TravelCountries, '{n}.TravelCountry.country_code', array('%s - %s', '{n}.TravelCountry.country_code', '{n}.TravelCountry.country_name'));


        $TravelCities = $this->TravelCity->find('all', array('fields' => 'city_code, city_name', 'conditions' => array('city_code' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_city_code']), 'order' => 'city_name ASC'));
        $TravelCities = Set::combine($TravelCities, '{n}.TravelCity.city_code', array('%s - %s', '{n}.TravelCity.city_code', '{n}.TravelCity.city_name'));

        $Provinces = $this->Province->find('list', array(
            'conditions' => array(
                'Province.country_id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_country_id'],
                'Province.continent_id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_continent_id'],
                'Province.status' => '1',
                'Province.wtb_status' => '1',
                'Province.active' => 'TRUE',
            ),
            'fields' => array('Province.id', 'Province.name'),
            'order' => 'Province.name ASC'
        ));

        $TravelHotelLookups = $this->TravelHotelLookup->find('list', array('fields' => 'hotel_code, hotel_name', 'conditions' => array('hotel_code' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_code']), 'order' => 'hotel_name ASC'));


        $TravelAreas = $this->TravelArea->find('list', array(
            'conditions' => array(
                'TravelArea.id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_area_id'],
            ),
            'fields' => 'TravelArea.id, TravelArea.area_name',
            'order' => 'TravelArea.area_name ASC'
        ));


        $TravelSuburbs = $this->TravelSuburb->find('list', array(
            'conditions' => array(
                'TravelSuburb.id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_suburb_id'],
            ),
            'fields' => 'TravelSuburb.id, TravelSuburb.name',
            'order' => 'TravelSuburb.name ASC'
        ));

        $TravelChains = $this->TravelChain->find('list', array(
            'conditions' => array(
                'TravelChain.id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_chain_id'],
            ),
            'fields' => 'TravelChain.id, TravelChain.chain_name',
            'order' => 'TravelChain.chain_name ASC'
        ));

        $TravelBrands = $this->TravelBrand->find('list', array(
            'conditions' => array(
                'TravelBrand.id' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_brand_id'],
            ),
            'fields' => 'TravelBrand.id, TravelBrand.brand_name',
            'order' => 'TravelBrand.brand_name ASC'
        ));

        $HotelUrl = $this->TravelHotelLookup->find('first', array('conditions' => array('hotel_code' => $TravelHotelRoomSuppliers['TravelHotelRoomSupplier']['hotel_code']), 'fields' => array('url_hotel', 'address')));

        $this->set(compact('TravelHotelLookups', 'Provinces', 'TravelCities', 'TravelCountries', 'TravelSuppliers', 'TravelAreas', 'TravelSuburbs', 'TravelChains', 'TravelBrands', 'HotelUrl'));

        $this->request->data = $TravelHotelRoomSuppliers;
    }

    public function suburb_edit($id = null, $mode = 1) {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $user_id = $this->Auth->user('id');
        $dummy_status = $this->Auth->user('dummy_status');
        $this->set(compact('mode'));
        $xml_error = 'FALSE';

        if (!$id) {
            throw new NotFoundException(__('Invalid Suburb'));
        }

        $TravelSuburbs = $this->TravelSuburb->findById($id);

        if (!$TravelSuburbs) {
            throw new NotFoundException(__('Invalid suburb'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            //    $this->TravelSuburb->set($this->data);
            if ($this->TravelSuburb->validates() == true) {

                $this->TravelSuburb->id = $id;
                if ($this->TravelSuburb->save($this->request->data)) {
                    $SuburbId = $id;
                    $SuburbName = $this->data['TravelSuburb']['name'];
                    $CityId = $this->data['TravelSuburb']['city_id'];
                    $CityName = $this->data['TravelSuburb']['city_name'];
                    $CountryId = $this->data['TravelSuburb']['country_id'];
                    $CountryName = $this->data['TravelSuburb']['country_name'];
                    $ContinentId = $this->data['TravelSuburb']['continent_id'];
                    $ContinentName = $this->data['TravelSuburb']['continent_name'];
                    $SuburbDescription = $this->data['TravelSuburb']['description'];
                    $Active = strtolower($this->data['TravelSuburb']['active']);
                    $TopNeighborhood = strtolower($this->data['TravelSuburb']['top_neighborhood']);
                    $SuburbStatus = $this->data['TravelSuburb']['status'];
                    $ProvinceId = $this->data['TravelSuburb']['province_id'];
                    $ProvinceName = $this->data['TravelSuburb']['province_name'];
                    if ($SuburbStatus)
                        $SuburbStatus = 'true';
                    else
                        $SuburbStatus = 'false';

                    $WtbStatus = $TravelSuburbs['TravelSuburb']['wtb_status'];
                    if ($WtbStatus)
                        $WtbStatus = 'true';
                    else
                        $WtbStatus = 'false';
                    $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');

                    $is_update = $TravelSuburbs['TravelSuburb']['is_update'];

                    if ($is_update == 'Y')
                        $actiontype = 'Update';
                    else
                        $actiontype = 'AddNew';

                    $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_Suburb</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="' . $actiontype . '">
                                                                <SuburbId>' . $SuburbId . '</SuburbId>
                                                                <SuburbCode>NA</SuburbCode>
                                                                <SuburbName>' . $SuburbName . '</SuburbName>
                                                                <CityId>' . $CityId . '</CityId>
                                                                <CityCode>NA</CityCode>
                                                                <CityName>' . $CityName . '</CityName>
                                                                <CountryId>' . $CountryId . '</CountryId>
                                                                <CountryCode>NA</CountryCode>
                                                                <CountryName>' . $CountryName . '</CountryName>
                                                                <ContinentId>' . $ContinentId . '</ContinentId>
                                                                <ContinentCode>NA</ContinentCode>
                                                                <ContinentName>' . $ContinentName . '</ContinentName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName>' . $ProvinceName . '</ProvinceName>
                                                                <SuburbDescription>' . $SuburbDescription . '</SuburbDescription>
                                                                <SuburbKeyword>NA</SuburbKeyword>
                                                                <Active>' . $Active . '</Active>
                                                                <TopNeighborhood>' . $TopNeighborhood . '</TopNeighborhood>
                                                                <SuburbStatus>' . $SuburbStatus . '</SuburbStatus>
                                                                <WtbStatus>' . $WtbStatus . '</WtbStatus>
                                                                <ApprovedBy>0</ApprovedBy>
                                                                <ApprovedDate>1111-01-01T00:00:00</ApprovedDate>
                                                                <CreatedBy>' . $user_id . '</CreatedBy>
                                                                <CreatedDate>' . $CreatedDate . '</CreatedDate>                                 

                                                            </ResourceDetailsData>                        
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';


                    $log_call_screen = 'Suburb - Edit';

                    $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                    $client = new SoapClient(null, array(
                        'location' => $location_URL,
                        'uri' => '',
                        'trace' => 1,
                    ));

                    try {
                        $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                        $xml_arr = $this->xml2array($order_return);

                        if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_SUBURB']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_SUBURB']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_SUBURB']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                            $xml_msg = "Foreign record has been successfully updated [Code:$log_call_status_code]";
                            $this->TravelSuburb->updateAll(array('TravelSuburb.wtb_status' => "'1'"), array('TravelSuburb.id' => $SuburbId));
                        } else {

                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_SUBURB']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_SUBURB']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                            $xml_msg = "There was a problem with foreign record updated [Code:$log_call_status_code]";
                            $this->TravelSuburb->updateAll(array('TravelSuburb.wtb_status' => "'2'"), array('TravelSuburb.id' => $SuburbId));
                            $xml_error = 'TRUE';
                        }
                    } catch (SoapFault $exception) {
                        var_dump(get_class($exception));
                        var_dump($exception);
                    }


                    $this->request->data['LogCall']['log_call_nature'] = 'Production';
                    $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                    $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                    $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                    $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                    $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                    $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                    $this->request->data['LogCall']['log_call_by'] = $user_id;
                    $this->LogCall->save($this->request->data['LogCall']);
                    $message = 'Local record has been successfully updated.<br />' . $xml_msg;
                    $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                    $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                    if ($xml_error == 'TRUE') {
                        $Email = new CakeEmail();

                        $Email->viewVars(array(
                            'request_xml' => trim($xml_string),
                            'respon_message' => $log_call_status_message,
                            'respon_code' => $log_call_status_code,
                        ));

                        $to = 'biswajit@wtbglobal.com';
                        $cc = 'infra@sumanus.com';

                        $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                    }
                    $this->Session->setFlash($message, 'success');
                    $this->redirect(array('controller' => 'reports', 'action' => 'suburb_list/' . $CityId));
                } else {
                    $this->Session->setFlash('Unable to update Suburb.', 'failure');
                }
            }
        }


        $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $TravelSuburbs['TravelSuburb']['country_id'], 'city_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'city_name ASC'));
        //  $TravelCities = Set::combine($TravelCities, '{n}.TravelCity.id', array('%s - %s', '{n}.TravelCity.city_code', '{n}.TravelCity.city_name'));
        $this->set(compact('TravelCities'));

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $TravelSuburbs['TravelSuburb']['continent_id'], 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
        // $TravelCountries = Set::combine($TravelCountries, '{n}.TravelCountry.id', array('%s - %s', '{n}.TravelCountry.country_code', '{n}.TravelCountry.country_name'));
        $this->set(compact('TravelCountries'));

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents'));

        $Provinces = $this->Province->find('list', array(
            'conditions' => array(
                'Province.country_id' => $TravelSuburbs['TravelSuburb']['country_id'],
                'Province.continent_id' => $TravelSuburbs['TravelSuburb']['continent_id'],
                'Province.status' => '1',
                'Province.wtb_status' => '1',
                'Province.active' => 'TRUE',
            ),
            'fields' => array('Province.id', 'Province.name'),
            'order' => 'Province.name ASC'
        ));

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents', 'Provinces'));

        $this->request->data = $TravelSuburbs;
    }

    public function area_edit($id = null, $mode = 1) {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $user_id = $this->Auth->user('id');
        $dummy_status = $this->Auth->user('dummy_status');
        $xml_error = 'FALSE';
        $this->set(compact('mode'));

        if (!$id) {
            throw new NotFoundException(__('Invalid Suburb'));
        }

        $TravelAreas = $this->TravelArea->findById($id);

        if (!$TravelAreas) {
            throw new NotFoundException(__('Invalid suburb'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $AreaName = $this->data['TravelArea']['area_name'];
                    $SuburbId = $this->data['TravelArea']['suburb_id'];
                    $SuburbName = $this->Common->getSuburbName($SuburbId);
                    $this->request->data['TravelArea']['suburb_name'] = $SuburbName;
                    $CityId = $this->data['TravelArea']['city_id'];
                    $CityName = $this->Common->getCityName($CityId);
                    $this->request->data['TravelArea']['city_name'] = $CityName;
                    $CountryId = $this->data['TravelArea']['country_id'];
                    $CountryName = $this->Common->getCountryName($CountryId);
                    $this->request->data['TravelArea']['country_name'] = $CountryName;
                    $ContinentId = $this->data['TravelArea']['continent_id'];
                    $ContinentName = $this->Common->getContinentName($ContinentId);
                    $this->request->data['TravelArea']['continent_name'] = $ContinentName;
                    $ProvinceId = $this->data['TravelArea']['province_id'];
                    $ProvinceName = $this->Common->getProvinceName($ProvinceId);
                    $this->request->data['TravelArea']['province_name'] = $ProvinceName;
                    
            $this->TravelArea->set($this->data);
            if ($this->TravelArea->validates() == true) {

                $this->TravelArea->id = $id;
                if ($this->TravelArea->save($this->request->data)) {
                    $AreaId = $id;
                    
                    $AreaDescription = $this->data['TravelArea']['area_description'];
                    $TopArea = strtolower($this->data['TravelArea']['top_area']);
                    
                    $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
                    $Active = strtolower($this->data['TravelArea']['area_active']);
                    $is_update = $TravelAreas['TravelArea']['is_update'];
                    $AreaStatus = $this->data['TravelArea']['area_status'];
                    if ($AreaStatus)
                        $AreaStatus = 'true';
                    else
                        $AreaStatus = 'false';

                    $WtbStatus = $TravelAreas['TravelArea']['wtb_status'];
                    if ($WtbStatus)
                        $WtbStatus = 'true';
                    else
                        $WtbStatus = 'false';

                    if ($is_update == 'Y')
                        $actiontype = 'Update';
                    else
                        $actiontype = 'AddNew';



                    $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_Area</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                             <ResourceDetailsData srno="1" actiontype="' . $actiontype . '">
                                                                <AreaId>' . $AreaId . '</AreaId>
                                                                <AreaCode>NA</AreaCode>
                                                                <AreaName>' . $AreaName . '</AreaName>
                                                                <SuburbId>' . $SuburbId . '</SuburbId>
                                                                <SuburbCode>NA</SuburbCode>
                                                                <SuburbName>' . $SuburbName . '</SuburbName>
                                                                <CityId>' . $CityId . '</CityId>
                                                                <CityCode>NA</CityCode>
                                                                <CityName>' . $CityName . '</CityName>
                                                                <CountryId>' . $CountryId . '</CountryId>
                                                                <CountryCode>NA</CountryCode>
                                                                <CountryName>' . $CountryName . '</CountryName>
                                                                <ContinentId>' . $ContinentId . '</ContinentId>
                                                                <ContinentCode>NA</ContinentCode>
                                                                <ContinentName>' . $ContinentName . '</ContinentName>
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName>' . $ProvinceName . '</ProvinceName>
                                                                <AreaMap></AreaMap>
                                                                <AreaMapSS></AreaMapSS>
                                                                <AreaComment></AreaComment>
                                                                <AreaDescription></AreaDescription>
                                                                <AreaActive>' . $Active . '</AreaActive>
                                                                <AreaDomainName></AreaDomainName>
                                                                <TopArea>' . $TopArea . '</TopArea>
                                                                <AreaStatus>' . $AreaStatus . '</AreaStatus>
                                                                <WtbStatus>' . $WtbStatus . '</WtbStatus>
                                                                <ActiveMap>true</ActiveMap>
                                                                <Isupdated>1</Isupdated>
                                                                <PFTActive>1</PFTActive>
                                                                <AreaNameURL></AreaNameURL>
                                                                <TopDestination>1</TopDestination>
                                                                <ApprovedBy>0</ApprovedBy>
                                                                <ApprovedDate>1111-01-01T00:00:00</ApprovedDate>
                                                                <CreatedBy>' . $user_id . '</CreatedBy>
                                                                <CreatedDate>' . $CreatedDate . '</CreatedDate>
                                                            </ResourceDetailsData>

                         
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';


                    $log_call_screen = 'Area - Edit';

                    $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                    $client = new SoapClient(null, array(
                        'location' => $location_URL,
                        'uri' => '',
                        'trace' => 1,
                    ));

                    try {
                        $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                        $xml_arr = $this->xml2array($order_return);
                        //echo htmlentities($xml_string);
                        // pr($xml_arr);
                        //  die;


                        if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_AREA']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_AREA']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_AREA']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                            $xml_msg = "Foreign record has been successfully updated [Code:$log_call_status_code]";
                            $this->TravelArea->updateAll(array('TravelArea.wtb_status' => "'1'"), array('TravelArea.id' => $AreaId));
                        } else {

                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_AREA']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_AREA']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                            $xml_msg = "There was a problem with foreign record updation [Code:$log_call_status_code]";
                            $this->TravelArea->updateAll(array('TravelArea.wtb_status' => "'2'"), array('TravelArea.id' => $AreaId));
                            $xml_error = 'TRUE';
                        }
                    } catch (SoapFault $exception) {
                        var_dump(get_class($exception));
                        var_dump($exception);
                    }


                    $this->request->data['LogCall']['log_call_nature'] = 'Production';
                    $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                    $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                    $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                    $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                    $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                    $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                    $this->request->data['LogCall']['log_call_by'] = $user_id;
                    $this->LogCall->save($this->request->data['LogCall']);
                    $message = 'Local record has been successfully updated.<br />' . $xml_msg;
                    $this->Session->setFlash($message, 'success');
                    $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                    $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                    if ($xml_error == 'TRUE') {
                        $Email = new CakeEmail();

                        $Email->viewVars(array(
                            'request_xml' => trim($xml_string),
                            'respon_message' => $log_call_status_message,
                            'respon_code' => $log_call_status_code,
                        ));

                        $to = 'biswajit@wtbglobal.com';
                        $cc = 'infra@sumanus.com';

                        $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                    }
                    $this->redirect(array('controller' => 'reports', 'action' => 'area_list?city_id:' . $CityId));
                } else {
                    $this->Session->setFlash('Unable to update Area.', 'failure');
                }
            }
        }


        $TravelCities = $this->TravelCity->find('list', array('fields' => 'id,city_name', 'conditions' => array('country_id' => $TravelAreas['TravelArea']['country_id'], 'city_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'city_name ASC'));
        //  $TravelCities = Set::combine($TravelCities, '{n}.TravelCity.id', array('%s - %s', '{n}.TravelCity.city_code', '{n}.TravelCity.city_name'));
        $this->set(compact('TravelCities'));

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $TravelAreas['TravelArea']['continent_id'], 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
        $this->set(compact('TravelCountries'));

        $Provinces = $this->Province->find('list', array(
            'conditions' => array(
                'Province.country_id' => $TravelAreas['TravelArea']['country_id'],
                'Province.continent_id' => $TravelAreas['TravelArea']['continent_id'],
                'Province.status' => '1',
                'Province.wtb_status' => '1',
                'Province.active' => 'TRUE',
            ),
            'fields' => array('Province.id', 'Province.name'),
            'order' => 'Province.name ASC'
        ));

        $TravelSuburbs = $this->TravelSuburb->find('list', array('fields' => 'id,name', 'conditions' => array('status' => 1, 'country_id' => $TravelAreas['TravelArea']['country_id'], 'city_id' => $TravelAreas['TravelArea']['city_id']), 'order' => 'name ASC'));
        $this->set(compact('TravelSuburbs'));

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $this->set(compact('TravelLookupContinents', 'Provinces'));



        $this->request->data = $TravelAreas;
    }

    public function province_list() {

        $search_condition = array();
        $TravelCountries = array();
        $name = '';
        $continent_id = '';
        $country_id = '';
        $status = '';
        $wtb_status = '';
        $active = '';


        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->data['Province']['name'])) {
                $name = $this->data['Province']['name'];
                array_push($search_condition, array("Province.name LIKE '%$name%'"));
            }
            if (!empty($this->data['Province']['continent_id'])) {
                $continent_id = $this->data['Province']['continent_id'];
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
                array_push($search_condition, array('Province.continent_id' => $continent_id));
            }

            if (!empty($this->data['Province']['country_id'])) {
                $country_id = $this->data['Province']['country_id'];
                array_push($search_condition, array('Province.country_id' => $country_id));
            }
            if (!empty($this->data['Province']['status'])) {
                $status = $this->data['Province']['status'];
                array_push($search_condition, array('Province.status' => $status));
            }
            if (!empty($this->data['Province']['wtb_status'])) {
                $wtb_status = $this->data['Province']['wtb_status'];
                array_push($search_condition, array('Province.wtb_status' => $wtb_status));
            }
            if (!empty($this->data['Province']['active'])) {
                $active = $this->data['Province']['active'];
                array_push($search_condition, array('Province.active' => $active));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['name'])) {
                $name = $this->request->params['named']['name'];
                array_push($search_condition, array("Province.name LIKE '%$name%'"));
            }
            if (!empty($this->request->params['named']['continent_id'])) {
                $continent_id = $this->request->params['named']['continent_id'];
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id,country_name', 'conditions' => array('continent_id' => $continent_id, 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
                array_push($search_condition, array('Province.continent_id' => $continent_id));
            }

            if (!empty($this->request->params['named']['country_id'])) {
                $country_id = $this->request->params['named']['country_id'];
                array_push($search_condition, array('Province.country_id' => $country_id));
            }
            if (!empty($this->request->params['named']['status'])) {
                $status = $this->request->params['named']['status'];
                array_push($search_condition, array('Province.status' => $status));
            }
            if (!empty($this->request->params['named']['wtb_status'])) {
                $wtb_status = $this->request->params['named']['wtb_status'];
                array_push($search_condition, array('Province.wtb_status' => $wtb_status));
            }
            if (!empty($this->request->params['named']['active'])) {
                $active = $this->request->params['named']['active'];
                array_push($search_condition, array('Province.active' => $active));
            }
        }
        
        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('Province.' . $key => $value));
               
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('Province.' . $key => $value));
                
            }
        }
        
        
        $this->paginate['order'] = array('Province.name' => 'asc');
        $this->set('Provinces', $this->paginate("Province", $search_condition));

        //$log = $this->Province->getDataSource()->getLog(false, false);       
        //debug($log);


        if (!isset($this->passedArgs['name']) && empty($this->passedArgs['name'])) {
            $this->passedArgs['name'] = (isset($this->data['Province']['name'])) ? $this->data['Province']['name'] : '';
        }
        if (!isset($this->passedArgs['continent_id']) && empty($this->passedArgs['continent_id'])) {
            $this->passedArgs['continent_id'] = (isset($this->data['Province']['continent_id'])) ? $this->data['Province']['continent_id'] : '';
        }
        if (!isset($this->passedArgs['country_id']) && empty($this->passedArgs['country_id'])) {
            $this->passedArgs['country_id'] = (isset($this->data['Province']['country_id'])) ? $this->data['Province']['country_id'] : '';
        }
        if (!isset($this->passedArgs['status']) && empty($this->passedArgs['status'])) {
            $this->passedArgs['status'] = (isset($this->data['Province']['status'])) ? $this->data['Province']['status'] : '';
        }
        if (!isset($this->passedArgs['wtb_status']) && empty($this->passedArgs['wtb_status'])) {
            $this->passedArgs['wtb_status'] = (isset($this->data['Province']['wtb_status'])) ? $this->data['Province']['wtb_status'] : '';
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
            $this->passedArgs['active'] = (isset($this->data['Province']['active'])) ? $this->data['Province']['active'] : '';
        }

        if (!isset($this->data) && empty($this->data)) {

            $this->data['Province']['name'] = $this->passedArgs['name'];
            $this->data['Province']['continent_id'] = $this->passedArgs['continent_id'];
            $this->data['Province']['country_id'] = $this->passedArgs['country_id'];
            $this->data['Province']['status'] = $this->passedArgs['status'];
            $this->data['Province']['wtb_status'] = $this->passedArgs['wtb_status'];
            $this->data['Province']['active'] = $this->passedArgs['active'];
        }

        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));

        $this->set(compact('name', 'country_id', 'continent_id', 'status', 'wtb_status', 'active', 'TravelCountries', 'TravelLookupContinents'));
    }

    public function province_edit($id = null, $mode = 1) {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $user_id = $this->Auth->user('id');
        $xml_error = 'FALSE';
        //$ModifiedDate = '1111-01-01T00:00:00';

        $this->set(compact('mode'));
        if (!$id) {
            throw new NotFoundException(__('Invalid Province'));
        }

        $Provinces = $this->Province->findById($id);

        if (!$Provinces) {
            throw new NotFoundException(__('Invalid Province'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Province->set($this->data);
            if ($this->Province->validates() == true) {

                $this->Province->id = $id;
                if ($this->Province->save($this->request->data)) {
                    $ProvinceId = $id;
                    $ProvinceName = $this->data['Province']['name'];
                    $CountryId = $this->data['Province']['country_id'];
                    $CountryCode = $this->data['Province']['country_code'];
                    $CountryName = $this->data['Province']['country_name'];
                    $ContinentId = $this->data['Province']['continent_id'];
                    $ContinentCode = $this->data['Province']['continent_code'];
                    $ContinentName = $this->data['Province']['continent_name'];
                    $ProvinceDescription = $this->data['Province']['description'];
                    $TopProvince = strtolower($this->data['Province']['top_province']);
                    $status = $Provinces['Province']['status'];
                    if ($status == '1')
                        $status = 'true';
                    else
                        $status = 'false';
                    $Active = strtolower($Provinces['Province']['active']);
                    $is_update = $Provinces['Province']['is_update'];
                    if ($is_update == 'Y')
                        $actiontype = 'Update';
                    else
                        $actiontype = 'AddNew';
                    //$modified = explode(' ', $Provinces['Province']['modified']);
                    $ModifiedDate = '1111-01-01T00:00:00';
                    $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');

                    $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                                <ResourceDataRequest>
                                                    <RequestAuditInfo>
                                                        <RequestType>PXML_WData_Province</RequestType>
                                                        <RequestTime>' . $CreatedDate . '</RequestTime>
                                                        <RequestResource>Silkrouters</RequestResource>
                                                    </RequestAuditInfo>
                                                    <RequestParameters>                        
                                                        <ResourceData>
                                                            <ResourceDetailsData srno="1" actiontype="' . $actiontype . '">
                                                                <ProvinceId>' . $ProvinceId . '</ProvinceId>
                                                                <ProvinceName><![CDATA[' . $ProvinceName . ']]></ProvinceName>
                                                                <CountryId>' . $CountryId . '</CountryId>
                                                                <CountryCode><![CDATA[' . $CountryCode . ']]></CountryCode>
                                                                <CountryName><![CDATA[' . $CountryName . ']]></CountryName>
                                                                <ContinentId>' . $ContinentId . '</ContinentId>
                                                                <ContinentCode><![CDATA[' . $ContinentCode . ']]></ContinentCode>
                                                                <ContinentName><![CDATA[' . $ContinentName . ']]></ContinentName>
                                                                <TopProvince>' . $TopProvince . '</TopProvince>
                                                                <ProvinceNameJP></ProvinceNameJP>
                                                                <ProvinceNameFR></ProvinceNameFR>
                                                                <ProvinceNameDE></ProvinceNameDE>
                                                                <ProvinceNameCN></ProvinceNameCN>
                                                                <ProvinceNameCNT></ProvinceNameCNT>
                                                                <ProvinceNameIT></ProvinceNameIT>
                                                                <ProvinceNameES></ProvinceNameES>
                                                                <ProvinceNameKR></ProvinceNameKR>
                                                                <ProvinceNameURL></ProvinceNameURL>
                                                                <ProvinceURLTEMP1></ProvinceURLTEMP1>
                                                                <ProvinceURLTEMP2></ProvinceURLTEMP2>
                                                                <ProvinceURLTEMP3></ProvinceURLTEMP3>
                                                                <ProvinceDescription><![CDATA[' . $ProvinceDescription . ']]></ProvinceDescription>
                                                                <ProvinceKeyword></ProvinceKeyword>
                                                                <Active>' . $Active . '</Active>
                                                                <WtbStatus>true</WtbStatus>
                                                                <Status>' . $status . '</Status>
                                                                <CreatedBy>' . $user_id . '</CreatedBy>
                                                                <CreatedDate>' . $CreatedDate . '</CreatedDate>
                                                                <ModifiedDate>' . $ModifiedDate . '</ModifiedDate>
                                                            </ResourceDetailsData>                         
                                                    </ResourceData>
                                                    </RequestParameters>
                                                </ResourceDataRequest>
                                            </RequestInfo>
                                        </ProcessXML>
                                    </soap:Body>';


                    $log_call_screen = 'Province - Edit';

                    $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                    $client = new SoapClient(null, array(
                        'location' => $location_URL,
                        'uri' => '',
                        'trace' => 1,
                    ));

                    try {
                        $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                        $xml_arr = $this->xml2array($order_return);


                        if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_PROVINCE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_PROVINCE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_PROVINCE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                            $xml_msg = "Foreign record has been successfully updated [Code:$log_call_status_code]";
                            $this->Province->updateAll(array('Province.wtb_status' => "'1'", 'Province.is_update' => "'Y'"), array('Province.id' => $ProvinceId));
                        } else {

                            $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_PROVINCE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                            $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_PROVINCE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                            $xml_msg = "There was a problem with foreign record updation [Code:$log_call_status_code]";
                            $this->Province->updateAll(array('Province.wtb_status' => "'2'"), array('Province.id' => $ProvinceId));
                            $xml_error = 'TRUE';
                        }
                    } catch (SoapFault $exception) {
                        var_dump(get_class($exception));
                        var_dump($exception);
                    }


                    $this->request->data['LogCall']['log_call_nature'] = 'Production';
                    $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                    $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                    $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                    $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                    $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                    $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                    $this->request->data['LogCall']['log_call_by'] = $user_id;
                    $this->LogCall->create();
                    $this->LogCall->save($this->request->data['LogCall']);
                    $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                    $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                    if ($xml_error == 'TRUE') {
                        $Email = new CakeEmail();

                        $Email->viewVars(array(
                            'request_xml' => trim($xml_string),
                            'respon_message' => $log_call_status_message,
                            'respon_code' => $log_call_status_code,
                        ));

                        $to = 'biswajit@wtbglobal.com';
                        $cc = 'infra@sumanus.com';

                        $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                    }


                    $message = 'Local record has been successfully updated.<br />' . $xml_msg;
                    $this->Session->setFlash($message, 'success');
                    $this->redirect(array('action' => 'province_list'));
                } else {
                    $this->Session->setFlash('Unable to update Province.', 'failure');
                }
            }
        }


        $TravelCountries = $this->TravelCountry->find('all', array('fields' => 'id,country_name,country_code', 'conditions' => array('continent_id' => $Provinces['Province']['continent_id'], 'country_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'country_name ASC'));
        $TravelCountries = Set::combine($TravelCountries, '{n}.TravelCountry.id', array('%s - %s', '{n}.TravelCountry.country_code', '{n}.TravelCountry.country_name'));


        $TravelLookupContinents = $this->TravelLookupContinent->find('all', array('fields' => 'id,continent_name,continent_code', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        $TravelLookupContinents = Set::combine($TravelLookupContinents, '{n}.TravelLookupContinent.id', array('%s - %s', '{n}.TravelLookupContinent.continent_code', '{n}.TravelLookupContinent.continent_name'));

        $this->set(compact('TravelLookupContinents', 'TravelCountries'));


        $this->request->data = $Provinces;
    }

    public function province_delete() {

        $location_URL = 'http://dev.wtbnetworks.com/TravelXmlManagerv001/ProEngine.Asmx';
        $action_URL = 'http://www.travel.domain/ProcessXML';
        $log_call_screen = '';
        $xml_msg = '';
        $xml_error = 'FALSE';
        $user_id = $this->Auth->user('id');
        $CreatedDate = date('Y-m-d') . 'T' . date('h:i:s');
        $flag = 0;
        foreach ($this->data['Province']['check'] as $val) {

            $Provinces = $this->Province->findById($val);

            $ProvinceId = $Provinces['Province']['id'];
            $ProvinceName = $Provinces['Province']['name'];
            $CountryId = $Provinces['Province']['country_id'];
            $CountryCode = $Provinces['Province']['country_code'];
            $CountryName = $Provinces['Province']['country_name'];
            $ContinentId = $Provinces['Province']['continent_id'];
            $ContinentCode = $Provinces['Province']['continent_code'];
            $ContinentName = $Provinces['Province']['continent_name'];
            $ProvinceDescription = $Provinces['Province']['description'];
            $TopProvince = $Provinces['Province']['top_province'];
            $status = $Provinces['Province']['status'];
            $WtbStatus = $Provinces['Province']['wtb_status'];
            $is_update = $Provinces['Province']['is_update'];
            $created_by = $Provinces['Province']['created_by'];
            $created = $Provinces['Province']['created'];
            $modified = $Provinces['Province']['modified'];
            $Active = $Provinces['Province']['active'];
            
            if ($this->Common->checkProvinceExistsHotel($ProvinceId)) {
                $this->Session->setFlash($ProvinceName.' province can not deleted, '.$ProvinceName.' exists in hotel table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'province_reports'));
            }
            elseif($this->Common->checkProvinceExistsCity($ProvinceId)) {
                $this->Session->setFlash($ProvinceName.' province can not deleted, '.$ProvinceName.' exists in city table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'province_reports'));
            }
            elseif($this->Common->checkProvinceExistsSuburb($ProvinceId)) {
                $this->Session->setFlash($ProvinceName.' province can not deleted, '.$ProvinceName.' exists in suburb table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'province_reports'));
            }
            elseif($this->Common->checkProvinceExistsArea($ProvinceId)) {
                $this->Session->setFlash($ProvinceName.' province can not deleted, '.$ProvinceName.' exists in area table', 'failure');
                return $this->redirect(array('controller' => 'reports', 'action' => 'province_reports'));
            }


            $save[] = array('DeleteProvince' => array(
                    'id' => $ProvinceId,
                    'name' => $ProvinceName,
                    'country_id' => $CountryId,
                    'country_code' => $CountryCode,
                    'country_name' => $CountryName,
                    'continent_id' => $ContinentId,
                    'continent_code' => $ContinentCode,
                    'continent_name' => $ContinentName,
                    'description' => $ProvinceDescription,
                    'active' => $Active,
                    'top_province' => $TopProvince,
                    'status' => $status,
                    'is_update' => $is_update,
                    'wtb_status' => $WtbStatus,
                    'created_by' => $created_by,
                    'created' => $created,
                    'modified' => $modified,
            ));


            if ($this->Province->delete($val)) {

                $content_xml_str = '<soap:Body>
                                        <ProcessXML xmlns="http://www.travel.domain/">
                                            <RequestInfo>
                                              <ResourceDataRequest>
                                                <RequestAuditInfo>
                                                  <RequestType>PXML_WData_LookupDelete</RequestType>
                                                  <RequestTime>' . $CreatedDate . '</RequestTime>
                                                  <RequestResource>Silkrouters</RequestResource>
                                                </RequestAuditInfo>
                                                <RequestParameters>
                                                  <ResourceData>
                                                  <ResourceDetailsData srno="1" lookuptype="Province">
                                                        <ProvinceId>' . $val . '</ProvinceId>            
                                                    </ResourceDetailsData>                                                    
                                                  </ResourceData>
                                                </RequestParameters>
                                              </ResourceDataRequest>
                                            </RequestInfo>
                                          </ProcessXML>
                                    </soap:Body>';


                $log_call_screen = 'Delete - Province';

                $xml_string = Configure::read('travel_start_xml_str') . $content_xml_str . Configure::read('travel_end_xml_str');
                $client = new SoapClient(null, array(
                    'location' => $location_URL,
                    'uri' => '',
                    'trace' => 1,
                ));

                try {
                    $order_return = $client->__doRequest($xml_string, $location_URL, $action_URL, 1);

                    $xml_arr = $this->xml2array($order_return);
                    //echo htmlentities($xml_string);
                    //pr($xml_arr);
                    //die;

                    if ($xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0] == '201') {
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0];
                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['UPDATEINFO']['STATUS'][0];
                        $xml_msg = "Foreign record has been successfully deleted [Code:$log_call_status_code]";
                    } else {

                        $log_call_status_message = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['ERRORINFO']['ERROR'][0];
                        $log_call_status_code = $xml_arr['SOAP:ENVELOPE']['SOAP:BODY']['PROCESSXMLRESPONSE']['PROCESSXMLRESULT']['RESOURCEDATA_LOOKUPDELETE']['RESPONSEAUDITINFO']['RESPONSEINFO']['RESPONSEID'][0]; // RESPONSEID
                        $xml_msg = "There was a problem with foreign record deletion [Code:$log_call_status_code]";

                        $xml_error = 'TRUE';
                    }
                } catch (SoapFault $exception) {
                    var_dump(get_class($exception));
                    var_dump($exception);
                }


                $this->request->data['LogCall']['log_call_nature'] = 'Production';
                $this->request->data['LogCall']['log_call_type'] = 'Outbound';
                $this->request->data['LogCall']['log_call_parms'] = trim($xml_string);
                $this->request->data['LogCall']['log_call_status_code'] = $log_call_status_code;
                $this->request->data['LogCall']['log_call_status_message'] = $log_call_status_message;
                $this->request->data['LogCall']['log_call_screen'] = $log_call_screen;
                $this->request->data['LogCall']['log_call_counterparty'] = 'WTBNETWORKS';
                $this->request->data['LogCall']['log_call_by'] = $user_id;
                $this->LogCall->create();
                $this->LogCall->save($this->request->data['LogCall']);
                $message = 'Local record has been successfully deleted.<br />' . $xml_msg;

                $a = date('m/d/Y H:i:s', strtotime('-1 hour'));
                $date = new DateTime($a, new DateTimeZone('Asia/Calcutta'));
                if ($xml_error == 'TRUE') {
                    $Email = new CakeEmail();

                    $Email->viewVars(array(
                        'request_xml' => trim($xml_string),
                        'respon_message' => $log_call_status_message,
                        'respon_code' => $log_call_status_code,
                    ));

                    $to = 'biswajit@wtbglobal.com';
                    $cc = 'infra@sumanus.com';

                    $Email->template('XML/xml', 'default')->emailFormat('html')->to($to)->cc($cc)->from('admin@silkrouters.com')->subject('XML Error [' . $log_call_screen . '] Open By [' . $this->User->Username($user_id) . '] Date [' . date("m/d/Y H:i:s", $date->format('U')) . ']')->send();
                }
                $flag = 1;
            }
        }
        if ($flag) {
            $this->request->data['DeleteLogTable']['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $this->request->data['DeleteLogTable']['created_by'] = $user_id;
            $this->DeleteLogTable->save($this->request->data['DeleteLogTable']);
            $LogId = $this->DeleteLogTable->getLastInsertId();
            foreach ($save as $key => $val) {
                $save[$key]['DeleteProvince']['log_id'] = $LogId;
            }
            $this->DeleteProvince->create();
            $this->DeleteProvince->saveMany($save);
            $this->Session->setFlash($message, 'success');
        } else {
            $this->Session->setFlash('Unable to delete province.', 'failure');
        }

        return $this->redirect(array('controller' => 'reports', 'action' => 'province_reports'));
    }

    public function view_action($hotel_id = null, $hotel_supplier_id = null) {
        $this->layout = '';
        $TravelActionItems = $this->TravelActionItem->find('all', array('conditions' => array('TravelActionItem.hotel_id' => $hotel_id, 'TravelActionItem.level_id' => '7', 'TravelActionItem.action_item_active' => 'Yes', 'TravelActionItem.next_action_by != "NULL"')));
        $HotelMappingAction = $this->TravelActionItem->find('all', array('conditions' => array('TravelActionItem.hotel_supplier_id' => $hotel_supplier_id, 'TravelActionItem.level_id' => '4', 'TravelActionItem.action_item_active' => 'Yes', 'TravelActionItem.next_action_by != "NULL"')));
        // pr($HotelMappingAction);
        //$log = $this->TravelActionItem->getDataSource()->getLog(false, false);       
        //ebug($log);
        //die;
        $this->set(compact('TravelActionItems', 'HotelMappingAction'));
    }
    
    public function country_mapping_list() {

        $dummy_status = $this->Auth->user('dummy_status');
        $role_id = $this->Session->read("role_id");
        $search_condition = array();
        $search = '';
        $supplier_code = '';
        $country_wtb_code = '';
   
        $hotel_wtb_code = '';
        $status = '';
        $active = '';
        $exclude = '';
        $mapping_type = '';
        $wtb_status = '';
        $province_id = '';
        $TravelCities = array();
        $TravelHotelLookups = array();
        $Provinces = array();


        if ($this->request->is('post') || $this->request->is('put')) {

            if (!empty($this->data['TravelCountrySupplier']['search'])) {
                $search = $this->data['TravelCountrySupplier']['search'];
                array_push($search_condition, array('TravelCountrySupplier.country_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%"));
            }
            if (!empty($this->data['TravelCountrySupplier']['active'])) {
                $active = $this->data['TravelCountrySupplier']['active'];
                array_push($search_condition, array('TravelCountrySupplier.active' => $active));
            }
            if (!empty($this->data['TravelCountrySupplier']['wtb_status'])) {
                $wtb_status = $this->data['TravelCountrySupplier']['wtb_status'];
                array_push($search_condition, array('TravelCountrySupplier.wtb_status' => $wtb_status));
            }

            if (!empty($this->data['TravelCountrySupplier']['supplier_code'])) {
                $supplier_code = $this->data['TravelCountrySupplier']['supplier_code'];
                array_push($search_condition, array('TravelCountrySupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }

           
            if (!empty($this->data['TravelCountrySupplier']['pf_country_code'])) {
                $country_wtb_code = $this->data['TravelCountrySupplier']['pf_country_code'];
                array_push($search_condition, array('TravelCountrySupplier.pf_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
            }

            if (!empty($this->data['TravelCountrySupplier']['province_id'])) {
                $province_id = $this->data['TravelCountrySupplier']['province_id'];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.province_id' => $province_id)));
            }

            if (!empty($this->data['TravelCountrySupplier']['country_suppliner_status'])) {
                $status = $this->data['TravelCountrySupplier']['country_suppliner_status'];
                array_push($search_condition, array('TravelCountrySupplier.country_suppliner_status' => $status));
            }
            if (!empty($this->data['TravelCountrySupplier']['exclude'])) {
                $exclude = $this->data['TravelCountrySupplier']['exclude'];
                array_push($search_condition, array('TravelCountrySupplier.exclude' => $exclude));
            }
        } elseif ($this->request->is('get')) {

            if (!empty($this->request->params['named']['search '])) {
                $search = $this->request->params['named']['search '];
                array_push($search_condition, array('TravelCountrySupplier.country_name' . ' LIKE' => "%" . mysql_escape_string(trim(strip_tags($search))) . "%"));
            }
            if (!empty($this->request->params['named']['active'])) {
                $active = $this->request->params['named']['active'];
                array_push($search_condition, array('TravelCountrySupplier.active' => $active));
            }
            if (!empty($this->request->params['named']['province_id'])) {
                $province_id = $this->request->params['named']['province_id'];
                array_push($search_condition, array('OR' => array('TravelCountrySupplier.province_id' => $province_id)));
            }
            if (!empty($this->request->params['named']['wtb_status'])) {
                $wtb_status = $this->request->params['named']['wtb_status'];
                array_push($search_condition, array('TravelCountrySupplier.wtb_status' => $wtb_status));
            }

            if (!empty($this->request->params['named']['supplier_code'])) {
                $supplier_code = $this->request->params['named']['supplier_code'];
                array_push($search_condition, array('TravelCountrySupplier.supplier_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($supplier_code))) . "%"));
            }

            if (!empty($this->request->params['named']['pf_country_code'])) {
                $country_wtb_code = $this->request->params['named']['pf_country_code'];
                array_push($search_condition, array('TravelCountrySupplier.pf_country_code LIKE' => "%" . mysql_escape_string(trim(strip_tags($country_wtb_code))) . "%"));
               
            }
       

            if (!empty($this->request->params['named']['country_suppliner_status'])) {
                $status = $this->request->params['named']['country_suppliner_status'];
                array_push($search_condition, array('TravelCountrySupplier.country_suppliner_status' => $status));
            }
            if (!empty($this->request->params['named']['exclude'])) {
                $exclude = $this->request->params['named']['exclude'];
                array_push($search_condition, array('TravelCountrySupplier.exclude' => $exclude));
            }
        }

        if (count($this->params['pass'])) {

           foreach ($this->params['pass'] as $key => $value) {
                array_push($search_condition, array('TravelCountrySupplier.' . $key => $value));
                //$conArry = array('TravelHotelLookup.'.$key => $value);
                //$conAreaArry = array('TravelArea.'.$key => $value);
            }                
        } elseif (count($this->params['named'])) {

            foreach ($this->params['named'] as $key => $value) {
                array_push($search_condition, array('TravelCountrySupplier.' . $key => $value));
                //$conArry = array('TravelHotelLookup.'.$key => $value);
                //$conAreaArry = array('TravelArea.'.$key => $value);
            }
        }

        $this->paginate['order'] = array('TravelCountrySupplier.city_id' => 'asc');
        $this->set('TravelCountrySuppliers', $this->paginate("TravelCountrySupplier", $search_condition));


        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'supplier_code, supplier_name', 'conditions' => array('active' => 'TRUE'), 'order' => 'supplier_name ASC'));
        $this->set(compact('TravelSuppliers'));

        $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array(
                'TravelCountry.country_status' => '1',
                'TravelCountry.wtb_status' => '1',
                'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));

        //$TravelCountries = $this->TravelCountry->find('list', array('fields' => 'country_code, country_name', 'conditions' => array('country_status' => '1'), 'order' => 'country_name ASC'));
        $this->set(compact('TravelCountries'));


        $this->set(compact('TravelCities'));



        if (!isset($this->passedArgs['search']) && empty($this->passedArgs['search'])) {
            $this->passedArgs['search'] = (isset($this->data['TravelCountrySupplier']['search'])) ? $this->data['TravelCountrySupplier']['search'] : '';
        }
        if (!isset($this->passedArgs['active']) && empty($this->passedArgs['active'])) {
            $this->passedArgs['active'] = (isset($this->data['TravelCountrySupplier']['active'])) ? $this->data['TravelCountrySupplier']['active'] : '';
        }
        if (!isset($this->passedArgs['wtb_status']) && empty($this->passedArgs['wtb_status'])) {
            $this->passedArgs['wtb_status'] = (isset($this->data['TravelCountrySupplier']['wtb_status'])) ? $this->data['TravelCountrySupplier']['wtb_status'] : '';
        }
        if (!isset($this->passedArgs['supplier_code']) && empty($this->passedArgs['supplier_code'])) {
            $this->passedArgs['supplier_code'] = (isset($this->data['TravelCountrySupplier']['supplier_code'])) ? $this->data['TravelCountrySupplier']['supplier_code'] : '';
        }
        if (!isset($this->passedArgs['pf_country_code']) && empty($this->passedArgs['pf_country_code'])) {
            $this->passedArgs['pf_country_code'] = (isset($this->data['TravelCountrySupplier']['pf_country_code'])) ? $this->data['TravelCountrySupplier']['city_country_code'] : '';
        }
   
        if (!isset($this->passedArgs['country_suppliner_status']) && empty($this->passedArgs['country_suppliner_status'])) {
            $this->passedArgs['country_suppliner_status'] = (isset($this->data['TravelCountrySupplier']['country_suppliner_status'])) ? $this->data['TravelCountrySupplier']['city_supplier_status'] : '';
        }
        if (!isset($this->passedArgs['exclude']) && empty($this->passedArgs['exclude'])) {
            $this->passedArgs['exclude'] = (isset($this->data['TravelCountrySupplier']['exclude'])) ? $this->data['TravelCountrySupplier']['exclude'] : '';
        }
        if (!isset($this->passedArgs['province_id']) && empty($this->passedArgs['province_id'])) {
            $this->passedArgs['province_id'] = (isset($this->data['TravelCountrySupplier']['province_id'])) ? $this->data['TravelCountrySupplier']['province_id'] : '';
        }



        if (!isset($this->data) && empty($this->data)) {
            $this->data['TravelCountrySupplier']['search'] = $this->passedArgs['search'];
            $this->data['TravelCountrySupplier']['active'] = $this->passedArgs['active'];
            $this->data['TravelCountrySupplier']['wtb_status'] = $this->passedArgs['wtb_status'];
            $this->data['TravelCountrySupplier']['pf_country_code'] = $this->passedArgs['pf_country_code'];

            $this->data['TravelCountrySupplier']['country_suppliner_status'] = $this->passedArgs['country_suppliner_status'];
            $this->data['TravelCountrySupplier']['exclude'] = $this->passedArgs['exclude'];
            $this->data['TravelCountrySupplier']['province_id'] = $this->passedArgs['province_id'];
        }
        $TravelActionItemTypes = $this->TravelActionItemType->find('list', array('fields' => 'id, value', 'order' => 'value ASC'));

        $this->set(compact('search', 'supplier_code', 'country_wtb_code', 'wtb_status', 'city_wtb_code', 'active', 'TravelActionItemTypes', 'status', 'exclude', 'TravelMappingTypes', 'mapping_type', 'province_id', 'Provinces'));
    }
    
    public function mismatch_hotel_count($country_id = null, $city_id = null){
        
        $this->layout = '';
        
        $TravelHotelLookups = $this->TravelHotelLookup->find
                (
                'all', array
            (
            'fields' => array('TravelHotelLookup.city_name', 'TravelHotelLookup.city_id', 'TravelHotelLookup.country_name', 'TravelHotelLookup.city_id', 'TravelHotelLookup.country_id', 'TravelHotelLookup.city_code', 'TravelHotelLookup.continent_name', 'TravelHotelLookup.province_name', 'COUNT(TravelHotelLookup.city_id) AS cnt'),
            
            'conditions' => array
                (                
                'TravelHotelLookup.city_id' => $city_id
            ),
            'group' => array('TravelHotelLookup.country_id','TravelHotelLookup.continent_id'),
            'order' => 'TravelHotelLookup.city_name ASC'
                )
        );
        
         
        $this->set(compact('TravelHotelLookups'));
        
        //echo count($TravelHotelLookups['TravelHotelLookup']);
        
        //$log = $this->TravelHotelLookup->getDataSource()->getLog(false, false);       
        //debug($log);
        //pr($TravelHotelLookups);
        //die;
    }
    
    public function job_report() {
        
        $user_id = $this->Auth->user('id');
        $role_id = $this->Session->read("role_id");
        $channel_id = $this->Session->read("channel_id");
        $dataArray = array();   
        $TravelCities = array();     
        $display = 'FALSE';
        $personArr = array();
        $summary = array();
        $persons = array();
        $Select = '--Select--';
        
        if($channel_id == '262'){
        $personArr = array('ProvincePermission.user_id' => $user_id);
        $summary = array('1' => 'Operation');
        $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )               
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
           }
        elseif($channel_id == '258' || $channel_id == '259') {
            $summary = array('1' => 'Operation','2' => 'Approver'); 
        }  
        elseif($channel_id == '261' || $channel_id == '214') {
            //die('fgh');;
            $summary = array('1' => 'Operation','2' => 'Approver');
            $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )               
            ),
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
        }
          
        if ($this->request->is('post') || $this->request->is('put')) {
            
            $display = 'TRUE';
            
           $data_user_id = $this->data['Report']['user_id'];
           $summary_type = $this->data['Report']['summary_type'];
           $supplier_id = $this->data['Report']['supplier_id'];
           if($channel_id == '262'){
           $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
          
           
           }
// || $channel_id == '261' add by pc as per requirment
           elseif($channel_id == '258' || $channel_id == '259' || $channel_id == '261') {
               if($summary_type == '2'){
                   $Select = 'All';
                   $personArr = array('OR' => array('ProvincePermission.approval_id' => $user_id,'ProvincePermission.maaping_approval_id' => $user_id,'ProvincePermission.user_id' => $user_id));
                   $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )               
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
                   
                   
                    if($data_user_id<>'')
                        $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
                    else
                       $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('OR' => array('ProvincePermission.approval_id' => $user_id,'ProvincePermission.maaping_approval_id' => $user_id,'ProvincePermission.user_id' => $user_id)))); 
               }
               else
                   $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
        }
          
//echo '<pre>';
//print_r($ProvincePermissions);die;
           foreach($ProvincePermissions as $ProvincePermission){
               array_push($dataArray, array('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id'],'user_id' => $ProvincePermission['ProvincePermission']['user_id']
			   ,'approval_id' => $ProvincePermission['ProvincePermission']['approval_id'],'maaping_approval_id' => $ProvincePermission['ProvincePermission']['maaping_approval_id']
			   
			   ));
               //$dataArray = ARRAY('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id']);
               
           }
          $i=0;
           foreach($dataArray as $val){
          $this->TravelCity->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier','TravelCitySupplier','TravelArea','TravelHotelLookup','TravelSuburb'))
            );
               $TravelCities[] = $this->TravelCity->find('first',array('fields' => 'id,city_name','conditions' => array('province_id' => $val['province_id'])));
              
               array_push($TravelCities[$i], array('province_id' => $val['province_id'],'country_id' => $val['country_id'],'user_id' => $val['user_id'],'approval_id' => $val['approval_id'],'maaping_approval_id' => $val['maaping_approval_id'])); 
              
                $conArray[] = array('TravelHotelLookup.city_id' => $TravelCities[$i]['TravelCity']['id'],'TravelHotelLookup.country_id' => $val['country_id']);
                ///$andArray = array('province_id' => $val['province_id'],'country_id' => $val['country_id']);
                $i++;
              
           }         
        }
        
        
        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'id,supplier_code', 'order' => 'supplier_code ASC'));
        
        
        $this->set(compact('persons','TravelCities','TravelSuppliers','display','summary','Select','channel_id'));
    }
	
	public function my_activity_report(){

        $user_id = $this->Auth->user('id');
        $role_id = $this->Session->read("role_id");
		//echo '<Br>';
        $channel_id = $this->Session->read("channel_id"); 
        $dataArray = array(); 
        $TravelCities = array();
        $display = 'FALSE';
        $personArr = array();
        $summary = array();
        $persons = array();
        $Select = '--Select--';
//        if($channel_id == '262'){
        if($role_id == '65' || $role_id == '28') {
        $personArr = array('ProvincePermission.user_id' => $user_id);
        $summary = array('1' => 'Operation');
        $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                ) 
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
           }

//        elseif($channel_id == '258' || $channel_id == '259') {
        elseif($role_id == '61' || $role_id == '62') {
        $summary = array('2' => 'Approver');
        if($role_id == '61') {
            $personArr = array('OR' => array('ProvincePermission.maaping_approval_id' => $user_id));
            $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
			'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.maaping_approval_id = User.id')
                ) 
            ),
            'conditions' => $personArr,
            'group' => 'User.id'));
             $persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));	
        }  elseif($role_id == '62') { 
            $personArr = array('OR' => array('ProvincePermission.approval_id' => $user_id));
            $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.approval_id = User.id')
                ) 
            ),
            'conditions' => $personArr,
            'group' => 'User.id'));
             $persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname')); 			
         }  
        }  

//        elseif($channel_id == '261' || $channel_id == '214') {
        elseif($role_id == '64') { 
//            $Select = 'All';
				$personArr = array();            
            $summary = array('1' => 'Operation','2' => 'Approver');
/*            
            $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
//					 'type' => 'LEFT',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id') 
                ) 
            ), 
            'group' => 'ProvincePermission.user_id'));
			//echo '<pre>';
			//print_r($persons);die;
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
*/
        }
        elseif($role_id == '68') {
            $summary = array('1' => 'Operation');
            $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )
            ),
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));
        }
	
        $data_choose_date= '';
        if ($this->request->is('post') || $this->request->is('put')) {
           $display = 'TRUE';
           $data_user_id = $this->data['Report']['user_id'];
           $summary_type = $this->data['Report']['summary_type'];
           $supplier_id = $this->data['Report']['supplier_id']; 
           $data_choose_date= $this->data['Report']['choose_date'];
//           if($channel_id == '262'){
        if($role_id == '65' || $role_id == '28') {           
           $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
          }
// || $channel_id == '261' add by pc as per requirment
//           elseif($channel_id == '258' || $channel_id == '259' || $channel_id == '261') {
		elseif($role_id == '61' || $role_id == '62') {	          
               if($summary_type == '2'){
                   $Select = 'All';
                   $personArr = array('OR' => array('ProvincePermission.approval_id' => $data_user_id,'ProvincePermission.maaping_approval_id' => $data_user_id,'ProvincePermission.user_id' => $data_user_id));
                   $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                ) 
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname')); 
                    if($data_user_id<>'')
                        $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
                    else
                       $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('OR' => array('ProvincePermission.approval_id' => $data_user_id,'ProvincePermission.maaping_approval_id' => $data_user_id,'ProvincePermission.user_id' => $data_user_id)))); 
               }
               else
                   $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
	} 
        elseif($role_id == '64' || $role_id == '68') {		   
				$personArr = array();
				$Select = 'All';
               if($summary_type == '2'){  //for approvel
				
			     $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'), 
				   'joins' => array(
						array(
							'table' => 'users',
							'alias' => 'User',
							'conditions' => array(
												'OR' => array(
													'ProvincePermission.approval_id = User.id',
													'ProvincePermission.maaping_approval_id = User.id'))
						)
					),

            'conditions' => $personArr,
            'group' => 'User.id',
            'order' => 'User.fname ASC'));
			
			} else{ //for opration
				$persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
                       'joins' => array(     
                       array(
								'table' => 'users',
                                'alias' => 'User',
                                'conditions' => array(
                                                    'ProvincePermission.user_id = User.id')
                            )
                        ),
                        'conditions' => $personArr,
                        'group' => 'User.id',
                        'order' => 'User.fname ASC'));	
			}			
				$all_users = Set::combine($persons, '{n}.User.id', array('%s', '{n}.User.id'));				
				 $user_str = implode(',',$all_users); 				
				 $user_arr =  explode(',',$user_str);
			
	
					$persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));  
  			
                    if($data_user_id ==''){
                        $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $user_arr)));
						
					}else{
                       $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('ProvincePermission.user_id' => $data_user_id))); 
					}

			
			
				

	}
		
        
        
//        $dataArray = array();
			foreach($ProvincePermissions as $ProvincePermission){
               array_push($dataArray, array('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id'],'user_id' => $ProvincePermission['ProvincePermission']['user_id']
			   ,'approval_id' => $ProvincePermission['ProvincePermission']['approval_id'],'maaping_approval_id' => $ProvincePermission['ProvincePermission']['maaping_approval_id']
			   
			   ));
               //$dataArray = ARRAY('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id']);
           }
//echo '<pre>';
//print_r($dataArray); die;
          $i=0;
           foreach($dataArray as $val){
          $this->TravelCity->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier','TravelCitySupplier','TravelArea','TravelHotelLookup','TravelSuburb'))
            );
			$AllCitiesProvinces = $this->TravelCity->find('all',array('fields' => 'id,city_name','conditions' => array('province_id' => $val['province_id'])));
			
			foreach($AllCitiesProvinces as $AllCitiesProvince)
			{	
				$TravelCities[$i]['TravelCity']['id'] =$AllCitiesProvince['TravelCity']['id'];
				$TravelCities[$i]['TravelCity']['city_name'] =$AllCitiesProvince['TravelCity']['city_name'];
				
				// $TravelCities[$i] = array('id'=>$AllCitiesProvince['TravelCity']['id'],'city_name'=>$AllCitiesProvince['TravelCity']['city_name']);
				 array_push($TravelCities[$i], array('province_id' => $val['province_id'],'country_id' => $val['country_id'],'user_id' => $val['user_id'],'approval_id' => $val['approval_id'],'maaping_approval_id' => $val['maaping_approval_id'])); 
				    $i++;
			}
 }            
/*          
           foreach($dataArray as $val){
          $this->TravelCity->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier','TravelCitySupplier','TravelArea','TravelHotelLookup','TravelSuburb'))
            );
               $TravelCities[] = $this->TravelCity->find('first',array('fields' => 'id,city_name','conditions' => array('province_id' => $val['province_id'])));
               array_push($TravelCities[$i], array('province_id' => $val['province_id'],'country_id' => $val['country_id'],'user_id' => $val['user_id'],'approval_id' => $val['approval_id'],'maaping_approval_id' => $val['maaping_approval_id'])); 
			   
                $conArray[] = array('TravelHotelLookup.city_id' => $TravelCities[$i]['TravelCity']['id'],'TravelHotelLookup.country_id' => $val['country_id']);
                ///$andArray = array('province_id' => $val['province_id'],'country_id' => $val['country_id']);
                $i++;
           } 
*/           
        }
        
        
        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'id,supplier_code', 'order' => 'supplier_code ASC'));
		$ChooseDate = array('today' => 'Today','yesterday' => 'Yesterday','this_week' => 'This Week','this_month' => 'This Month','this_year' => 'This Year','last_year' => 'Last Year');
        $this->set(compact('persons','TravelCities','TravelSuppliers','display','summary','Select','channel_id','ChooseDate','data_choose_date'));
    }
    public function summary_report() {
        $user_id = $this->Auth->user('id');
        $role_id = $this->Session->read("role_id");
        $channel_id = $this->Session->read("channel_id");
        $dataArray = array();   
		
        $TravelCities = array();
        $display = 'FALSE';
        $personArr = array();
        $summary = array();
        $persons = array();
        $Select = '--Select--';
	$logged_user = ''; 
	$logged_user = $user_id;         
//        if($channel_id == '262'){
        if($role_id == '65' || $role_id == '28') {	
        $personArr = array('ProvincePermission.user_id' => $user_id);
        $summary = array('1' => 'Operation');
        $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )   
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
           }
//        elseif($channel_id == '258' || $channel_id == '259') {
        elseif($role_id == '61' || $role_id == '62') {
            $summary = array('1' => 'Operation','2' => 'Approver');
        if($role_id == '61') {
          $personArr = array('OR' => array('ProvincePermission.maaping_approval_id' => $user_id));
          $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.maaping_approval_id = User.id')
                ) 
            ),
            'conditions' => $personArr,
            'group' => 'User.id'));
             $persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname')); 			
        }  elseif($role_id == '62') {
            $personArr = array('OR' => array('ProvincePermission.approval_id' => $user_id));
            $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.approval_id = User.id')
                )               
            ),
            'conditions' => $personArr,
            'group' => 'User.id'));
             $persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname')); 			
         }

        }  
//        elseif($channel_id == '261' || $channel_id == '214') {
        elseif($role_id == '64') {  
				$personArr = array();		
            $summary = array('1' => 'Operation','2' => 'Approver');
           
        }
        elseif($role_id == '68') {
            $summary = array('1' => 'Operation');
            $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
						)   
            ),
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));
        } 
		
		
        if ($this->request->is('post') || $this->request->is('put')) {
           $display = 'TRUE';
           $data_user_id = $this->data['Report']['user_id'];
           $summary_type = $this->data['Report']['summary_type'];
           $supplier_id = $this->data['Report']['supplier_id'];
        if($role_id == '65' || $role_id == '28') {				   
           $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
           }
		elseif($role_id == '61' || $role_id == '62') {			   
               if($summary_type == '2'){
                   $Select = 'All';
                   $personArr = array('OR' => array('ProvincePermission.approval_id' => $data_user_id,'ProvincePermission.maaping_approval_id' => $data_user_id,'ProvincePermission.user_id' => $data_user_id));
                   $persons = $this->ProvincePermission->find('all', array('fields' => array('ProvincePermission.user_id', 'User.fname','User.lname'),
           'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'conditions' => array(
                        'ProvincePermission.user_id = User.id')
                )         
            ),
            'conditions' => $personArr,
            'group' => 'ProvincePermission.user_id'));
             $persons = Set::combine($persons, '{n}.ProvincePermission.user_id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));   
                    if($data_user_id<>'')
                        $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));	
					else
                       $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('OR' => array('ProvincePermission.approval_id' => $data_user_id,'ProvincePermission.maaping_approval_id' => $data_user_id,'ProvincePermission.user_id' => $data_user_id)))); 
			}
			else
				$ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $data_user_id)));
	} elseif($role_id == '64' || $role_id == '68') {		   
				$personArr = array();
				$Select = 'All';
               if($summary_type == '2'){  //for approvel
				
			     $persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'), 
				   'joins' => array(
						array(
							'table' => 'users',
							'alias' => 'User',
							'conditions' => array(
												'OR' => array(
													'ProvincePermission.approval_id = User.id',
													'ProvincePermission.maaping_approval_id = User.id'))
						)
					),

            'conditions' => $personArr,
            'group' => 'User.id',
            'order' => 'User.fname ASC'));
			
			} else{ //for opration
				$persons = $this->ProvincePermission->find('all', array('fields' => array('User.id', 'User.fname','User.lname'),
                       'joins' => array(     
                       array(
								'table' => 'users',
                                'alias' => 'User',
                                'conditions' => array(
                                                    'ProvincePermission.user_id = User.id')
                            )
                        ),
                        'conditions' => $personArr,
                        'group' => 'User.id',
                        'order' => 'User.fname ASC'));	
			}			
				$all_users = Set::combine($persons, '{n}.User.id', array('%s', '{n}.User.id'));				
				 $user_str = implode(',',$all_users); 				
				 $user_arr =  explode(',',$user_str);
			
	
					$persons = Set::combine($persons, '{n}.User.id', array('%s %s', '{n}.User.fname', '{n}.User.lname'));  
  			
                    if($data_user_id ==''){
                        $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('user_id' => $user_arr)));
						
					}else{
                       $ProvincePermissions = $this->ProvincePermission->find('all',array('conditions' => array('ProvincePermission.user_id' => $data_user_id))); 
					}

			
			
				

	}

           foreach($ProvincePermissions as $ProvincePermission){
               array_push($dataArray, array('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id'],'user_id' => $ProvincePermission['ProvincePermission']['user_id']
			   ,'approval_id' => $ProvincePermission['ProvincePermission']['approval_id'],'maaping_approval_id' => $ProvincePermission['ProvincePermission']['maaping_approval_id']
			   ));
               //$dataArray = ARRAY('province_id' => $ProvincePermission['ProvincePermission']['province_id'],'country_id' => $ProvincePermission['ProvincePermission']['country_id']);
           }
          $i=0;
           foreach($dataArray as $val){
          $this->TravelCity->unbindModel(
                array('hasMany' => array('TravelHotelRoomSupplier','TravelCitySupplier','TravelArea','TravelHotelLookup','TravelSuburb'))
            );
			$AllCitiesProvinces = $this->TravelCity->find('all',array('fields' => 'id,city_name','conditions' => array('province_id' => $val['province_id'])));
			
			foreach($AllCitiesProvinces as $AllCitiesProvince)
			{	
				$TravelCities[$i]['TravelCity']['id'] =$AllCitiesProvince['TravelCity']['id'];
				$TravelCities[$i]['TravelCity']['city_name'] =$AllCitiesProvince['TravelCity']['city_name'];
				
				// $TravelCities[$i] = array('id'=>$AllCitiesProvince['TravelCity']['id'],'city_name'=>$AllCitiesProvince['TravelCity']['city_name']);
				 array_push($TravelCities[$i], array('province_id' => $val['province_id'],'country_id' => $val['country_id'],'user_id' => $val['user_id'],'approval_id' => $val['approval_id'],'maaping_approval_id' => $val['maaping_approval_id'])); 
				    $i++;
			}
 }         

}

        $TravelSuppliers = $this->TravelSupplier->find('list', array('fields' => 'id,supplier_code', 'order' => 'supplier_code ASC'));
        $this->set(compact('persons','TravelCities','TravelSuppliers','display','summary','Select','channel_id','logged_user','role_id'));

    }
    public function duplicate_hotel_report() {


        $TravelCities = array();
        $TravelCountries = array();
        $Provinces= array();
        $search_condition = array();
        $continent_id = '';
        $country_id = '';
        $province_id = '';
        $city_id = '';
        $flag = 0;
       
        

        if ($this->request->is('post') || $this->request->is('put')) {
            
            
            if (!empty($this->data['Report']['continent_id'])) {
                $continent_id = $this->data['Report']['continent_id'];
                array_push($search_condition, array('TravelHotelLookup.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id, country_name', 'conditions' => array('TravelCountry.continent_id' => $continent_id,
                        'TravelCountry.country_status' => '1',
                        'TravelCountry.wtb_status' => '1',
                        'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));
            }

            if (!empty($this->data['Report']['country_id'])) {
                $country_id = $this->data['Report']['country_id'];
                $province_id = $this->data['Report']['province_id'];
                array_push($search_condition, array('TravelHotelLookup.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id, city_name', 'conditions' => array('TravelCity.province_id' => $province_id,
                        'TravelCity.city_status' => '1',
                        'TravelCity.wtb_status' => '1',
                        'TravelCity.active' => 'TRUE',), 'order' => 'city_name ASC'));
                
                
            }
            if (!empty($this->data['Report']['province_id'])) {
                
                array_push($search_condition, array('TravelHotelLookup.province_id' => $province_id));
                $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.continent_id' => $continent_id,
                    'Province.status' => '1',
                    'Province.wtb_status' => '1',
                    'Province.active' => 'TRUE',
                    
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));
            }
            if (!empty($this->data['Report']['city_id'])) {
                $city_id = $this->data['Report']['city_id'];
                array_push($search_condition, array('TravelHotelLookup.city_id' => $city_id));
            }
            
          $flag = 1;
        }
        
        elseif (count($this->request->params['named'])) {

           //die;

            if (!empty($this->request->params['named']['continent_id'])) {
                $continent_id = $this->request->params['named']['continent_id'];
                array_push($search_condition, array('TravelHotelLookup.continent_id' => $continent_id));
                $TravelCountries = $this->TravelCountry->find('list', array('fields' => 'id, country_name', 'conditions' => array('TravelCountry.continent_id' => $continent_id,
                        'TravelCountry.country_status' => '1',
                        'TravelCountry.wtb_status' => '1',
                        'TravelCountry.active' => 'TRUE'), 'order' => 'country_name ASC'));
            }

            if (!empty($this->request->params['named']['country_id'])) {
                $country_id = $this->request->params['named']['country_id'];
                $province_id = $this->request->params['named']['province_id'];
                array_push($search_condition, array('TravelHotelLookup.country_id' => $country_id));
                $TravelCities = $this->TravelCity->find('list', array('fields' => 'id, city_name', 'conditions' => array('TravelCity.province_id' => $province_id,
                       ), 'order' => 'city_name ASC'));
            }
            if (!empty($this->request->params['named']['province_id'])) {

                array_push($search_condition, array('TravelHotelLookup.province_id' => $province_id));
                $Provinces = $this->Province->find('list', array(
                'conditions' => array(
                    'Province.country_id' => $country_id,
                    'Province.continent_id' => $continent_id,
                    'Province.status' => '1',
                    'Province.wtb_status' => '1',
                    'Province.active' => 'TRUE',
                    
                ),
                'fields' => array('Province.id', 'Province.name'),
                'order' => 'Province.name ASC'
            ));
            }

            if (!empty($this->request->params['named']['city_id'])) {
                $city_id = $this->request->params['named']['city_id'];
                array_push($search_condition, array('TravelHotelLookup.city_id' => $city_id));
                
            }
           $flag = 1;
        }   
        
        
        if($flag == 1){
         $this->paginate['order'] = array('TravelHotelLookup.city_code' => 'asc');
            $this->paginate['limit'] = '50';
            $this->set('TravelHotelLookups', $this->paginate("TravelHotelLookup", $search_condition));
        }
      
        
        if (!isset($this->passedArgs['continent_id']) && empty($this->passedArgs['continent_id'])) {
            if(isset($this->data['Report']['continent_id']) && !empty($this->data['Report']['continent_id'])) 
                $this->passedArgs['continent_id'] =  $this->data['Report']['continent_id'];
        }
        if (!isset($this->passedArgs['country_id']) && empty($this->passedArgs['country_id'])) {
            if(isset($this->data['Report']['country_id']) && !empty($this->data['Report']['country_id'])) 
                $this->passedArgs['country_id'] =  $this->data['Report']['country_id'];
        }
        if (!isset($this->passedArgs['province_id']) && empty($this->passedArgs['province_id'])) {
             if(isset($this->data['Report']['province_id']) && !empty($this->data['Report']['province_id'])) 
                 $this->passedArgs['province_id'] = $this->data['Report']['province_id'];
        }
        if (!isset($this->passedArgs['city_id']) && empty($this->passedArgs['city_id'])) {
            if(isset($this->data['Report']['city_id']) && !empty($this->data['Report']['city_id'])) 
                $this->passedArgs['city_id'] =  $this->data['Report']['city_id'];
        }
        
        if (!isset($this->data) && empty($this->data)) {
           
            $this->data['Report']['continent_id'] = $this->passedArgs['continent_id'];
            $this->data['Report']['country_id'] = $this->passedArgs['country_id'];
            $this->data['Report']['province_id'] = $this->passedArgs['province_id'];
            $this->data['Report']['city_id'] = $this->passedArgs['city_id'];
            
        }
        
        
        $TravelLookupContinents = $this->TravelLookupContinent->find('list', array('fields' => 'id,continent_name', 'conditions' => array('continent_status' => 1, 'wtb_status' => 1, 'active' => 'TRUE'), 'order' => 'continent_name ASC'));
        
        $this->set(compact('TravelLookupContinents','TravelCountries','TravelCities','Provinces',
                'continent_id','country_id','province_id','city_id'));
    }
}

