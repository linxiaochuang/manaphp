<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/1
 * Time: 22:31
 */
namespace Application\Home\Models;
use \ManaPHP\Mvc\Model;
class Film extends Model{
    public $film_id;
    public $title;
    public $description;
    public $release_year;
    public $language_id;
    public $original_language_id;
    public $rental_duration;
    public $rental_rate;
    public $length;
    public $replacement_cost;
    public $rating;
    public $special_features;
    public $last_update;

    public function getSource(){
        return 'film';
    }
}