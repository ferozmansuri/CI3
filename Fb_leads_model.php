<?php defined('BASEPATH') || exit('No direct script access allowed');

class Fb_leads_model extends BF_Model
{
    
    protected $table_name   = 'fb_leads';
    protected $key          = 'id';
    protected $soft_deletes = FALSE;
    protected $date_format  = 'datetime';
    
    //capture the user id for INSERT, UPDATE and DELETE actions
    protected $log_user     = FALSE;

    protected $set_created  = TRUE;
    protected $created_field    = 'created_on';

    protected $set_modified     = FALSE;
    protected $modified_field   = 'modified_on';
    
    // Observers
    protected $before_insert    = array();
    protected $after_insert     = array();
    protected $before_update    = array();
    protected $after_update     = array();
    protected $before_find      = array();
    protected $after_find       = array();
    protected $before_delete    = array();
    protected $after_delete     = array();

    protected $return_insert_id = true;
    protected $return_type      = 'object';
    protected $protected_attributes = array();
    protected $field_info           = array();

    protected $validation_rules         = array();
    protected $insert_validation_rules  = array();
    protected $skip_validation          = false;
    protected $empty_validation_rules   = array();
    
    public function add_if_not_exists($data)
    {
        if( $this->find_by('lead_id', $data['lead_id']) )
        return;
    
        $this->insert($data);
    }
    
    public function get_new_entries()
    {
        $fb_leads = $this->where('submit_status',0)->find_all();
        
        if( !$fb_leads )
        return [];
        
        $fb_lead_ids_array = [];
        foreach($fb_leads as $lead)
        {
            $fb_lead_ids_array[] = $lead->id;
        }
        
        $this->db->where_in('id', $fb_lead_ids_array);
        $this->db->update($this->table_name, ['submit_status'=>1]);
        
        return $fb_leads;
    }
    
    public function update_submit_status($sent_lead_ids=[], $failed_lead_ids=[])
    {
        if( !empty($sent_lead_ids) )
        {
            $this->db->where_in('id', $sent_lead_ids);
            $this->db->update($this->table_name, ['submit_status'=>2]);
        }
        
        if( !empty($failed_lead_ids) )
        {
            $this->db->where_in('id', $failed_lead_ids);
            $this->db->update($this->table_name, ['submit_status'=>3]);    
        }
    }
    
    public function get_by_ids($lead_ids)
    {
        $this->db->where_in('id', $lead_ids);
        $this->db->from($this->table_name);
        return $this->db->get()->result();
    }
}