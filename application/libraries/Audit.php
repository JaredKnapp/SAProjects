<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit
{

    const DBINSERT = 'insert';
    const DBUPDATE = 'update';
    const DBDELETE = 'delete';

    protected $CI;
    protected $field_to_ignore;

    public function __construct($params)
    {
        $this->CI               = &get_instance();
        $this->field_to_ignore  = array_key_exists('ignore', $params) ? $params['ignore'] : array();

        $this->CI->load->model('AuditHistory_model', 'auditlog');
    }

    public function add_to_ignore($fieldname){
        array_push($this->field_to_ignore, $fieldname);
    }

    public function log_insert($author_id, $table, $id, $parent_id, $description)
    {
        $key = $this->_get_guid();
        $this->CI->auditlog->insert($key, $author_id, Audit::DBINSERT, $table, $id, $parent_id, NULL, NULL, $description);
        return $key;
    }

    public function log_delete($author_id, $table, $id, $parent_id)
    {
        $key = $this->_get_guid();
        $this->CI->auditlog->insert($key, $author_id, Audit::DBDELETE, $table, $id, $parent_id);
        return $key;
    }

    public function log_update($author_id, $table, $id, $parent_id, $data = array(), $old_object = NULL)
    {
        $key = $this->_get_guid();

        $old_data = get_object_vars($old_object);
        foreach($data as $field_name=>$value)
        {
            $old_value = ! array_key_exists($field_name, $old_data) ? '' : $old_data[$field_name];

            if($this->_endsWith($field_name, '_date'))
            {
                $value = $this->_cleanDate($value);
                $old_value = $this->_cleanDate($old_value);
            }

            if($old_value !== $value && ! in_array($field_name, $this->field_to_ignore)){
                $this->CI->auditlog->insert($key, $author_id, Audit::DBUPDATE, $table, $id, $parent_id, $field_name, $old_value, $value);
            }
        }

        return $key;
    }

    private function _cleanDate($date, $interval = 0)
    {
        if($date && $this->_endsWith($date, '00:00:00'))
        {
            $datetimearray = explode(' ', $date);
            $date = @$datetimearray[0];
        }
        return $date;
    }

    private function _get_guid()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    private function _endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
}