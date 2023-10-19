<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Pemindahan extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function cek_posisi_baranginv($id_baranginv)
    {
        $q = $this->db->select('p.id_baranginv,p.id_ruang,p.status,r.ruang')->from('tb_pindah p')->join('tb_ruang r','p.id_ruang=r.id_ruang','left')->where('p.id_baranginv',$id_baranginv)->where('p.status',1)->get()->row();
        return $this->response($q);
    }

    function add_to_cart($id_baranginv, $id_user)
    {
        $get = $this->db->select('id_baranginv')->from('tb_baranginv')->where('id_baranginv', $id_baranginv)->get()->row();
        if($get)
        {
            $cek = $this->db->select('id_baranginv,id_user')->from('tb_pindahtemp')->where('id_baranginv', $get->id_baranginv)->where('id_user', $id_user)->get()->num_rows();
            if($cek > 0)
            {
                return $this->response(['status' => false, 'message' => 'Barang sudah berada di keranjang!'], 400);
            }else
            {            
                $data = [
                    'id_baranginv' => $get->id_baranginv,
                    'id_user' => $id_user
                ];
                
                $this->db->insert('tb_pindahtemp', $data);
                return $this->response(['status' => true, 'message' => 'Barang berhasil dimasukan keranjang']);
            }
        }
    }

    function add_to_cart_edit($kode_pindah, $id_user)
    {
        $cek = $this->db->select('kode_pindah')->from('tb_pindahtemp')->where('kode_pindah',$kode_pindah)->get()->num_rows();
        if($cek > 0)
        {
            return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
        }else
        {
            $q_select = $this->db->select('*')->from('tb_pindahtemp')->where('kode_pindah != ',$kode_pindah)->where('id_user',$id_user)->get();
            $jml = $q_select->num_rows();
            if($jml > 0)
            {
                return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
            }else
            {
                $hsl = $this->db->select('b.id_baranginv,b.kode_inv,b.barang,b.satuan,p.kode_pindah,p.id_user,p.id_ruang')->from('tb_baranginv b')->join('tb_pindah p','p.id_baranginv=b.id_baranginv')->where('p.kode_pindah',$kode_pindah)->where('p.id_user',$id_user)->get();
                $jml2 = $hsl->num_rows();
                if($jml2 > 0)
                {
                    foreach($hsl->result() as $r)
                    {   
                        $data_insert = [
                            'id_baranginv' => $r->id_baranginv,
                            'id_ruang' => $r->id_ruang,
                            'kode_pindah' => $r->kode_pindah,
                            'id_user' => $id_user
                        ];
                        $this->db->insert('tb_pindahtemp',$data_insert);
                    }   
                    return $this->response(['status' => true, 'message' => 'Barang berhasil dimasukan keranjang']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Gagal memasukan barang ke keranjang!'], 400);
                }    
            } 
        }
    }

    function list_pemindahan()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = $this->input->get('search');
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_pindah');
        if($search)
        {
            $this->db->like('kode_pindah', $search)->or_like('tgl_pindah', $search);
        }else
        {
            $this->db->group_by('kode_pindah');
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('p.*,r.ruang')->from('tb_pindah p')->join('tb_ruang r','p.id_ruang=r.id_ruang')->group_by('p.kode_pindah')->order_by('p.kode_pindah','desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('p.kode_pindah', $search)->or_like('p.tgl_pindah', $search)->or_like('r.ruang', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function data_cart($id_user)
    {
        $q = $this->db->select('b.id_baranginv,b.kode_inv,b.barang,d.kode_pindah,b.id_kondisi,d.id_baranginv,d.id_user,d.id_ruang,p.tgl_pindah,p.id_kondisi as kondisi_pindah')->from('tb_pindahtemp d')->join('tb_baranginv b','d.id_baranginv=b.id_baranginv','left')->join('tb_pindah p','d.kode_pindah=p.kode_pindah','left')->where('d.id_user',$id_user)->group_by('d.id_baranginv')->order_by('b.kode_inv','asc')->get();

        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function kondisi_pindah($id_baranginv, $kode_pindah)
    {
        $q = $this->db->select('id_baranginv,id_kondisi,kode_pindah')->from('tb_pindah')->where('id_baranginv',$id_baranginv)->where('kode_pindah',$kode_pindah)->get()->row_array();
        
        return $this->response(['data' => $q]);
    }

    function ruang_pindah($id_baranginv, $kode_pindah)
    {
        $q = $this->db->select('id_baranginv,id_ruang,kode_pindah')->from('tb_pindah')->where('id_baranginv',$id_baranginv)->where('kode_pindah',$kode_pindah)->get()->row_array();

        return $this->response(['data' => $q]);
    }

    function hapus_cart($id_baranginv)
    {
        $this->db->where('id_baranginv',$id_baranginv)->delete('tb_pindahtemp');
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        } 
    }

    function delete_cart($id_user)
    {
        $this->db->where('id_user',$id_user)->delete('tb_pindahtemp');  
        if($this->db->affected_rows() > 0)
        {
            return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        } 
    }
    
    function simpan_pemindahan()
    {
        $param = json_decode(file_get_contents('php://input'), true);
        if(!isset($param)) 
        {
            echo "REQUEST NOT ALLOWED";
        }else
        {
            $kode_alert = $param['kode_pindah'];
            if(empty($kode_alert))
            {
                $kode_pindah = $this->_no_trans();
                $this->simpan_pengambilan_detail($kode_alert, $kode_pindah, $param['id_baranginv'], $param['id_ruang'], $param['id_kondisi'], $param['tgl_pindah'], $param['id_user']);
            }else
            {
                $kode_pindah = $param['kode_pindah'];
                $this->db->where('kode_pindah',$kode_pindah)->delete('tb_pindah');
                $this->simpan_pengambilan_detail($kode_alert, $kode_pindah, $param['id_baranginv'], $param['id_ruang'], $param['id_kondisi'], $param['tgl_pindah'], $param['id_user']);
            }
        }
    }

    function simpan_pengambilan_detail($kode_alert, $kode_pindah, $id_baranginv, $id_ruang, $id_kondisi, $tgl_pindah, $id_user)
    {
        $this->db->trans_start();
            // insert tb_pindah
            $data_insert = [];
            foreach($id_baranginv AS $key => $val)
            {
                $data_insert[] = [
                    'kode_pindah' => $kode_pindah,
                    'id_baranginv' => $id_baranginv[$key],
                    'status' => 1,
                    'id_ruang' => $id_ruang,
                    'id_kondisi' => $id_kondisi[$key],
                    'tgl_pindah' => $tgl_pindah,
                    'id_user' => $id_user
                ];
                
                $data_update = [
                    'status' => 2
                ];

                $data_update_kondisi = [
                    'id_kondisi' => $id_kondisi[$key],
                ];
                
                $this->db->update('tb_pindah', $data_update, ['id_baranginv'=>$id_baranginv[$key], 'kode_pindah !='=> $kode_pindah]);
                $this->db->update('tb_baranginv', $data_update_kondisi, ['id_baranginv'=>$id_baranginv[$key]]);
            }      
            $this->db->insert_batch('tb_pindah', $data_insert);
            $this->db->where('id_user', $id_user)->delete('tb_pindahtemp');
        $this->db->trans_complete();
        if($this->db->trans_status() == TRUE)
        {
            if(empty($kode_alert))
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Disimpan']);
            }else
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Disimpan']);
            }
        }else
        {
            if(empty($kode_alert))
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Diupdate!'], 400);
            }
        }
    }

    private function _no_trans()
    {
        $this->db->select('Right(tb_pindah.kode_pindah,5) as kode ',false);
        $this->db->order_by('kode_pindah', 'desc');
        $this->db->limit(1);
        $query = $this->db->get('tb_pindah');
        if($query->num_rows() <> 0)
        {
            $data = $query->row();
            $kode = intval($data->kode)+1;
        }else
        {
            $kode = 1;
        }
        $kodemax = str_pad($kode,5,"0",STR_PAD_LEFT);
        $kodejadi  = "PD".$kodemax;
        return $kodejadi;
    }

    function detail_pemindahan($kode_pindah)
    {
        $q = $this->db->select('p.*,r.ruang,b.kode_inv,b.barang,b.id_kondisi,k.kondisi')->from('tb_pindah p')->join('tb_ruang r','p.id_ruang=r.id_ruang')->join('tb_baranginv b','p.id_baranginv=b.id_baranginv')->join('tb_kondisi k','b.id_kondisi=k.id_kondisi')->where('p.kode_pindah',$kode_pindah)->order_by('b.kode_inv','asc')->get();
        
        return $this->response(['data' => $q->result_array(), 'totalRecords' => $q->num_rows()]);
    }

    function cek_kode_pindah($kode_pindah)
    {
        $q = $this->db->get_where('tb_pindah', ['kode_pindah'=>$kode_pindah])->row();
        if($q)
        {
            return $this->response(['status' => true, 'message' => 'Kode Transaksi ditemukan']);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Kode Transaksi tidak ditemukan'], 204);
        }
    }

    function hapus_pemindahan($kode_pindah)
    {
        $cek = $this->db->select('kode_pindah')->from('tb_pindahtemp')->where('kode_pindah',$kode_pindah)->get()->num_rows();
        if($cek > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
        }else
        {
            $this->db->where('kode_pindah',$kode_pindah)->delete('tb_pindah');
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