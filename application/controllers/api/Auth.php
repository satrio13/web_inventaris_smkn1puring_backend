<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/Key.php';
require_once APPPATH . '/libraries/ExpiredException.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Auth extends REST_Controller 
{
    private $key;
    function __construct()
    {
        parent::__construct();
        $this->key = '123456789';
    }

    function register()
    {
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['email']) OR !isset($param['username']) OR !isset($param['password']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {   
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[100]|is_unique[tb_user.email]', [
				'is_unique' => 'Email sudah digunakan!'
			]);
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|is_unique[tb_user.username]', [
				'is_unique' => 'Username sudah digunakan!'
			]);
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|max_length[30]');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $data = [
                    'email' => trim($param['email']),
                    'username' => trim($param['username']),
                    'password' => $param['password']    
                ];

                $this->db->insert('tb_user', $data);
                if($this->db->affected_rows() > 0)
                {
                    return $this->response(['status' => true, 'message' => 'Berhasil Register']);
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Gagal Register!'], 400);
                }
            }
        }
    }

    function login()
    {       
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param['username']) OR !isset($param['password']))
        {
			return $this->response(['status' => false, 'message' => 'Paramater tidak lengkap!'], 400);
		}else
        {
            $date = new DateTime();
            $user = $param['username'];
            $pass = $param['password'];
            
            $this->form_validation->set_data($param);
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[30]|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|max_length[30]|trim');
            if($this->form_validation->run() == false)
            {
                $message = $this->form_validation->error_array();
                return $this->response(['status' => false, 'message' => $message], 400);
            }else
            {
                $cek = $this->db->get_where('tb_user', array('username' => $user));
                if($cek->num_rows() > 0)
                {
                    $data = $cek->row();
                    if($pass == $data->password)
                    {
                        $payload = [
                            'id_user' => $data->id_user, 
                            'email' => $data->email, 
                            'username' => $data->username,
                            'iat' => $date->getTimestamp(), //waktu token digenerate
                            'exp' => $date->getTimestamp() + (60 * 120) //token berlaku 2 jam
                        ];

                        $token = JWT::encode($payload, $this->key, 'HS256');
                        return $this->response(['status' => true, 'message' => 'Login berhasil', 'data' => $payload, 'token' => $token]);
                    }else
                    {
                        return $this->response(['status' => false, 'message' => 'Password yang anda masukkan salah!'], 401);
                    }
                }else
                {
                    return $this->response(['status' => false, 'message' => 'Username belum terdaftar!'], 401);
                }  
            }      
        }
    }

    protected function cek_token()
    {
        $jwt = $this->input->get_request_header('Authorization');
        try{
            $token = JWT::decode($jwt, new Key($this->key, 'HS256'));
        }catch(Exception $e)
        {
            return $this->response(['status' => false, 'message' => 'Invalid Token!'], 401);
        }
    }
    
}