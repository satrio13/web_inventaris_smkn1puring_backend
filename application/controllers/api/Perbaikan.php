<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Perbaikan extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_perbaikan()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_perbaikan');
        if($search)
        {
            $this->db->like('siapa', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('p.*,b.kode_inv,b.barang,b.id_kondisi,k.kondisi')->from('tb_perbaikan p')->join('tb_baranginv b','p.id_baranginv=b.id_baranginv')->join('tb_kondisi k','b.id_kondisi=k.id_kondisi')->order_by('p.id','desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('b.kode_inv', $search)->or_like('b.barang', $search)->or_like('p.siapa', $search)->or_like('p.no_hp', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listperbaikan()
    {
        $q = $this->db->select('p.*,b.kode_inv,b.barang,b.id_kondisi,k.kondisi')->from('tb_perbaikan p')->join('tb_baranginv b','p.id_baranginv=b.id_baranginv')->join('tb_kondisi k','b.id_kondisi=k.id_kondisi')->order_by('p.id','desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_perbaikan()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('id_baranginv', 'ID Barang', 'required|trim|callback_cek_id_baranginv['.$param['id_baranginv'].']');
            $this->form_validation->set_rules('id_kondisi', 'ID Kondisi Barang', 'required|trim|callback_cek_id_kondisi['.$param['id_kondisi'].']');
            $this->form_validation->set_rules('tgl', 'Tanggal diperbaiki', 'required');
            $this->form_validation->set_rules('siapa', 'Nama yang memperbaiki', 'required');
            $this->form_validation->set_rules('no_hp', 'No HP yang memperbaiki', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $this->db->trans_start();
                    $data_insert = [
                        'id_baranginv' => $param['id_baranginv'],
                        'tgl' => $param['tgl'],
                        'siapa' => $param['siapa'],
                        'no_hp' => $param['no_hp']
                    ]; 

                    $this->db->insert('tb_perbaikan', $data_insert);

                    $data_update = [
                        'id_kondisi' => $param['id_kondisi']
                    ]; 

                    $this->db->update('tb_baranginv', $data_update, ['id_baranginv' => $param['id_baranginv']]);
                $this->db->trans_complete();
                if($this->db->trans_status() == TRUE)
                {
                    return $this->response(['status' => true, 'message' => 'Data Berhasil Disimpan']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Data Gagal Disimpan!'], 400);
                }
            }
        }
    }

    function get_perbaikan_by_id($id)
	{	
        $q = $this->db->select('p.*,b.id_kondisi')->from('tb_perbaikan p')->join('tb_baranginv b','p.id_baranginv=b.id_baranginv')->where('p.id',$id)->get()->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function edit_perbaikan($id)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('id_baranginv', 'ID Barang', 'required|trim|callback_cek_id_baranginv['.$param['id_baranginv'].']');
            $this->form_validation->set_rules('id_kondisi', 'ID Kondisi Barang', 'required|trim|callback_cek_id_kondisi['.$param['id_kondisi'].']');
            $this->form_validation->set_rules('tgl', 'Tanggal diperbaiki', 'required');
            $this->form_validation->set_rules('siapa', 'Nama yang memperbaiki', 'required');
            $this->form_validation->set_rules('no_hp', 'No HP yang memperbaiki', 'required');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $this->db->trans_start();                
                    $data = [
                        'tgl' => $param['tgl'],
                        'siapa' => $param['siapa'],
                        'no_hp' => $param['no_hp']
                    ]; 

                    $this->db->update('tb_perbaikan', $data, ['id'=>$id]);

                     $data_update = [
                        'id_kondisi' => $param['id_kondisi']
                    ]; 

                    $this->db->update('tb_baranginv', $data_update, ['id_baranginv' => $param['id_baranginv']]);
                $this->db->trans_complete();
                if($this->db->trans_status() == TRUE)
                {
                    return $this->response(['status' => true, 'message' => 'Data Berhasil Diupdate']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
                }
            }
        }
    }
        
    function hapus_perbaikan($id)
    {    
        $this->db->delete('tb_perbaikan', ['id'=>$id]);
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }    
    }

    function cek_id_baranginv($id_baranginv)
	{	
        $cek = $this->db->get_where('tb_baranginv', ['id_baranginv'=>$id_baranginv])->row();
        if(!$cek)
        {
			$this->form_validation->set_message('cek_id_baranginv', 'ID Barang tidak terdaftar!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

    function cek_id_kondisi($id_kondisi)
	{	
        $cek = $this->db->get_where('tb_kondisi', ['id_kondisi'=>$id_kondisi])->row();
        if(!$cek)
        {
			$this->form_validation->set_message('cek_id_kondisi', 'ID Kondisi Barang tidak terdaftar!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

}
