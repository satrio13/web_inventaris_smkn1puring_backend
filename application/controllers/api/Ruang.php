<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Ruang extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_ruang()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_ruang');
        if($search)
        {
            $this->db->like('ruang', $search)->or_like('nomor', $search)->or_like('nama_pj', $search)->or_like('nip_pj', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_ruang')->order_by('id_ruang', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('ruang', $search)->or_like('nomor', $search)->or_like('nama_pj', $search)->or_like('nip_pj', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listruang()
    {
        $q = $this->db->select('*')->from('tb_ruang')->order_by('id_ruang', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function list_ruang_cart()
    {
        $q = $this->db->select('*')->from('tb_ruang')->order_by('ruang', 'asc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_ruang()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('ruang', 'Ruang', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'ruang' => $param['ruang'],
                    'nomor' => $param['nomor'],
                    'nama_pj' => $param['nama_pj'],
                    'nip_pj' => $param['nip_pj']
                ];

                $this->db->insert('tb_ruang', $data);
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

    function edit_ruang($id_ruang)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('ruang', 'Ruang', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'ruang' => $param['ruang'],
                    'nomor' => $param['nomor'],
                    'nama_pj' => $param['nama_pj'],
                    'nip_pj' => $param['nip_pj']
                ];

                $this->db->update('tb_ruang', $data, ['id_ruang'=>$id_ruang]);
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

    function get_ruang_by_id($id_ruang)
	{	
        $q = $this->db->get_where('tb_ruang', ['id_ruang'=>$id_ruang])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function hapus_ruang($id_ruang)
    {    
        $cek_pindah = $this->db->select('id_ruang')->from('tb_pindah')->where('id_ruang',$id_ruang)->get()->num_rows();
        $cek_pindah_temp = $this->db->select('id_ruang')->from('tb_pindahtemp')->where('id_ruang',$id_ruang)->get()->num_rows();
        if($cek_pindah > 0 OR $cek_pindah_temp > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
        }else
        {
            $this->db->delete('tb_ruang', ['id_ruang'=>$id_ruang]);
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
