<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Gedung extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_gedung()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = $this->input->get('search');
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_gedung');
        if($search)
        {
            $this->db->like('nama_gedung', $search)->or_like('luas', $search)->or_like('tahun_p', $search)->or_like('sumberdana', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_gedung')->order_by('id_gedung', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('nama_gedung', $search)->or_like('luas', $search)->or_like('tahun_p', $search)->or_like('sumberdana', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listgedung()
    {
        $q = $this->db->select('*')->from('tb_gedung')->order_by('id_gedung', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_gedung()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama_gedung', 'Nama Gedung', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'nama_gedung' => $param['nama_gedung'],
                    'luas' => $param['luas'],
                    'tahun_p' => $param['tahun_p'],
                    'sumberdana' => $param['sumberdana']
                ];

                $this->db->insert('tb_gedung', $data);
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

    function edit_gedung($id_gedung)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama_gedung', 'Nama Gedung', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'nama_gedung' => $param['nama_gedung'],
                    'luas' => $param['luas'],
                    'tahun_p' => $param['tahun_p'],
                    'sumberdana' => $param['sumberdana']
                ];

                $this->db->update('tb_gedung', $data, ['id_gedung'=>$id_gedung]);
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

    function get_gedung_by_id($id_gedung)
	{	
        $q = $this->db->get_where('tb_gedung', ['id_gedung'=>$id_gedung])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function hapus_gedung($id_gedung)
    {    
        $this->db->delete('tb_gedung', ['id_gedung'=>$id_gedung]);
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }    
    }

}