<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $kint;
    public $web;
    public $role;
    public $gender;
    public $image_type;
    public $image;
    public $user;

    public function __construct()
    {
        parent::__construct();
    }
}