<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Kondisi extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_kondisi()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = $this->input->get('search');
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_kondisi');
        if($search)
        {
            $this->db->like('kondisi', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_kondisi')->order_by('id_kondisi', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('kondisi', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listkondisi()
    {
        $q = $this->db->select('*')->from('tb_kondisi')->order_by('kondisi', 'asc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function cek_id_kondisi($id_kondisi)
	{	
        $q = $this->db->get_where('tb_kondisi', ['id_kondisi'=>$id_kondisi])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q]);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

}