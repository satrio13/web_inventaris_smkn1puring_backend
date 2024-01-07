<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Tanah extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_tanah()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_tanah');
        if($search)
        {
            $this->db->like('tanah', $search)->or_like('luas', $search)->or_like('selatan', $search)->or_like('timur', $search)->or_like('barat', $search)->or_like('utara', $search)->or_like('tahun_p', $search)->or_like('sumberdana', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_tanah')->order_by('id_tanah', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('tanah', $search)->or_like('luas', $search)->or_like('selatan', $search)->or_like('timur', $search)->or_like('barat', $search)->or_like('utara', $search)->or_like('tahun_p', $search)->or_like('sumberdana', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listtanah()
    {
        $q = $this->db->select('*')->from('tb_tanah')->order_by('id_tanah', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_tanah()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('tanah', 'Nama Tanah', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'tanah' => $param['tanah'],
                    'luas' => $param['luas'],
                    'selatan' => $param['selatan'],
                    'timur' => $param['timur'],                    
                    'barat' => $param['barat'],
                    'utara' => $param['utara'],
                    'tahun_p' => $param['tahun_p'],
                    'sumberdana' => $param['sumberdana']
                ];

                $this->db->insert('tb_tanah', $data);
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

    function edit_tanah($id_tanah)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('tanah', 'Nama Tanah', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'tanah' => $param['tanah'],
                    'luas' => $param['luas'],
                    'selatan' => $param['selatan'],
                    'timur' => $param['timur'],                    
                    'barat' => $param['barat'],
                    'utara' => $param['utara'],
                    'tahun_p' => $param['tahun_p'],
                    'sumberdana' => $param['sumberdana']
                ];

                $this->db->update('tb_tanah', $data, ['id_tanah'=>$id_tanah]);
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

    function get_tanah_by_id($id_tanah)
	{	
        $q = $this->db->get_where('tb_tanah', ['id_tanah'=>$id_tanah])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function hapus_tanah($id_tanah)
    {    
        $this->db->delete('tb_tanah', ['id_tanah'=>$id_tanah]);
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }    
    }

}
