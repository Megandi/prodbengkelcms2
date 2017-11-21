<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// default page
Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

// home dashboard
Route::get('dashboard/home', 'HomeController@index');

// module hrd-------------------------------------------------
// employee route---------------------------------------------
Route::get('hrd/emp_home', 'Hrd@index_emp');
Route::get('hrd/emp_home/add', 'Hrd@add_emp');
Route::post('hrd/emp_home/do_add', 'Hrd@do_add_emp');
Route::get('hrd/emp_home/edit/{id}', 'Hrd@edit_emp');
Route::put('hrd/emp_home/do_edit/{id}', 'Hrd@do_edit_emp');
Route::get('hrd/emp_home/delete/{id}', 'Hrd@delete_emp');

Route::get('hrd/emp_home/summary/{id}/{emp_id}', 'Hrd@summary_emp');

Route::post('hrd/emp_home/getrange', 'Hrd@emp_range');
Route::post('hrd/emp_home/export', 'Hrd@emp_export');

// salary route-----------------------------------------------
Route::get('hrd/sal_home', 'Hrd@index_sal');
Route::get('hrd/sal_home/add', 'Hrd@add_sal');
Route::post('hrd/sal_home/do_add', 'Hrd@do_add_sal');
Route::get('hrd/sal_home/edit/{id}', 'Hrd@edit_sal');
Route::put('hrd/sal_home/do_edit/{id}', 'Hrd@do_edit_sal');
Route::get('hrd/sal_home/delete/{id}', 'Hrd@delete_sal');

Route::post('hrd/sal_home/getrange', 'Hrd@sal_range');
Route::get('hrd/sal_home/search_employee', 'Hrd@search_employee');
Route::post('hrd/sal_home/export', 'Hrd@sal_export');
// module hrd-------------------------------------------------

// operational------------------------------------------------
// items type route-----------------------------------------------
Route::get('operational/items_type_home', 'Operational@index_items_type');
Route::get('operational/items_type_home/add', 'Operational@add_items_type');
Route::post('operational/items_type_home/do_add', 'Operational@do_add_items_type');
Route::get('operational/items_type_home/edit/{id}', 'Operational@edit_items_type');
Route::put('operational/items_type_home/do_edit/{id}', 'Operational@do_edit_items_type');
Route::get('operational/items_type_home/delete/{id}', 'Operational@delete_items_type');

Route::post('operational/items_type_home/getrange', 'Operational@items_type_range');
Route::post('operational/items_type_home/export', 'Operational@items_type_export');

// items route-----------------------------------------------
Route::get('operational/items_home', 'Operational@index_items');
Route::get('operational/items_home/add', 'Operational@add_items');
Route::post('operational/items_home/do_add', 'Operational@do_add_items');
Route::get('operational/items_home/edit/{id}', 'Operational@edit_items');
Route::put('operational/items_home/do_edit/{id}', 'Operational@do_edit_items');
Route::get('operational/items_home/delete/{id}', 'Operational@delete_items');

Route::post('operational/items_home/getrange', 'Operational@items_range');
Route::post('operational/items_home/export', 'Operational@items_export');

// service route-----------------------------------------------
Route::get('operational/service_home', 'Operational@index_service');
Route::get('operational/service_home/add', 'Operational@add_service');
Route::post('operational/service_home/do_add', 'Operational@do_add_service');
Route::get('operational/service_home/edit/{id}/{barang_id}', 'Operational@edit_service');
Route::put('operational/service_home/do_edit/{id}/{barang_id}', 'Operational@do_edit_service');
Route::get('operational/service_home/delete/{id}', 'Operational@delete_service');

Route::get('operational/service_home/search_items_service', 'Operational@search_items_service');
Route::post('operational/service_home/getrange', 'Operational@service_range');
Route::post('operational/service_home/export', 'Operational@service_export');

// supplier route-----------------------------------------------
Route::get('operational/supp_home', 'operational@index_supp');
Route::get('operational/supp_home/add', 'Operational@add_supp');
Route::post('operational/supp_home/do_add', 'Operational@do_add_supp');
Route::get('operational/supp_home/edit/{id}', 'Operational@edit_supp');
Route::put('operational/supp_home/do_edit/{id}', 'Operational@do_edit_supp');
Route::get('operational/supp_home/delete/{id}', 'Operational@delete_supp');

Route::post('operational/supp_home/getrange', 'Operational@supp_range');
Route::post('operational/supp_home/export', 'Operational@supp_export');

// customer route-----------------------------------------------
Route::get('operational/cust_home', 'operational@index_cust');
Route::get('operational/cust_home/add', 'Operational@add_cust');
Route::post('operational/cust_home/do_add', 'Operational@do_add_cust');
Route::get('operational/cust_home/edit/{id}', 'Operational@edit_cust');
Route::put('operational/cust_home/do_edit/{id}', 'Operational@do_edit_cust');
Route::get('operational/cust_home/delete/{id}', 'Operational@delete_cust');

Route::post('operational/cust_home/getrange', 'Operational@cust_range');
Route::post('operational/cust_home/export', 'Operational@cust_export');

// car route-----------------------------------------------
Route::get('operational/car_home', 'operational@index_car');
Route::get('operational/car_home/add', 'Operational@add_car');
Route::post('operational/car_home/do_add', 'Operational@do_add_car');
Route::get('operational/car_home/edit/{id}', 'Operational@edit_car');
Route::put('operational/car_home/do_edit/{id}', 'Operational@do_edit_car');
Route::get('operational/car_home/delete/{id}', 'Operational@delete_car');

Route::get('operational/car_home/search_cust', 'Operational@search_cust');
Route::post('operational/car_home/getrange', 'Operational@car_range');
Route::post('operational/car_home/export', 'Operational@car_export');

// quarry route-----------------------------------------------
Route::get('operational/quar_home', 'operational@index_quar');
Route::get('operational/quar_home/add', 'Operational@add_quar');
Route::post('operational/quar_home/do_add', 'Operational@do_add_quar');
Route::get('operational/quar_home/edit/{id}', 'Operational@edit_quar');
Route::put('operational/quar_home/do_edit/{id}', 'Operational@do_edit_quar');
Route::get('operational/quar_home/delete/{id}', 'Operational@delete_quar');

Route::post('operational/quar_home/getrange', 'Operational@quar_range');
Route::post('operational/quar_home/export', 'Operational@quar_export');

// port route-----------------------------------------------
Route::get('operational/port_home', 'operational@index_port');
Route::get('operational/port_home/add', 'Operational@add_port');
Route::post('operational/port_home/do_add', 'Operational@do_add_port');
Route::get('operational/port_home/edit/{id}', 'Operational@edit_port');
Route::put('operational/port_home/do_edit/{id}', 'Operational@do_edit_port');
Route::get('operational/port_home/delete/{id}', 'Operational@delete_port');

Route::post('operational/port_home/getrange', 'Operational@port_range');
Route::post('operational/port_home/export', 'Operational@port_export');

// solar type route----------------------------------------------
Route::get('operational/solar_type_home', 'operational@index_solar_type');
Route::get('operational/solar_type_home/add', 'Operational@add_solar_type');
Route::post('operational/solar_type_home/do_add', 'Operational@do_add_solar_type');
Route::get('operational/solar_type_home/edit/{id}', 'Operational@edit_solar_type');
Route::put('operational/solar_type_home/do_edit/{id}', 'Operational@do_edit_solar_type');
Route::get('operational/solar_type_home/delete/{id}', 'Operational@delete_solar_type');

Route::post('operational/solar_type_home/getrange', 'Operational@solar_type_range');
Route::post('operational/solar_type_home/export', 'Operational@solar_type_export');

// solar use route-----------------------------------------------
Route::get('operational/solar_home', 'operational@index_solar');
Route::get('operational/solar_home/add', 'Operational@add_solar');
Route::post('operational/solar_home/do_add', 'Operational@do_add_solar');
Route::get('operational/solar_home/edit/{id}', 'Operational@edit_solar');
Route::put('operational/solar_home/do_edit/{id}', 'Operational@do_edit_solar');
Route::get('operational/solar_home/delete/{id}', 'Operational@delete_solar');

Route::get('operational/solar_home/search_car', 'Operational@search_car');
Route::get('operational/solar_home/search_employee_solar', 'Operational@search_employee_solar');
Route::post('operational/solar_home/getrange', 'Operational@solar_range');
Route::post('operational/solar_home/export', 'Operational@solar_export');

// route manage route----------------------------------------------
Route::get('operational/route_home', 'operational@index_route');
Route::get('operational/route_home/add', 'Operational@add_route');
Route::post('operational/route_home/do_add', 'Operational@do_add_route');
Route::get('operational/route_home/edit/{id}', 'Operational@edit_route');
Route::put('operational/route_home/do_edit/{id}', 'Operational@do_edit_route');
Route::get('operational/route_home/delete/{id}', 'Operational@delete_route');

Route::get('operational/route_home/search_route_a', 'Operational@search_route_a');
Route::get('operational/route_home/search_route_b', 'Operational@search_route_b');

Route::post('operational/route_home/getrange', 'Operational@route_range');
Route::post('operational/route_home/export', 'Operational@route_export');

// tonase manage route----------------------------------------------
Route::get('operational/tonase_home', 'operational@index_tonase');
Route::get('operational/tonase_home/add', 'Operational@add_tonase');
Route::post('operational/tonase_home/do_add', 'Operational@do_add_tonase');
Route::get('operational/tonase_home/edit/{id}', 'Operational@edit_tonase');
Route::put('operational/tonase_home/do_edit/{id}', 'Operational@do_edit_tonase');
Route::get('operational/tonase_home/delete/{id}', 'Operational@delete_tonase');

Route::get('operational/tonase_home/search_tonase_route', 'Operational@search_tonase_route');
Route::post('operational/tonase_home/getrange', 'Operational@tonase_range');
Route::post('operational/tonase_home/export', 'Operational@tonase_export');

// houling use route-----------------------------------------------
Route::get('operational/houling_home', 'operational@index_houling');
Route::get('operational/houling_home/add', 'Operational@add_houling');
Route::post('operational/houling_home/do_add', 'Operational@do_add_houling');
Route::get('operational/houling_home/edit/{id}', 'Operational@edit_houling');
Route::put('operational/houling_home/do_edit/{id}', 'Operational@do_edit_houling');
Route::get('operational/houling_home/delete/{id}', 'Operational@delete_houling');

Route::get('operational/houling_home/search_car_houling', 'Operational@search_car_houling');
Route::get('operational/houling_home/search_employee', 'Operational@search_employee');
Route::get('operational/houling_home/search_route', 'Operational@search_route');
Route::get('operational/houling_home/search_tonase', 'Operational@search_tonase');

Route::post('operational/houling_home/getrange', 'Operational@houling_range');
Route::post('operational/houling_home/export', 'Operational@houling_export');
// ---------------------------------------------------------------

// finance
// buying use route-----------------------------------------------
Route::get('finance/buying_home', 'Finance@index_buying');
Route::post('finance/buying_home/export', 'Finance@index_buying_export');
Route::get('finance/buying_home/checkout/{id}', 'Finance@index_buying_checkout');
Route::post('finance/buying_home/do_checkout', 'Finance@index_buying_docheckout');
Route::get('finance/buying_home/add', 'Finance@add_buying');
Route::get('finance/buying_home/addnext/{id}', 'Finance@addnext_buying');
Route::post('finance/buying_home/do_add', 'Finance@do_add_buying');
Route::get('finance/buying_home/edit/{id}', 'Finance@edit_buying');
Route::put('finance/buying_home/do_edit/{id}', 'Finance@do_edit_buying');
Route::get('finance/buying_home/delete/{id}', 'Finance@delete_buying');
Route::get('finance/buying_home/delete_buying_detail/{id_detail}/{buyingid}', 'Finance@delete_buying_detail');
//Route::get('finance/buying_home/delete_buying_detail2/{id_detail}/{buyingid}', 'Finance@delete_buying_detail2');
// Route::get('finance/buying_home/export', 'Finance@buying_export');
Route::post('finance/buying_home/getrange', 'Finance@buying_range');
Route::get('finance/buying_home/search_items_buying', 'Finance@search_items_buying');
Route::get('finance/buying_home/search_supp_buying', 'Finance@search_supp_buying');

Route::get('finance/buying_home/tig/add/{totaluang}/{idretur}/{idreturdetail}', 'FinanceTig@tigadd_buying');
Route::get('finance/buying_home/tig/addnext/{id}/{totaluang}/{idretur}', 'FinanceTig@tigaddnext_buying');
Route::post('finance/buying_home/tig/do_add', 'FinanceTig@tigdo_add_buying');
Route::get('finance/buying_home/tig/checkout/{id}/{totaluang}/{idretur}', 'FinanceTig@tigindex_buying_checkout');
Route::post('finance/buying_home/tig/do_checkout', 'FinanceTig@tigindex_buying_docheckout');
Route::get('finance/buying_home/tig/delete_buying_detail/{id_detail}/{buyingid}/{totaluang}/{idretur}', 'FinanceTig@delete_buying_detail');

// selling use route-----------------------------------------------
Route::get('finance/selling_home', 'Finance@index_selling');
Route::post('finance/selling_home/export', 'Finance@index_selling_export');
Route::get('finance/selling_home/checkout/{id}', 'Finance@index_selling_checkout');
Route::post('finance/selling_home/do_checkout', 'Finance@index_selling_docheckout');
Route::get('finance/selling_home/add', 'Finance@add_selling');
Route::get('finance/selling_home/addnext/{id}', 'Finance@addnext_selling');
Route::post('finance/selling_home/do_add', 'Finance@do_add_selling');
Route::get('finance/selling_home/edit/{id}', 'Finance@edit_selling');
Route::put('finance/selling_home/do_edit/{id}', 'Finance@do_edit_selling');
Route::get('finance/selling_home/delete/{id}', 'Finance@delete_selling');
Route::get('finance/selling_home/delete_selling_detail/{id_detail}/{id_penjualan}', 'Finance@delete_selling_detail');
// Route::get('finance/selling_home/add_next/', 'Finance@add_next_selling');

Route::post('finance/selling_home/getrange', 'Finance@selling_range');
Route::get('finance/selling_home/search_employee_1', 'Finance@search_employee_1');
Route::get('finance/selling_home/search_employee_2', 'Finance@search_employee_2');
Route::get('finance/selling_home/search_employee_3', 'Finance@search_employee_3');
Route::get('finance/selling_home/search_employee_4', 'Finance@search_employee_4');
Route::get('finance/selling_home/search_employee_5', 'Finance@search_employee_5');
Route::get('finance/selling_home/search_items_selling', 'Finance@search_items_selling');
Route::get('finance/selling_home/search_customer_selling', 'Finance@search_customer_selling');

Route::get('finance/selling_home/tig/add/{totaluang}/{idretur}/{idreturdetail}', 'FinanceTig@tigadd_selling');
Route::get('finance/selling_home/tig/addnext/{id}/{totaluang}/{idretur}', 'FinanceTig@tigaddnext_selling');
Route::post('finance/selling_home/tig/do_add', 'FinanceTig@tigdo_add_selling');
Route::get('finance/selling_home/tig/checkout/{id}/{totaluang}/{idretur}', 'FinanceTig@tigindex_selling_checkout');
Route::post('finance/selling_home/tig/do_checkout', 'FinanceTig@tigindex_selling_docheckout');
Route::get('finance/selling_home/tig/delete_selling_detail/{id_detail}/{id_penjualan}/{totaluang}/{idretur}', 'FinanceTig@delete_selling_detail');

// debt use route-----------------------------------------------
Route::get('finance/debt_home', 'Finance@index_debt');
Route::post('finance/debt_home/export', 'Finance@index_debt_export');
Route::get('finance/debt_home/edit/{id}', 'Finance@edit_debt');
Route::put('finance/debt_home/do_edit', 'Finance@do_edit_debt');
// Route::get('finance/debt_home/export', 'Finance@debt_export');
Route::post('finance/debt_home/getrange', 'Finance@debt_range');

// return use route-----------------------------------------------
Route::get('finance/return_home', 'Finance@index_return');
Route::post('finance/return_home/export', 'Finance@index_return_export');
Route::get('finance/return_home/add', 'Finance@add_return');
Route::get('finance/return_home/addnext/{id}', 'Finance@next_return');
Route::get('finance/return_home/addnext/getbarang/{id}', 'Finance@next_return_get');
Route::post('finance/return_home/do_add', 'Finance@do_add_return');
Route::post('finance/return_home/do_addnext', 'Finance@do_addnext_return');
Route::get('finance/return_home/edit/{id}', 'Finance@edit_return');
Route::put('finance/return_home/do_edit/{id}', 'Finance@do_edit_return');
Route::get('finance/return_home/delete/{id}', 'Finance@delete_return');
Route::get('finance/return_home/addnext/cdb/{detailid}/{id}/{total}', 'Finance@addnext_cdb');
Route::post('finance/return_home/addnext/docdb', 'Finance@do_addnext_cdb');

Route::post('finance/return_home/getrange', 'Finance@return_range');

Route::get('finance/return_home/search_buying', 'Finance@search_buying');



// credit use route-----------------------------------------------
Route::get('finance/credit_home', 'Finance@index_credit');
Route::post('finance/credit_home/export', 'Finance@index_credit_export');
Route::get('finance/credit_home/edit/{id}', 'Finance@edit_credit');
Route::put('finance/credit_home/do_edit', 'Finance@do_edit_credit');

Route::post('finance/credit_home/getrange', 'Finance@credit_range');

// return sale use route-----------------------------------------------
Route::get('finance/salreturn_home/addnext/getbarang/{id}', 'Finance@salnext_return_get');
Route::get('finance/salreturn_home', 'Finance@index_salreturn');
Route::post('finance/salreturn_home/export', 'Finance@index_salreturn_export');
Route::get('finance/salreturn_home/add', 'Finance@add_salreturn');
Route::post('finance/salreturn_home/do_add', 'Finance@do_add_salreturn');
Route::post('finance/salreturn_home/do_addnext', 'Finance@saldo_addnext_salreturn');
Route::get('finance/salreturn_home/edit/{id}', 'Finance@edit_salreturn');
Route::put('finance/salreturn_home/do_edit/{id}', 'Finance@do_edit_salreturn');
Route::get('finance/salreturn_home/delete/{id}', 'Finance@delete_salreturn');
Route::get('finance/salreturn_home/addnext/cdb/{detailid}/{id}/{total}', 'Finance@saladdnext_cdb');
Route::post('finance/salreturn_home/addnext/docdb', 'Finance@saldo_addnext_cdb');

Route::post('finance/salreturn_home/getrange', 'Finance@salreturn_range');

Route::get('finance/salreturn_home/search_selling', 'Finance@search_selling');
Route::get('finance/salreturn_home/addnext/{id}', 'Finance@salnext_return');

// addtional use route---------------------------------------------
Route::get('finance/addtional_home', 'Finance@index_addtional');
Route::post('finance/addtional_home/exportt', 'Finance@export_addtional');
Route::get('finance/addtional_home/add', 'Finance@add_addtional');
Route::post('finance/addtional_home/do_addnext', 'Finance@do_add_addtional');
Route::get('finance/addtional_home/addnext/{id}', 'Finance@addnext_addtional');
Route::post('finance/addtional_home/do_add', 'Finance@do_add_addtional');
Route::get('finance/addtional_home/edit/{id}', 'Finance@edit_addtional');
Route::put('finance/addtional_home/do_edit/{id}', 'Finance@do_edit_addtional');
Route::get('finance/addtional_home/delete/{id}', 'Finance@delete_addtional');
Route::get('finance/addtional_home/delete_addtional_detail/{detailid}/{idmaster}', 'Finance@delete_addtional_detail');
Route::get('finance/addtional_home/checkout/{id}', 'Finance@index_addtional_checkout');
Route::post('finance/addtional_home/do_checkout', 'Finance@index_addtional_docheckout');

Route::post('finance/addtional_home/getrange', 'Finance@addtional_range');

// addtional use route---------------------------------------------
Route::get('finance/loan_home', 'Finance@index_loan');
Route::get('finance/loan_home/add', 'Finance@add_loan');
Route::post('finance/loan_home/do_add', 'Finance@do_add_loan');
Route::get('finance/loan_home/edit/{id}', 'Finance@edit_loan');
Route::put('finance/loan_home/do_edit/{id}', 'Finance@do_edit_loan');
Route::get('finance/loan_home/delete/{id}', 'Finance@delete_loan');

Route::post('finance/loan_home/getrange', 'Finance@loan_range');
Route::post('finance/loan_home/export', 'Finance@loan_export');
Route::get('finance/loan_home/search_loan_user', 'Finance@search_loan_user');

// module user_management------------------------------------------
Route::get('usermanagement/user_home', 'UserManagement@index');
Route::get('usermanagement/user_home/add', 'UserManagement@add_user');
Route::post('usermanagement/user_home/do_add', 'UserManagement@do_add_user');
Route::get('usermanagement/user_home/edit/{id}', 'UserManagement@edit_user');
Route::put('usermanagement/user_home/do_edit/{id}', 'UserManagement@do_edit_user');
Route::get('usermanagement/user_home/delete/{id}', 'UserManagement@delete_user');

Route::get('usermanagement/user_home/search_employee', 'UserManagement@search_employee');
Route::post('usermanagement/user_home/getrange', 'UserManagement@user_range');

// module user_management pass-------------------------------------
Route::get('usermanagement/user_pass_home', 'UserManagement@index_pass');
Route::get('usermanagement/user_pass_home/edit/{id}', 'UserManagement@edit_user_pass');
Route::put('usermanagement/user_pass_home/do_edit/{id}', 'UserManagement@do_edit_user_pass');
Route::get('usermanagement/user_pass_home/delete/{id}', 'UserManagement@delete_user_pass');

Route::get('usermanagement/user_pass_home/search_employee', 'UserManagement@search_employee_pass');
Route::post('usermanagement/user_pass_home/getrange', 'UserManagement@user_range_pass');

// module department-----------------------------------------------
Route::get('usermanagement/dep_home', 'UserManagement@index_dep');
Route::get('usermanagement/dep_home/add', 'UserManagement@add_dep');
Route::post('usermanagement/dep_home/do_add', 'UserManagement@do_add_dep');
Route::get('usermanagement/dep_home/edit/{id}', 'UserManagement@edit_dep');
Route::put('usermanagement/dep_home/do_edit/{id}', 'UserManagement@do_edit_dep');
Route::get('usermanagement/dep_home/delete/{id}', 'UserManagement@delete_dep');

Route::post('usermanagement/dep_home/getrange', 'UserManagement@dep_range');

// module position--------------------------------------------------
Route::get('usermanagement/pos_home', 'UserManagement@index_pos');
Route::get('usermanagement/pos_home/add', 'UserManagement@add_pos');
Route::post('usermanagement/pos_home/do_add', 'UserManagement@do_add_pos');
Route::get('usermanagement/pos_home/edit/{id}', 'UserManagement@edit_pos');
Route::put('usermanagement/pos_home/do_edit/{id}', 'UserManagement@do_edit_pos');
Route::get('usermanagement/pos_home/delete/{id}', 'UserManagement@delete_pos');

Route::post('usermanagement/pos_home/getrange', 'UserManagement@pos_range');

// profile setting--------------------------------------------------
Route::get('usermanagement/user_profile/{id}', 'UserManagement@profile');
Route::put('usermanagement/user_profile/do_edit/{id}', 'UserManagement@do_edit_profile');

// Develop ---------------------------------------------------------
// Logs
Route::get('developers/logs_home', 'Developers@index_logs');
Route::post('developers/logs_home/getrange', 'Developers@logs_range');
Route::put('developers/logs_home/do_edit/{id}', 'Developers@do_edit_logs');
// Destroy
Route::get('developers/destroy_home', 'Developers@index_destroy');
Route::post('developers/destroy_home/login', 'Developers@login_destroy');

// module level-----------------------------------------------------
Route::get('usermanagement/level_home', 'UserManagement@index_level');
Route::get('usermanagement/level_home/add', 'UserManagement@add_level');
Route::post('usermanagement/level_home/do_add', 'UserManagement@do_add_level');
Route::get('usermanagement/level_home/edit/{id}', 'UserManagement@edit_level');
Route::put('usermanagement/level_home/do_edit/{id}', 'UserManagement@do_edit_level');
Route::get('usermanagement/level_home/delete/{id}', 'UserManagement@delete_level');

Route::get('usermanagement/level_home/set_menu/{id}', 'UserManagement@edit_set_level');
Route::post('usermanagement/level_home/do_add_set_menu/', 'UserManagement@do_add_set_menu');

// fix
Route::post('temp/fix', 'HomeController@tempfix');
Route::get('temp/fix/get/{id}', 'HomeController@tempfixget');

// 404 page
Route::get('404', 'Developers@error');
