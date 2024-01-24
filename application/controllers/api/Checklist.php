<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Checklist extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function index()
    {
        $q = $this->db->select('*')->from('tb_checklist')->order_by('id_checklist','desc')->get();
        return $this->response(['data' => $q->result()]);
    }

    function create()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['name']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[100]');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'name' => $param['name']    
                ];

                $this->db->insert('tb_checklist', $data);
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

    function delete($id_checklist)
    {    
        $cek = $this->db->get_where('tb_checklist', ['id_checklist' => $id_checklist])->row();
        if(!$cek)
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan!'], 400);
        }else
        {
            $this->db->delete('tb_checklist', ['id_checklist' => $id_checklist]);
            if($this->db->affected_rows() > 0)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
            }
        }    
    }

}