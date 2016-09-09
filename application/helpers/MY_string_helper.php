<?php
defined('BASEPATH') or exit('No direct script access allowed');

function null_or_empty($value){
    return (!$value || $value==='');
}