<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Baranginv extends Auth 
{
    function __construct()
    {
        parent::__construct();
        $this->cek_token();
    }

    function list_baranginv()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = $this->input->get('search');
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_baranginv');
        if($search)
        {
            $this->db->like('barang', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('b.*,k.kategori,s.kondisi')->from('tb_baranginv b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->join('tb_kondisi s','b.id_kondisi=s.id_kondisi')->order_by('b.id_baranginv', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('b.kode_inv', $search)->or_like('b.barang', $search)->or_like('k.kategori', $search)->or_like('b.merk', $search)->or_like('b.satuan', $search)->or_like('b.th_beli', $search)->or_like('s.kondisi', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listbaranginv()
    {
        $q = $this->db->select('b.*,k.kategori,s.kondisi')->from('tb_baranginv b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->join('tb_kondisi s','b.id_kondisi=s.id_kondisi')->order_by('b.id_baranginv', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function list_baranginv_asc()
    {
        $q = $this->db->select('b.*,k.kategori,s.kondisi')->from('tb_baranginv b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->join('tb_kondisi s','b.id_kondisi=s.id_kondisi')->order_by('b.barang', 'asc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_baranginv()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kode_inv', 'Kode Barang', 'required|trim|is_unique[tb_baranginv.kode_inv]', [
				'is_unique' => 'Kode Barang sudah digunakan!'
			]);
            $this->form_validation->set_rules('barang', 'Nama Barang', 'required');
            $this->form_validation->set_rules('satuan', 'Satuan Barang', 'required');
            $this->form_validation->set_rules('id_kategori', 'Kategori Barang', 'required|callback_cek_id_kategori['.$param['id_kategori'].']');
            $this->form_validation->set_rules('id_kondisi', 'Kondisi Barang', 'required|callback_cek_id_kondisi['.$param['id_kondisi'].']');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kode_inv' => trim($param['kode_inv']),
                    'barang' => $param['barang'],
                    'merk' => $param['merk'],
                    'satuan' => $param['satuan'],
                    'id_kategori' => $param['id_kategori'],
                    'th_beli' => $param['th_beli'],
                    'id_kondisi' => $param['id_kondisi'],
                    'keterangan' => $param['keterangan']
                ];

                $this->db->insert('tb_baranginv', $data);
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

    function edit_baranginv($id_baranginv)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kode_inv', 'Kode Barang', 'required|trim|callback_cek_kode_inv['.$id_baranginv.']');
            $this->form_validation->set_rules('barang', 'Nama Barang', 'required');
            $this->form_validation->set_rules('satuan', 'Satuan Barang', 'required');
            $this->form_validation->set_rules('id_kategori', 'Kategori Barang', 'required|callback_cek_id_kategori['.$param['id_kategori'].']');
            $this->form_validation->set_rules('id_kondisi', 'Kondisi Barang', 'required|callback_cek_id_kondisi['.$param['id_kondisi'].']');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kode_inv' => trim($param['kode_inv']),
                    'barang' => $param['barang'],
                    'merk' => $param['merk'],
                    'satuan' => $param['satuan'],
                    'id_kategori' => $param['id_kategori'],
                    'th_beli' => $param['th_beli'],
                    'id_kondisi' => $param['id_kondisi'],
                    'keterangan' => $param['keterangan']
                ];

                $this->db->update('tb_baranginv', $data, ['id_baranginv'=>$id_baranginv]);
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

    function get_baranginv_by_id($id_baranginv)
	{	
        $q = $this->db->get_where('tb_baranginv', ['id_baranginv'=>$id_baranginv])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function get_baranginv_by_kode($kode)
	{	
        $q = $this->db->get_where('tb_baranginv', ['kode_inv'=>$kode])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q], 200);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function cek_kode_inv($kode = '', $id_baranginv = '')
	{	
        $cek = $this->db->select('id_baranginv,kode_inv')->from('tb_baranginv')->where('kode_inv',$kode)->where('id_baranginv != ',$id_baranginv)->get()->row();
        if($cek)
        {
			$this->form_validation->set_message('cek_kode_inv', 'Kode Barang sudah digunakan!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

    function cek_id_kategori($id_kategori)
	{	
        $cek = $this->db->get_where('tb_kategori', ['id_kategori'=>$id_kategori])->row();
        if(!$cek)
        {
			$this->form_validation->set_message('cek_id_kategori', 'ID Kategori tidak terdaftar!');
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
    
    function validasi_edit_baranginv($id_baranginv, $kode)
	{
        $q = $this->db->select('id_baranginv,kode_inv')->from('tb_baranginv')->where('kode_inv',$kode)->where('id_baranginv != ',$id_baranginv)->get()->num_rows();
        if($q > 0)
        {
            return $this->response(['status' => false, 'message' => 'Kode Barang sudah digunakan!'], 400);
        }else
        {
            return $this->response(['status' => true, 'message' => 'Kode Barang belum digunakan!']);
        }
    }   

    function hapus_baranginv($id_baranginv)
    {    
        $cek_pindah = $this->db->select('id_baranginv')->from('tb_pindah')->where('id_baranginv',$id_baranginv)->get()->num_rows();
        $cek_pindahtemp = $this->db->select('id_baranginv')->from('tb_pindahtemp')->where('id_baranginv',$id_baranginv)->get()->num_rows();
        if($cek_pindah > 0 OR $cek_pindahtemp > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
        }else
        {
            $this->db->delete('tb_baranginv', ['id_baranginv'=>$id_baranginv]);
            if($this->db->affected_rows() > 0)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
            }    
        }
    }

    function import_baranginv()
    { 
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excelreader = new PHPExcel_Reader_Excel2007();
        
        // Mendapatkan informasi file yang diunggah dari postman
        $uploadedFile = $_FILES['file']['tmp_name'];
        $filename = $_FILES['file']['name'];
        if(isset($filename))
        {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if($ext == 'xlsx')
            {
                // Tentukan folder tujuan untuk menyimpan file yang diunggah
                $uploadFolder = './excel/baranginv/';
                // Cek apakah ada file yang diunggah
                if($_FILES['file']['error'] === UPLOAD_ERR_OK)
                {   
                    // Pindahkan file dari folder sementara ke folder tujuan
                    $destination = $uploadFolder . $filename;
                    if(move_uploaded_file($uploadedFile, $destination))
                    {
                        //echo "File berhasil diunggah dan disimpan di: " . $destination;
                        $loadexcel = $excelreader->load($destination);
                        $sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
                        
                        $data = array();		
                        $numrow = 1;
                        foreach($sheet as $row)
                        {
                            // Cek $numrow apakah lebih dari 1
                            // Artinya karena baris pertama adalah nama-nama kolom
                            // Jadi dilewat saja, tidak usah diimport
                            if($numrow > 1)
                            {
                                if($row['A'] == '' OR $row['B'] == '' OR $row['C'] == '' OR $row['E'] == '' OR $row['G'] == '')
                                {
                                    return $this->response(['status' => false, 'message' => 'Data gagal diimport, masih ada field yang kosong!'], 400);
                                }else
                                {
                                    array_push($data, array(
                                        'kode_inv' => trim($row['A']),
                                        'barang' => $row['B'],
                                        'id_kategori' => $row['C'],
                                        'merk' => $row['D'],
                                        'satuan' => $row['E'],
                                        'th_beli' => $row['F'],
                                        'id_kondisi' => $row['G'],
                                        'keterangan' => $row['H']
                                    ));
                                }
                            }
                            $numrow++; 
                        }
        
                        $this->db->insert_batch('tb_baranginv', $data);
                        if($this->db->affected_rows() > 0)
                        {
                            return $this->response(['status' => true, 'message' => 'File berhasil diunggah dan data berhasil diimport', 'total_insert' => $this->db->affected_rows()]);
                        }else
                        {
                            return $this->response(['status' => false, 'message' => 'Data gagal diimport, silahkan coba lagi!'], 400);
                        }
                    }else
                    {
                        return $this->response(['status' => false, 'message' => 'Gagal menyimpan file!'], 400);
                    }
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Terjadi kesalahan saat mengunggah file: '.$_FILES['file']['error'].'!'], 400);
                }      
            }else
            {
                return $this->response(['status' => false, 'message' => 'Periksa kembali ekstensi file yang anda import!'], 400);
            }
        }else
        {
            return $this->response(['status' => false, 'message' => 'Tidak ada file yang diimport!'], 404);
        }
    }

}