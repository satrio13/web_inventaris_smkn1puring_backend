<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['default_controller'] = 'welcome';
/*
| -------------------------------------------------------------------------
| REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/login']['POST'] = 'api/auth/login'; 
$route['api/pengurusbarang']['GET'] = 'api/pengurusbarang/pengurusbarang';
$route['api/get_pengurusbarang_by_id']['GET'] = 'api/pengurusbarang/get_pengurusbarang_by_id';
$route['api/list-pengurusbarang']['GET'] = 'api/pengurusbarang/list_pengurusbarang';
$route['api/edit-pengurusbarang']['POST'] = 'api/pengurusbarang/edit_pengurusbarang';
$route['api/list-tahun']['GET'] = 'api/tahun/list_tahun';
$route['api/tambah-tahun']['POST'] = 'api/tahun/tambah_tahun';
$route['api/edit-tahun/(:num)']['PUT'] = 'api/tahun/edit_tahun/$1';
$route['api/hapus-tahun/(:num)']['DELETE'] = 'api/tahun/hapus_tahun/$1';
$route['api/get_tahun_by_id/(:num)']['GET'] = 'api/tahun/get_tahun_by_id/$1';
$route['api/list-ruang']['GET'] = 'api/ruang/list_ruang';
$route['api/listruang']['GET'] = 'api/ruang/listruang';
$route['api/list-ruang-cart']['GET'] = 'api/ruang/list_ruang_cart';
$route['api/tambah-ruang']['POST'] = 'api/ruang/tambah_ruang';
$route['api/edit-ruang/(:num)']['PUT'] = 'api/ruang/edit_ruang/$1';
$route['api/hapus-ruang/(:num)']['DELETE'] = 'api/ruang/hapus_ruang/$1';
$route['api/get_ruang_by_id/(:num)']['GET'] = 'api/ruang/get_ruang_by_id/$1';
$route['api/list-tanah']['GET'] = 'api/tanah/list_tanah';
$route['api/listtanah']['GET'] = 'api/tanah/listtanah';
$route['api/tambah-tanah']['POST'] = 'api/tanah/tambah_tanah';
$route['api/edit-tanah/(:num)']['PUT'] = 'api/tanah/edit_tanah/$1';
$route['api/hapus-tanah/(:num)']['DELETE'] = 'api/tanah/hapus_tanah/$1';
$route['api/get_tanah_by_id/(:num)']['GET'] = 'api/tanah/get_tanah_by_id/$1';
$route['api/list-gedung']['GET'] = 'api/gedung/list_gedung';
$route['api/listgedung']['GET'] = 'api/gedung/listgedung';
$route['api/tambah-gedung']['POST'] = 'api/gedung/tambah_gedung';
$route['api/edit-gedung/(:num)']['PUT'] = 'api/gedung/edit_gedung/$1';
$route['api/hapus-gedung/(:num)']['DELETE'] = 'api/gedung/hapus_gedung/$1';
$route['api/get_gedung_by_id/(:num)']['GET'] = 'api/gedung/get_gedung_by_id/$1';
$route['api/list-kategori']['GET'] = 'api/kategori/list_kategori';
$route['api/listkategori']['GET'] = 'api/kategori/listkategori';
$route['api/list-kondisi']['GET'] = 'api/kondisi/list_kondisi';
$route['api/listkondisi']['GET'] = 'api/kondisi/listkondisi';
$route['api/cek_id_kondisi/(:num)']['GET'] = 'api/kondisi/cek_id_kondisi/$1';
$route['api/tambah-kategori']['POST'] = 'api/kategori/tambah_kategori';
$route['api/edit-kategori/(:num)']['PUT'] = 'api/kategori/edit_kategori/$1';
$route['api/hapus-kategori/(:num)']['DELETE'] = 'api/kategori/hapus_kategori/$1';
$route['api/get_kategori_by_id/(:num)']['GET'] = 'api/kategori/get_kategori_by_id/$1';
$route['api/cek_id_kategori/(:num)']['GET'] = 'api/kategori/cek_id_kategori/$1';
$route['api/list-baranghp']['GET'] = 'api/baranghp/list_baranghp';
$route['api/listbaranghp']['GET'] = 'api/baranghp/listbaranghp';
$route['api/tambah-baranghp']['POST'] = 'api/baranghp/tambah_baranghp';
$route['api/edit-baranghp/(:num)']['PUT'] = 'api/baranghp/edit_baranghp/$1';
$route['api/hapus-baranghp/(:num)']['DELETE'] = 'api/baranghp/hapus_baranghp/$1';
$route['api/get_baranghp_by_id/(:num)']['GET'] = 'api/baranghp/get_baranghp_by_id/$1';
$route['api/get_baranghp_by_kode/(:any)']['GET'] = 'api/baranghp/get_baranghp_by_kode/$1';
$route['api/validasi_edit_baranghp/(:num)/(:any)']['GET'] = 'api/baranghp/validasi_edit_baranghp/$1/$2';
$route['api/import-baranghp']['POST'] = 'api/baranghp/import_baranghp';
$route['api/import-baranginv']['POST'] = 'api/baranginv/import_baranginv';
$route['api/list-baranginv']['GET'] = 'api/baranginv/list_baranginv';
$route['api/listbaranginv']['GET'] = 'api/baranginv/listbaranginv';
$route['api/tambah-baranginv']['POST'] = 'api/baranginv/tambah_baranginv';
$route['api/edit-baranginv/(:num)']['PUT'] = 'api/baranginv/edit_baranginv/$1';
$route['api/hapus-baranginv/(:num)']['DELETE'] = 'api/baranginv/hapus_baranginv/$1';
$route['api/get_baranginv_by_id/(:num)']['GET'] = 'api/baranginv/get_baranginv_by_id/$1';
$route['api/get_baranginv_by_kode/(:any)']['GET'] = 'api/baranginv/get_baranginv_by_kode/$1';
$route['api/validasi_edit_baranginv/(:num)/(:any)']['GET'] = 'api/baranginv/validasi_edit_baranginv/$1/$2';
$route['api/list-barangmasukhp']['GET'] = 'api/barangmasukhp/list_barangmasukhp';
$route['api/listbarangmasukhp']['GET'] = 'api/barangmasukhp/listbarangmasukhp';
$route['api/tambah-barangmasukhp']['POST'] = 'api/barangmasukhp/tambah_barangmasukhp';
$route['api/hapus-barangmasukhp/(:num)']['DELETE'] = 'api/barangmasukhp/hapus_barangmasukhp/$1';
$route['api/get_barangmasukhp_by_id/(:num)']['GET'] = 'api/barangmasukhp/get_barangmasukhp_by_id/$1';
$route['api/data-cart-ambil/(:num)']['GET'] = 'api/pengambilan/data_cart/$1';
$route['api/detail-cart-ambil/(:any)']['GET'] = 'api/pengambilan/detail_cart/$1';
$route['api/hapus-cart-ambil/(:num)']['DELETE'] = 'api/pengambilan/hapus_cart/$1';
$route['api/simpan-pengambilan']['POST'] = 'api/pengambilan/simpan_pengambilan';
$route['api/simpan-pengambilan-detail']['POST'] = 'api/pengambilan/simpan_pengambilan_detail';
$route['api/list-pengambilan']['GET'] = 'api/pengambilan/list_pengambilan';
$route['api/hapus-pengambilan/(:any)']['DELETE'] = 'api/pengambilan/hapus_pengambilan/$1';
$route['api/add-to-cart/(:any)/(:num)']['GET'] = 'api/pengambilan/add_to_cart/$1/$2';
$route['api/add-to-cart-edit/(:any)/(:num)']['GET'] = 'api/pengambilan/add_to_cart_edit/$1/$2';
$route['api/delete-cart/(:num)']['DELETE'] = 'api/pengambilan/delete_cart/$1';
$route['api/detail-pengambilan/(:any)']['GET'] = 'api/pengambilan/detail-pengambilan/$1';
$route['api/cek-kode-trans/(:any)']['GET'] = 'api/pengambilan/cek_kode_trans/$1';
$route['api/cek-posisi-baranginv/(:any)']['GET'] = 'api/pemindahan/cek-posisi-baranginv/$1';
$route['api/add-to-cart-pemindahan/(:any)/(:num)']['GET'] = 'api/pemindahan/add_to_cart/$1/$2';
$route['api/add-to-cart-edit-pemindahan/(:any)/(:num)']['GET'] = 'api/pemindahan/add_to_cart_edit/$1/$2';
$route['api/list-pemindahan']['GET'] = 'api/pemindahan/list-pemindahan/$1';
$route['api/data-cart-pindah/(:num)']['GET'] = 'api/pemindahan/data_cart/$1';
$route['api/kondisi-pindah/(:num)/(:any)']['GET'] = 'api/pemindahan/kondisi_pindah/$1/$2';
$route['api/ruang-pindah/(:num)/(:any)']['GET'] = 'api/pemindahan/ruang_pindah/$1/$2';
$route['api/hapus-cart-pindah/(:num)']['DELETE'] = 'api/pemindahan/hapus_cart/$1';
$route['api/delete-cart-pindah/(:num)']['DELETE'] = 'api/pemindahan/delete_cart/$1';
$route['api/simpan-pemindahan']['POST'] = 'api/pemindahan/simpan_pemindahan';
$route['api/detail-pemindahan/(:any)']['GET'] = 'api/pemindahan/detail-pemindahan/$1';
$route['api/cek-kode-pindah/(:any)']['GET'] = 'api/pemindahan/cek_kode_pindah/$1';
$route['api/hapus-pemindahan/(:any)']['DELETE'] = 'api/pemindahan/hapus_pemindahan/$1';
$route['api/list-user']['GET'] = 'api/user/list_user';
$route['api/listuser']['GET'] = 'api/user/listuser';
$route['api/tambah-user']['POST'] = 'api/user/tambah_user';
$route['api/edit-user/(:num)']['PUT'] = 'api/user/edit_user/$1';
$route['api/edit-profil/(:num)']['PUT'] = 'api/user/edit_profil/$1';
$route['api/ganti-password/(:num)']['PUT'] = 'api/user/ganti_password/$1';
$route['api/hapus-user/(:num)']['DELETE'] = 'api/user/hapus_user/$1';
$route['api/get_user_by_id/(:num)']['GET'] = 'api/user/get_user_by_id/$1';
$route['api/get_user_by_username/(:any)']['GET'] = 'api/user/get_user_by_username/$1';
$route['api/get_user_by_email/(:any)']['GET'] = 'api/user/get_user_by_email/$1';
$route['api/get_user_ks']['GET'] = 'api/user/get_user_ks';
$route['api/validasi_edit_username/(:any)/(:num)']['GET'] = 'api/user/validasi_edit_username/$1/$2';
$route['api/validasi_edit_email/(:any)/(:num)']['GET'] = 'api/user/validasi_edit_email/$1/$2';
$route['api/laporan-pembelian/(:any)/(:any)']['GET'] = 'api/laporan/laporan-pembelian/$1/$2';
$route['api/laporan-pengambilan/(:any)/(:any)']['GET'] = 'api/laporan/laporan-pengambilan/$1/$2';
$route['api/laporan-pemindahan/(:any)/(:any)']['GET'] = 'api/laporan/laporan-pemindahan/$1/$2';
$route['api/daftar-barang-per-ruang/(:num)']['GET'] = 'api/laporan/daftar_barang_per_ruang/$1';
$route['api/rekap-barang-per-ruang/(:num)']['GET'] = 'api/laporan/rekap_barang_per_ruang/$1';
$route['api/list-perbaikan']['GET'] = 'api/perbaikan/list_perbaikan';
$route['api/listperbaikan']['GET'] = 'api/perbaikan/listperbaikan';
$route['api/tambah-perbaikan']['POST'] = 'api/perbaikan/tambah_perbaikan';
$route['api/edit-perbaikan/(:num)']['PUT'] = 'api/perbaikan/edit_perbaikan/$1';
$route['api/hapus-perbaikan/(:num)']['DELETE'] = 'api/perbaikan/hapus_perbaikan/$1';
$route['api/get_perbaikan_by_id/(:num)']['GET'] = 'api/perbaikan/get_perbaikan_by_id/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;