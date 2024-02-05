<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class Baranghp extends Auth 
{
    function __construct()
    {
        parent::__construct(); 
        $this->cek_token();
    }

    function list_baranghp()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_baranghp');
        if($search)
        {
            $this->db->like('barang', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('b.*,k.kategori')->from('tb_baranghp b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->order_by('b.id_baranghp', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('b.kode_hp', $search)->or_like('b.barang', $search)->or_like('b.satuan', $search)->or_like('b.stok', $search)->or_like('b.harga', $search)->or_like('k.kategori', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function listbaranghp()
    {
        $q = $this->db->select('b.*,k.kategori')->from('tb_baranghp b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->order_by('b.id_baranghp', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function list_baranghp_asc()
    {
        $q = $this->db->select('b.*,k.kategori')->from('tb_baranghp b')->join('tb_kategori k','b.id_kategori=k.id_kategori')->order_by('b.barang', 'asc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function tambah_baranghp()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kode_hp', 'Kode Barang', 'required|trim|is_unique[tb_baranghp.kode_hp]', [
				'is_unique' => 'Kode Barang sudah digunakan!'
			]);
            $this->form_validation->set_rules('barang', 'Nama Barang', 'required');
            $this->form_validation->set_rules('id_kategori', 'Kategori Barang', 'required|callback_cek_id_kategori['.$param['id_kategori'].']');
            $this->form_validation->set_rules('satuan', 'Satuan Barang', 'required');
            $this->form_validation->set_rules('harga', 'Harga', 'numeric');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kode_hp' => trim($param['kode_hp']),
                    'barang' => $param['barang'],
                    'id_kategori' => $param['id_kategori'],
                    'satuan' => $param['satuan'],
                    'harga' => $param['harga']           
                ];

                $this->db->insert('tb_baranghp', $data);
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

    function edit_baranghp($id_baranghp)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('kode_hp', 'Kode Barang', 'required|trim|callback_cek_kode_hp['.$id_baranghp.']');
            $this->form_validation->set_rules('barang', 'Nama Barang', 'required');
            $this->form_validation->set_rules('id_kategori', 'Kategori Barang', 'required|callback_cek_id_kategori['.$param['id_kategori'].']');
            $this->form_validation->set_rules('satuan', 'Satuan Barang', 'required');
            $this->form_validation->set_rules('harga', 'Harga', 'numeric');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'kode_hp' => trim($param['kode_hp']),
                    'barang' => $param['barang'],
                    'id_kategori' => $param['id_kategori'],
                    'satuan' => $param['satuan'],
                    'harga' => $param['harga']             
                ];

                $this->db->update('tb_baranghp', $data, ['id_baranghp'=>$id_baranghp]);
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

    function get_baranghp_by_id($id_baranghp)
	{	
        $q = $this->db->get_where('tb_baranghp', ['id_baranghp'=>$id_baranghp])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function get_baranghp_by_kode($kode)
	{	
        $q = $this->db->get_where('tb_baranghp', ['kode_hp'=>$kode])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q], 200);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function cek_kode_hp($kode = '', $id_baranghp = '')
	{	
        $cek = $this->db->select('id_baranghp,kode_hp')->from('tb_baranghp')->where('kode_hp',$kode)->where('id_baranghp != ',$id_baranghp)->get()->row();
        if($cek)
        {
			$this->form_validation->set_message('cek_kode_hp', 'Kode Barang sudah digunakan!');
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

    private function _cek_id_kondisi($id_kondisi)
    {	
        return $this->db->get_where('tb_kondisi', ['id_kondisi'=>$id_kondisi])->row();
    }

    function validasi_edit_baranghp($id_baranghp, $kode)
	{
        $q = $this->db->select('id_baranghp,kode_hp')->from('tb_baranghp')->where('kode_hp',$kode)->where('id_baranghp != ',$id_baranghp)->get()->num_rows();
        if($q > 0)
        {
            return $this->response(['status' => false, 'message' => 'Kode Barang sudah digunakan!'], 400);
        }else
        {
            return $this->response(['status' => true, 'message' => 'Kode Barang belum digunakan!']);
        }
    }   

    function hapus_baranghp($id_baranghp)
    {    
        $cek_keluarhp = $this->db->select('id_baranghp')->from('tb_keluarhp')->where('id_baranghp',$id_baranghp)->get()->num_rows();
        $cek_keluarhptemp = $this->db->select('id_baranghp')->from('tb_keluarhptemp')->where('id_baranghp',$id_baranghp)->get()->num_rows();
        $cek_masukhp = $this->db->select('id_baranghp')->from('tb_masukhp')->where('id_baranghp',$id_baranghp)->get()->num_rows();
        if($cek_keluarhp > 0 OR $cek_keluarhptemp > 0 OR $cek_masukhp > 0)
        {
            return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
        }else
        {
            $this->db->delete('tb_baranghp', ['id_baranghp'=>$id_baranghp]);
            if($this->db->affected_rows() > 0)
            {
                return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
            }else
            {
                return $this->response(['status' => false, 'message' => 'Data Gagal Dihapus!'], 400);
            }    
        }
    }

    function import_baranghp()
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
                $uploadFolder = './excel/baranghp/';
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
                        
                        // Array baru untuk menyimpan data unik berdasarkan 'kode_hp'
                        $uniqueData = [];
                        // Loop melalui data yang diberikan
                        foreach($sheet as $item)
                        {
                            // Gunakan 'kode_hp' sebagai kunci array
                            $kode_hp = $item['A'];
                            // Jika 'kode_hp' belum ada dalam array $uniqueData, tambahkan data tersebut
                            if(!isset($uniqueData[$kode_hp]))
                            {
                                $uniqueData[$kode_hp] = $item;
                            }
                        }

                        $data = array();		
                        $numrow = 1;
                        foreach($uniqueData as $row)
                        {
                            // Cek $numrow apakah lebih dari 1
                            // Artinya karena baris pertama adalah nama-nama kolom
                            // Jadi dilewat saja, tidak usah diimport
                            if($numrow > 1)
                            {
                                if($row['A'] == '' OR $row['B'] == '' OR $row['C'] == '' OR $row['D'] == '' OR $row['E'] == '')
                                {
                                    return $this->response(['status' => false, 'message' => 'Data gagal diimport, masih ada field yang kosong!'], 400);
                                }else
                                {
                                    array_push($data, array(
                                        'kode_hp' => trim($row['A']),
                                        'barang' => $row['B'],
                                        'id_kategori' => $row['C'],
                                        'satuan' => $row['D'],
                                        'harga' => $row['E']
                                    ));
                                }
                            }
                            $numrow++; 
                        }
        
                        $this->db->insert_batch('tb_baranghp', $data);
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
