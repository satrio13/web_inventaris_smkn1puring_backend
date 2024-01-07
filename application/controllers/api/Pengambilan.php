<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Pengambilan extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function add_to_cart($kobar, $id_user)
    {
        $get = $this->db->select('id_baranghp')->from('tb_baranghp')->where('id_baranghp',$kobar)->get()->row();
        if($get)
        {   
            $cek = $this->db->select('id_baranghp,id_user')->from('tb_keluarhptemp')->where('id_baranghp',$get->id_baranghp)->where('id_user', $id_user)->get()->num_rows();
            if($cek > 0)
            {
                return $this->response(['status' => false, 'message' => 'Barang sudah berada di keranjang!'], 400);
            }else
            {
                $data = [
                    'id_baranghp' => $get->id_baranghp,
                    'qty' => 1,
                    'tgl' => tgl_jam_simpan_sekarang(),
                    'id_user' => $id_user
                ];
                
                $this->db->insert('tb_keluarhptemp', $data); 
                return $this->response(['status' => true, 'message' => 'Barang berhasil dimasukan keranjang']);
            }
        }
	}

    function add_to_cart_edit($kobar, $id_user)
    {
        $cek = $this->db->select('kode_trans')->from('tb_keluarhptemp')->where('kode_trans',$kobar)->get()->num_rows();
        if($cek > 0)
        {
            return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
        }else
        {
            $q_select = $this->db->select('*')->from('tb_keluarhptemp')->where('kode_trans != ',$kobar)->where('id_user', $id_user)->get();
            $jml = $q_select->num_rows();
            if($jml > 0)
            {
                return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
            }else
            {
                $hsl = $this->db->select('b.id_baranghp,b.kode_hp,b.barang,b.satuan,d.kode_trans,d.id_user,d.nama_pengambil,d.tgl_keluar,s.jml_keluar')
                                ->from('tb_baranghp b')
                                ->join('tb_keluarhp s','s.id_baranghp=b.id_baranghp')
                                ->join('tb_detailkeluarhp d','d.kode_trans=s.kode_trans')
                                ->where('d.kode_trans',$kobar)
                                ->where('d.id_user',$id_user)->get();
                $jml2 = $hsl->num_rows();
                if($jml2 > 0)
                {
                    $this->db->where('id_user',$id_user)->delete('tb_keluarhptemp');
                    foreach($hsl->result() as $r)
                    {   
                        $data_insert = [
                            'id_baranghp' => $r->id_baranghp,
                            'qty' => $r->jml_keluar,
                            'kode_trans' => $r->kode_trans,
                            'tgl' => tgl_jam_simpan_sekarang(),
                            'id_user' => $id_user
                        ];
                        
                        $this->db->insert('tb_keluarhptemp',$data_insert);
                    }   
                    return $this->response(['status' => true, 'message' => 'Barang berhasil dimasukan keranjang']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
                }    
            }     
        }
    }

    function data_cart($id_user)
    {
        $q = $this->db->select('d.*,b.*')->from('tb_keluarhptemp d')->join('tb_baranghp b','d.id_baranghp=b.id_baranghp')->where('d.id_user',$id_user)->order_by('b.kode_hp','asc')->get();  
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function detail_cart($kode_trans)
    {
        $q = $this->db->select('*')->from('tb_detailkeluarhp')->where('kode_trans',$kode_trans)->get();
        return $this->response(['data' => $q->row(), 'totalRecords' => $q->num_rows()]);
    }

    function list_pengambilan()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_detailkeluarhp');
        if($search)
        {
            $this->db->like('kode_trans', $search)->or_like('nama_pengambil', $search)->or_like('tgl_keluar', $search)->or_like('jam_keluar', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('d.*,u.nama')->from('tb_detailkeluarhp d')->join('tb_user u','d.id_user=u.id_user')->order_by('d.kode_trans','desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('d.kode_trans', $search)->or_like('d.nama_pengambil', $search)->or_like('d.tgl_keluar', $search)->or_like('d.jam_keluar', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function hapus_cart($id_baranghp)
    {
        $this->db->delete('tb_keluarhptemp', ['id_baranghp'=>$id_baranghp]);
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }    
    }

    function hapus_pengambilan($kode_trans)
    {   
        $cek = $this->db->select('kode_trans')->from('tb_keluarhptemp')->where('kode_trans',$kode_trans)->get()->num_rows();
        if($cek > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }else
        {
            $this->db->trans_start();
                $data = $this->db->select('*')->from('tb_keluarhp')->where('kode_trans',$kode_trans)->get();
                foreach($data->result() as $r):
                    // cek stok
                    $cek = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp', $r->id_baranghp)->get()->row();
                    $stok = $cek->stok + $r->jml_keluar;
                    $data_update = [
                        'stok' => $stok 
                    ];
                    $this->db->update('tb_baranghp',$data_update,['id_baranghp'=>$r->id_baranghp]);
                endforeach;

                $this->db->where('kode_trans',$kode_trans)->delete('tb_detailkeluarhp');
                $this->db->where('kode_trans',$kode_trans)->delete('tb_keluarhp');
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

    function delete_cart($id_user)
    {
        $this->db->where('id_user',$id_user)->delete('tb_keluarhptemp');
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }
    }

    function simpan_pengambilan()
    { 
        $param = json_decode(file_get_contents('php://input'), true);
        if(!isset($param)) 
        {
            echo "REQUEST NOT ALLOWED";
        }else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama_pengambil', 'Nama Pengambil', 'required');
            $this->form_validation->set_rules('tgl_keluar', 'Tgl Keluar', 'required');
            $this->form_validation->set_rules('jam_keluar', 'Jam Keluar', 'required');
            $this->form_validation->set_rules('id_user', 'ID User', 'required|callback_cek_id_user['.$param['id_user'].']');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $no_trans = $this->_kode_trans();
                $kode_trans = $param['kode_trans'];
                if(empty($kode_trans))
                {
                    $kode_trans = $no_trans;
                    $data = [
                        'kode_trans' => $kode_trans,
                        'nama_pengambil' => $param['nama_pengambil'],
                        'tgl_keluar' => $param['tgl_keluar'],
                        'jam_keluar' => $param['jam_keluar'],
                        'id_user' => $param['id_user']     
                    ];
                
                    $this->db->insert('tb_detailkeluarhp', $data);
                    $this->simpan_pengambilan_detail($kode_trans, $param['id_baranghp'], $param['qty'], $param['id_user']);
                }else
                {
                    $data = [
                        'nama_pengambil' => $param['nama_pengambil'],
                        'tgl_keluar' => $param['tgl_keluar'],
                        'jam_keluar' => $param['jam_keluar'] 
                    ];

                    $this->db->update('tb_detailkeluarhp', $data, ['kode_trans'=>$kode_trans]); 
                    $this->edit_pengambilan_detail($kode_trans, $param['id_baranghp'], $param['qty'], $param['id_user']);
                }  
            }
        }
    }

    function simpan_pengambilan_detail($kode_trans, $id_baranghp, $jml_keluar, $id_user)
    {
        $this->db->trans_start();
            if(count($id_baranghp) > 0)
            {   
                // insert tb_keluarhp
                $insert = [];
                foreach($id_baranghp as $key => $val)
                {
                    $insert[] = [
                        'kode_trans' => $kode_trans,
                        'id_baranghp' => $id_baranghp[$key],
                        'jml_keluar' => $jml_keluar[$key]
                    ];
                }      
                
                $this->db->insert_batch('tb_keluarhp', $insert);

                // update tb_baranghp
                $update = [];
                foreach($id_baranghp AS $key => $val)
                {
                    // cek stok
                    $r = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp', $id_baranghp[$key])->get()->row();
                
                    $update[] = [
                        'id_baranghp' => $id_baranghp[$key],
                        'stok' => $r->stok - $jml_keluar[$key]
                    ];
                }      
                $this->db->update_batch('tb_baranghp', $update, 'id_baranghp');
                $this->db->delete('tb_keluarhptemp', ['id_user'=>$id_user]);
            $this->db->trans_complete();
            if($this->db->trans_status() == TRUE)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Disimpan']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Disimpan!'], 400);
            }
        }else
        {
            return $this->response(['status' => false, 'message' => 'Keranjang Kosong, Data Gagal Disimpan!'], 400);
        }
    }

    function edit_pengambilan_detail($kode_trans, $id_baranghp, $jml_keluar, $id_user)
    {
        $this->db->trans_start();
            if(count($id_baranghp) > 0)
            {
                $this->hapus_from_cart($kode_trans);
                // insert tb_keluarhp
                $insert = [];
                foreach($id_baranghp as $key => $val)
                {
                    $insert[] = [
                        'kode_trans' => $kode_trans,
                        'id_baranghp' => $id_baranghp[$key],
                        'jml_keluar' => $jml_keluar[$key]
                    ];
                }      
                
                $this->db->insert_batch('tb_keluarhp', $insert);

                // update tb_baranghp
                $update = [];
                foreach($id_baranghp AS $key => $val)
                {
                    // cek stok
                    $r = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp', $id_baranghp[$key])->get()->row();
                
                    $update[] = [
                        'id_baranghp' => $id_baranghp[$key],
                        'stok' => $r->stok - $jml_keluar[$key]
                    ];
                }      
                $this->db->update_batch('tb_baranghp', $update, 'id_baranghp');
                $this->db->delete('tb_keluarhptemp', ['id_user'=>$id_user]);
            $this->db->trans_complete();
            if($this->db->trans_status() == TRUE)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Diupdate']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
            }
        }else
        {
            return $this->response(['status' => false, 'message' => 'Keranjang Kosong, Data Gagal Disimpan!'], 400);
        }
    }

    function hapus_from_cart($kode_trans)
    {   
        $this->db->trans_start();
            $data = $this->db->select('*')->from('tb_keluarhp')->where('kode_trans',$kode_trans)->get();
            foreach($data->result() as $r):
                // cek stok
                $cek = $this->db->select('id_baranghp,stok')->from('tb_baranghp')->where('id_baranghp', $r->id_baranghp)->get()->row();
                $stok = $cek->stok + $r->jml_keluar;
                $data_update = [
                    'stok' => $stok 
                ];
                $this->db->update('tb_baranghp',$data_update,['id_baranghp'=>$r->id_baranghp]);
            endforeach;

            $this->db->where('kode_trans',$kode_trans)->delete('tb_keluarhp');
        $this->db->trans_complete();
    }

    function detail_pengambilan($kode_trans)
    {
        $q = $this->db->select('d.*,k.*,b.kode_hp,b.barang,b.satuan,u.nama')->from('tb_detailkeluarhp d')->join('tb_keluarhp k','d.kode_trans=k.kode_trans')->join('tb_baranghp b','k.id_baranghp=b.id_baranghp')->join('tb_user u','d.id_user=u.id_user')->where('k.kode_trans',$kode_trans)->order_by('b.kode_hp','asc')->get();

        return $this->response(['data' => $q->result_array(), 'totalRecords' => $q->num_rows()]);
    }

    function cek_kode_trans($kode_trans)
    {
        $q = $this->db->get_where('tb_keluarhp', ['kode_trans'=>$kode_trans])->row();
        if($q)
        {
            return $this->response(['status' => true, 'message' => 'Kode Transaksi ditemukan']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Kode Transaksi tidak ditemukan'], 204);
        }
    }

    function cek_id_user($id_user)
	{	
        $cek = $this->db->get_where('tb_user', ['id_user'=>$id_user])->row();
        if(!$cek)
        {
			$this->form_validation->set_message('cek_id_user', 'ID User tidak terdaftar!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }
    
    private function _kode_trans()
    {
        $this->db->select('Right(tb_detailkeluarhp.kode_trans,5) as kode ',false);
        $this->db->order_by('kode_trans', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('tb_detailkeluarhp');
        if($query->num_rows() <> 0)
        {
            $data = $query->row();
            $kode = intval($data->kode)+1;
        }else
        {
            $kode = 1;
        }
        $kodemax = str_pad($kode,5,"0",STR_PAD_LEFT);
        $kodejadi  = "TR".$kodemax;
        return $kodejadi;
    }

}
