<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct($groups = array())
	{
		parent::__construct();

        header('Expires: Wed, 13 Dec 1972 18:37:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		header('Pragma: no-cache');

        if(!empty($groups)){
            $this->authorization->is_member($groups, TRUE);
        }
	}

    //Use this functio to check whether REFERENCED vars are empty (needed for pre 5.5 PHP)
    public function _empty($val){
        return empty($val);
    }

}
