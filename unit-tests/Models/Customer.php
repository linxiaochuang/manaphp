<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/28
 * Time: 0:01
 */
namespace Models;

class Customer extends \ManaPHP\Mvc\Model{
    public $customer_id;
    public $store_id;
    public $first_name;
    public $last_name;
    public $email;
    public $address_id;
    public $active;
    public $create_date;
    public $last_update;

    public function getSource(){
        return 'customer';
    }
}
