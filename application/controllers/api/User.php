<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/api/Auth.php';

class User extends Auth 
{
    function __construct()
    {
        parent::__construct(); 
        $this->cek_token();
    }

    function list_user()
    {
        $q = $this->db->select('*')->from('tb_user')->order_by('id_user', 'desc')->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $q->num_rows()]);
    }

    function listuser()
    {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 10;
        $search = urldecode($this->input->get('search'));
        
        $offset = ($page - 1) * $limit;
        
        $this->db->from('tb_user');
        if($search)
        {
            $this->db->like('nama', $search)->like('nip', $search)->like('username', $search)->like('email', $search)->like('level', $search)->like('is_active', $search);
        }

        $totalRecords = $this->db->count_all_results(); // Menghitung total jumlah data (tanpa memperhitungkan pagination)

        $this->db->select('*')->from('tb_user')->order_by('id_user', 'desc')->limit($limit, $offset);
        
        if($search)
        {
            $this->db->like('nama', $search)->like('nip', $search)->like('username', $search)->like('email', $search)->like('level', $search)->like('is_active', $search);
        }
        
        $q = $this->db->get();
        return $this->response(['data' => $q->result(), 'totalRecords' => $totalRecords]);
    }

    function get_user_by_id($id_user)
	{	
        $q = $this->db->get_where('tb_user', ['id_user'=>$id_user])->row();
        if($q)
        {
            return $this->response($q);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function get_user_by_username($username)
	{	
        $q = $this->db->get_where('tb_user', ['username'=>$username])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q], 200);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function get_user_by_email($email)
	{	
        $q = $this->db->get_where('tb_user', ['email'=>$email])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q], 200);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function get_user_ks()
	{	
        $q = $this->db->get_where('tb_user', ['level'=>'ks'])->row();
        if($q)
        {
            return $this->response(['status' => true, 'data' => $q], 200);
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], 204);
        }
    }

    function tambah_user()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[30]');            
            $this->form_validation->set_rules('nip', 'NIP', 'max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|is_unique[tb_user.username]', [
				'is_unique' => 'Username sudah digunakan!'
			]);
            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]|max_length[30]'
			);
            $this->form_validation->set_rules('email', 'Email', 'required|trim|min_length[5]|max_length[100]|is_unique[tb_user.email]', [
				'is_unique' => 'Email sudah digunakan!'
			]);
            $this->form_validation->set_rules('level', 'Level', 'required');
            $this->form_validation->set_rules('is_active', 'Status Aktif', 'required|numeric');     
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'nama' => $param['nama'],
                    'nip' => $param['nip'],
                    'username' => trim($param['username']),
                    'email' => $param['email'],
                    'password' => password_hash(trim($param['password']), PASSWORD_DEFAULT),
                    'level' => $param['level'],
                    'is_active' => $param['is_active']
                ];

                $this->db->insert('tb_user', $data);
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

    function edit_user($id_user)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[30]');            
            $this->form_validation->set_rules('nip', 'NIP', 'max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|callback_cek_username['.$id_user.']');
            $this->form_validation->set_rules('password', 'Password', 'trim|min_length[5]|max_length[30]'
			);
            $this->form_validation->set_rules('email', 'Email', 'required|trim|min_length[5]|max_length[100]|callback_cek_email['.$id_user.']');
            $this->form_validation->set_rules('level', 'Level', 'required');
            $this->form_validation->set_rules('is_active', 'Status Aktif', 'required|numeric');     
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                if($param['password'] == '')
                {
                    $data = [
                        'nama' => $param['nama'],
                        'nip' => $param['nip'],
                        'username' => trim($param['username']),
                        'email' => $param['email'],
                        'level' => $param['level'],
                        'is_active' => $param['is_active']
                    ];
                }else
                {
                    $data = [
                        'nama' => $param['nama'],
                        'nip' => $param['nip'],
                        'username' => trim($param['username']),
                        'email' => $param['email'],
                        'password' => password_hash(trim($param['password']), PASSWORD_DEFAULT),
                        'level' => $param['level'],
                        'is_active' => $param['is_active']
                    ];
                }

                $this->db->update('tb_user', $data, ['id_user' => $id_user]);
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

    function hapus_user($id_user)
    {   
        $cek_dkeluarhp = $this->db->select('id_user')->from('tb_detailkeluarhp')->where('id_user',$id_user)->get()->num_rows();
        $cek_keluarhptemp = $this->db->select('id_user')->from('tb_keluarhptemp')->where('id_user',$id_user)->get()->num_rows();
        $cek_pindah = $this->db->select('id_user')->from('tb_pindah')->where('id_user',$id_user)->get()->num_rows();
        $cek_pindahtemp = $this->db->select('id_user')->from('tb_pindahtemp')->where('id_user',$id_user)->get()->num_rows();
        if($this->cek_user($id_user) == 'FALSE')
        {
            if($id_user == 1)
            {
                return $this->response(['status' => false, 'message' => 'Akun admin tidak dapat dihapus!'], 400);
            }else
            {
                if( ($cek_dkeluarhp > 0) OR ($cek_keluarhptemp > 0) OR ($cek_pindah > 0) OR ($cek_pindahtemp > 0) )
                {
                    return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
                }else
                {
                    $this->db->where('id_user',$id_user)->delete('tb_user');
                    if($this->db->affected_rows() > 0)
                    {
                        return $this->response(['status' => true, 'message' => 'Data Berhasil Dihapus']);
                    }else
                    {
                        return $this->response(['status' => false, 'message' => 'Data gagal dihapus, karena sudah berelasi!'], 400);
                    }
                }
            }
        }else
        {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan!'], 400);
        }
    }  

    function edit_profil($id_user)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[30]');      
            $this->form_validation->set_rules('nip', 'NIP', 'max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|callback_cek_username['.$id_user.']');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|min_length[5]|max_length[100]|callback_cek_email['.$id_user.']');   
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'nama' => $param['nama'],
                    'nip' => $param['nip'],
                    'username' => trim($param['username']),
                    'email' => $param['email']
                ];
                
                $this->db->update('tb_user', $data, ['id_user' => $id_user]);
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

    function ganti_password($id_user)
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
		}else
        {
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]|max_length[30]');      
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'password' => password_hash(trim($param['password']), PASSWORD_DEFAULT)
                ];
                
                $this->db->update('tb_user', $data, ['id_user' => $id_user]);
                if($this->db->affected_rows() > 0)
                {
                    return $this->response(['status' => true, 'message' => 'Password Berhasil Diupdate']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Password Gagal Diupdate!'], 400);
                }
            }
        }
    }

    function cek_user($id_user)
    {
        $cek = $this->db->select('id_user')->from('tb_user')->where('id_user',$id_user)->get()->row();
        if($cek)
        {
			return TRUE;
        }else
        {
			return FALSE;
		}
    }
        
    function cek_username($username = '', $id_user = '')
	{	
        $cek = $this->db->select('id_user,username')->from('tb_user')->where('username',$username)->where('id_user != ',$id_user)->get()->row();
        if($cek)
        {
			$this->form_validation->set_message('cek_username', 'Username sudah digunakan!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

    function cek_email($email = '', $id_user = '')
	{	
        $cek = $this->db->select('id_user,email')->from('tb_user')->where('email',$email)->where('id_user != ',$id_user)->get()->row();
        if($cek)
        {
			$this->form_validation->set_message('cek_email', 'Email sudah digunakan!');
			return FALSE;
        }else
        {
			return TRUE;
		}
    }

    function validasi_edit_username($username, $id_user)
	{
        $q = $this->db->select('username')->from('tb_user')->where('username',$username)->where('id_user != ',$id_user)->get()->num_rows();
        if($q > 0)
        {
            return $this->response(['status' => false, 'message' => 'Username sudah digunakan!'], 400);
        }else
        {
            return $this->response(['status' => true, 'message' => 'Username belum digunakan!']);
        }
    }
    
    function validasi_edit_email($email, $id_user)
	{
        $q = $this->db->select('email')->from('tb_user')->where('email',$email)->where('id_user != ',$id_user)->get()->num_rows();
        if($q > 0)
        {
            return $this->response(['status' => false, 'message' => 'Email sudah digunakan!'], 400);
        }else
        {
            return $this->response(['status' => true, 'message' => 'Email belum digunakan!']);
        }
    }
    
}
