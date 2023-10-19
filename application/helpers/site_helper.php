<?php 
function tgl_jam_simpan_sekarang()
{
   date_default_timezone_set('Asia/Jakarta');
   return date('Y-m-d H:i:s');
}

function is_email($str)
{
   return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function is_url($str)
{
   return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$str);
}