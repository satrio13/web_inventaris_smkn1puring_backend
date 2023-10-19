<?php
class REST_Controller extends CI_Controller
{
    protected $_supported_formats = [
        'json' => 'application/json',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setCors();
    }

    protected function response($data, $status = 200)
    {
        http_response_code($status); //set response code
        $this->toJson($data);
        exit();
    }

    /**
     * Retreive POST INPUT
     */
    protected function getPost($input)
    {
        $content_type = $this->input->server('CONTENT_TYPE');
        $content_type = (strpos($content_type, ';') !== FALSE ? current(explode(';', $content_type)) : $content_type);
        //cek jika input content type adalah JSON
        if ($content_type == 'application/json') {
            $body = json_decode($this->input->raw_input_stream, true);
            return isset($body[$input]) ? $body[$input] : null;
        } else {
            return $this->input->post($input);
        }
    }

    /**
     * Retreive POST File
     */
    protected function getFile($input)
    {
        return isset($_FILES[$input]) ? $_FILES[$input] : null;
    }

    /**
     * Set Output to JSON
     */
    protected function toJson($data)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))->_display();
    }

    /**
     * Set CORS
     */
    private function setCors()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400"); //cache 1 day
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    }

}