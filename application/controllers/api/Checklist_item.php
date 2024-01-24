<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Checklist_item extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function get_item_by_checklist_id($id_checklist)
    {
        $q = $this->db->select('i.*,c.name as checklist_name')->from('tb_checklist_item i')->join('tb_checklist c','i.id_checklist=c.id_checklist')->where('i.id_checklist', $id_checklist)->order_by('i.id_item','desc')->get();
        return $this->response(['status' => true, 'data' => $q->result()]);
    }

    function create($id_checklist)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['itemName']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('itemName', 'Item Name', 'required|max_length[100]');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'id_checklist' => $id_checklist,
                    'itemName' => $param['itemName']    
                ];

                $this->db->insert('tb_checklist_item', $data);
                if($this->db->affected_rows() > 0)
                {
                    return $this->response(['status' => true, 'message' => 'Data Berhasil Disimpan']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Data Gagal Disimpan!'], 400);
                }
            }
        }
    }

    function get_item_by_checklist_id_and_item_id($id_checklist, $id_item)
    {
        $q = $this->db->select('i.*,c.name as checklist_name')->from('tb_checklist_item i')->join('tb_checklist c','i.id_checklist=c.id_checklist')->where('i.id_checklist', $id_checklist)->where('i.id_item', $id_item)->order_by('i.id_item','desc')->get();
        return $this->response(['data' => $q->result()]);
    }

    function update_status_item_by_checklist_id_and_item_id($id_checklist, $id_item)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['itemName']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('itemName', 'Item Name', 'required|max_length[100]');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'id_checklist' => $id_checklist  
                ];

                $this->db->update('tb_checklist_item', $data, ['id_item' => $id_item]);
                if($this->db->affected_rows() > 0)
                {
                    return $this->response(['status' => true, 'message' => 'Data Berhasil Diupdate']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
                }
            }
        }
    }
    
    function delete_item_by_checklist_id_and_item_id($id_checklist, $id_item)
    {
        $cek = $this->db->get_where('tb_checklist_item', ['id_item' => $id_item])->row();
        if(!$cek)
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan!'], 400);
        }else
        {
            $this->db->delete('tb_checklist_item', ['id_item' => $id_item]);
            if($this->db->affected_rows() > 0)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
            }
        }    
    }
    
    function rename_status_item_by_checklist_id_and_item_id($id_checklist, $id_item)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['itemName']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('itemName', 'Item Name', 'required|max_length[100]');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'itemName' => $param['itemName']    
                ];

                $this->db->update('tb_checklist_item', $data, ['id_item' => $id_item]);
                if($this->db->affected_rows() > 0)
                {
                    return $this->response(['status' => true, 'message' => 'Data Berhasil Diupdate']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
                }
            }
        }
    }

}