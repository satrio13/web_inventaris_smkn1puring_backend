<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Pengurusbarang extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function pengurusbarang()
    {
        $q = $this->db->select('p.*,u.nama,u.nip')->from('tb_pengurusbarang p')->join('tb_user u','p.id_user=u.id_user')->where('p.id',1)->get();
        return $this->response($q->result());
    }

    function list_pengurusbarang()
    {
        $q = $this->db->select('*')->from('tb_user')->order_by('nama','asc')->get();
        return $this->response($q->result());
    }

    function edit_pengurusbarang()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('id_user', 'ID User', 'required|numeric|callback_cek_id_user['.$param['id_user'].']');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'id_user' => $param['id_user']
                ];
            
                $this->db->update('tb_pengurusbarang', $data, ['id'=>1]);
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

    function get_pengurusbarang_by_id()
    {
        $q = $this->db->get_where('tb_pengurusbarang', ['id'=>1]);
        return $this->response($q->row());
    }

    function cek_id_user($id_user)
	{	
        $cek = $this->db->select('id_user')->from('tb_user')->where('id_user',$id_user)->get()->row();
        if(!$cek)
        {
			$this->form_validation->set_message('cek_id_user', 'ID User tidak terdaftar!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

}