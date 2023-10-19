<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Kategori extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_kategori()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = $this->input->get('search');
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_kategori');
        if($search)
        {
            $this->db->like('kategori', $search)->or_like('id_kategori', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_kategori')->order_by('id_kategori', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('kategori', $search)->or_like('id_kategori', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listkategori()
    {
        $q = $this->db->select('*')->from('tb_kategori')->order_by('kategori', 'asc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_kategori()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kategori', 'Kategori', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kategori' => $param['kategori']
                ];

                $this->db->insert('tb_kategori', $data);
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

    function edit_kategori($id_kategori)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kategori', 'Kategori', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kategori' => $param['kategori']
                ];

                $this->db->update('tb_kategori', $data, ['id_kategori'=>$id_kategori]);
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

    function get_kategori_by_id($id_kategori)
	{	
        $q = $this->db->get_where('tb_kategori', ['id_kategori'=>$id_kategori])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function cek_id_kategori($id_kategori)
	{	
        $q = $this->db->get_where('tb_kategori', ['id_kategori'=>$id_kategori])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q]);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function hapus_kategori($id_kategori)
    {    
        $cek_baranghp = $this->db->select('id_kategori')->from('tb_baranghp')->where('id_kategori',$id_kategori)->get()->num_rows();
        $cek_baranginv = $this->db->select('id_kategori')->from('tb_baranginv')->where('id_kategori',$id_kategori)->get()->num_rows();
        if($cek_baranghp > 0 OR $cek_baranginv > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
        }else
        {
            $this->db->delete('tb_kategori', ['id_kategori'=>$id_kategori]);
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