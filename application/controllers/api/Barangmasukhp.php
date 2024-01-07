<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Barangmasukhp extends Auth 
{
    function __construct()
    {
        parent::__construct(); 
        $this->cek_token();
    }

    function list_barangmasukhp()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_masukhp');
        if($search)
        {
            $this->db->like('tgl_masuk', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('d.*,b.*,k.kategori')->from('tb_masukhp d')->join('tb_baranghp b','d.id_baranghp=b.id_baranghp')->join('tb_kategori k','b.id_kategori=k.id_kategori')->order_by('d.id_masukhp', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('b.kode_hp', $search)->or_like('b.barang', $search)->or_like('k.kategori', $search)->or_like('b.tgl_masuk', $search)->or_like('b.jml_masuk', $search)->or_like('b.satuan', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listbarangmasukhp()
    {
        $q = $this->db->select('d.*,b.*,k.kategori')->from('tb_masukhp d')->join('tb_baranghp b','d.id_baranghp=b.id_baranghp')->join('tb_kategori k','b.id_kategori=k.id_kategori')->order_by('d.id_masukhp', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_barangmasukhp()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('id_baranghp', 'ID Barang', 'required|numeric');
            $this->form_validation->set_rules('tgl_masuk', 'Tgl Masuk', 'required');
            $this->form_validation->set_rules('jml_masuk', 'Jml Masuk', 'numeric|numeric');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data_insert = [
                    'id_baranghp' => $param['id_baranghp'],
                    'tgl_masuk' => $param['tgl_masuk'],
                    'jml_masuk' => $param['jml_masuk']
                ];  

                $q = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp',$param['id_baranghp'])->get()->row();
                $stok = $q->stok + $param['jml_masuk'];
                $data_update = [
                    'stok' => $stok
                ];
                $this->db->trans_start();
                $this->db->insert('tb_masukhp', $data_insert);
                $this->db->update('tb_baranghp', $data_update, ['id_baranghp'=>$param['id_baranghp']]);
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

    function get_barangmasukhp_by_id($id_masukhp)
	{	
        $q = $this->db->get_where('tb_masukhp', ['id_masukhp'=>$id_masukhp])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function hapus_barangmasukhp($id_masukhp)
    {   
        // ambil id_baranghp dan stok dari tb_masukhp
        $q1 = $this->db->select('id_masukhp,id_baranghp,jml_masuk')->from('tb_masukhp')->where('id_masukhp',$id_masukhp)->get()->row();
        $id_baranghp = $q1->id_baranghp;
        //ambil stok dari tb_baranghp
        $q2 = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp',$id_baranghp)->get()->row();
        //set stok
        $stok = $q2->stok - $q1->jml_masuk;
        $data_update = [
            'stok' => $stok
        ];
        $this->db->trans_start();
        $this->db->update('tb_baranghp',$data_update,['id_baranghp'=>$id_baranghp]);
        $this->db->where('id_masukhp',$id_masukhp)->delete('tb_masukhp');
        $this->db->trans_complete();
        if($this->db->trans_status() == TRUE)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }
    }

}
