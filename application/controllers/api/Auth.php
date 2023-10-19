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

    function login()
    {       
        $param = json_decode(file_get_contents('php://input'), true);
		if(!isset($param))
        {
			echo "REQUEST NOT ALLOWED";	
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
                    if(password_verify($pass, $data->password))
                    {
                        if($data->is_active == 1)
                        { 
                            $payload = [
                                'id_user' => $data->id_user, 
                                'nama' => $data->nama, 
                                'level' => $data->level,
                                'iat' => $date->getTimestamp(), //waktu token digenerate
                                'exp' => $date->getTimestamp() + (60 * 120) //token berlaku 2 jam
                            ];

                            $token = JWT::encode($payload, $this->key, 'HS256');
                            return $this->response(['status' => true, 'message' => 'Login berhasil', 'data' => $payload, 'token' => $token]);
                        }else
                        {  
                            return $this->response(['status' => false, 'message' => 'Akun anda tidak aktif!'], 401);  
                        }
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