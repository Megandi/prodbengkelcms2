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


// DASHBOARD
	Route::get('dashboard/home', 'HomeController@index');
// ------------------------------------------------------------------------------


// BASE MENU---------------------------------------------------------------------

	// MAIN MENU
		// items type route----------------------------------------------------------

		Route::get('mainmenu/items_type_home', 'MainMenu\TypeItems@index_items_type');
		Route::get('mainmenu/items_type_home/add', 'MainMenu\TypeItems@add_items_type');
		Route::post('mainmenu/items_type_home/do_add', 'MainMenu\TypeItems@do_add_items_type');
		Route::get('mainmenu/items_type_home/edit/{id}', 'MainMenu\TypeItems@edit_items_type');
		Route::put('mainmenu/items_type_home/do_edit/{id}', 'MainMenu\TypeItems@do_edit_items_type');
		Route::get('mainmenu/items_type_home/delete/{id}', 'MainMenu\TypeItems@delete_items_type');

		Route::post('mainmenu/items_type_home/getrange', 'MainMenu\TypeItems@items_type_range');
		Route::post('mainmenu/items_type_home/export', 'MainMenu\TypeItems@items_type_export');

		// items route---------------------------------------------------------------
		Route::get('mainmenu/items_home', 'MainMenu\Items@index_items');
		Route::get('mainmenu/items_home/add', 'MainMenu\Items@add_items');
		Route::post('mainmenu/items_home/do_add', 'MainMenu\Items@do_add_items');
		Route::get('mainmenu/items_home/edit/{id}', 'MainMenu\Items@edit_items');
		Route::put('mainmenu/items_home/do_edit/{id}', 'MainMenu\Items@do_edit_items');
		Route::get('mainmenu/items_home/delete/{id}', 'MainMenu\Items@delete_items');

		Route::post('mainmenu/items_home/getrange', 'MainMenu\Items@items_range');
		Route::post('mainmenu/items_home/export', 'MainMenu\Items@items_export');

		// service route-------------------------------------------------------------
		Route::get('mainmenu/service_home', 'MainMenu\Service@index_service');
		Route::get('mainmenu/service_home/add', 'MainMenu\Service@add_service');
		Route::post('mainmenu/service_home/do_add', 'MainMenu\Service@do_add_service');
		Route::get('mainmenu/service_home/edit/{id}/{barang_id}', 'MainMenu\Service@edit_service');
		Route::put('mainmenu/service_home/do_edit/{id}/{barang_id}', 'MainMenu\Service@do_edit_service');
		Route::get('mainmenu/service_home/delete/{id}', 'MainMenu\Service@delete_service');

		Route::get('mainmenu/service_home/search_items_service', 'MainMenu\Service@search_items_service');
		Route::post('mainmenu/service_home/getrange', 'MainMenu\Service@service_range');
		Route::post('mainmenu/service_home/export', 'MainMenu\Service@service_export');

		// supplier route-------------------------------------------------------------
		Route::get('mainmenu/supp_home', 'MainMenu\Supplier@index_supp');
    Route::get('mainmenu/supp_home/detail/{id}', 'MainMenu\Supplier@detail_supp');
		Route::get('mainmenu/supp_home/add', 'MainMenu\Supplier@add_supp');
		Route::post('mainmenu/supp_home/do_add', 'MainMenu\Supplier@do_add_supp');
		Route::get('mainmenu/supp_home/edit/{id}', 'MainMenu\Supplier@edit_supp');
		Route::put('mainmenu/supp_home/do_edit/{id}', 'MainMenu\Supplier@do_edit_supp');
		Route::get('mainmenu/supp_home/delete/{id}', 'MainMenu\Supplier@delete_supp');

		Route::post('mainmenu/supp_home/getrange', 'MainMenu\Supplier@supp_range');
		Route::post('mainmenu/supp_home/export', 'MainMenu\Supplier@supp_export');

		// customer route-------------------------------------------------------------
		Route::get('mainmenu/cust_home', 'MainMenu\Customer@index_cust');
		Route::get('mainmenu/cust_home/detail/{id}', 'MainMenu\Customer@detail_cust');
		Route::get('mainmenu/cust_home/add', 'MainMenu\Customer@add_cust');
		Route::post('mainmenu/cust_home/do_add', 'MainMenu\Customer@do_add_cust');
		Route::get('mainmenu/cust_home/edit/{id}', 'MainMenu\Customer@edit_cust');
		Route::put('mainmenu/cust_home/do_edit/{id}', 'MainMenu\Customer@do_edit_cust');
		Route::get('mainmenu/cust_home/delete/{id}', 'MainMenu\Customer@delete_cust');

		Route::post('mainmenu/cust_home/getrange', 'MainMenu\Customer@cust_range');
		Route::post('mainmenu/cust_home/export', 'MainMenu\Customer@cust_export');

		// department route-----------------------------------------------------------
		Route::get('mainmenu/dep_home', 'MainMenu\Department@index_dep');
		Route::get('mainmenu/dep_home/add', 'MainMenu\Department@add_dep');
		Route::post('mainmenu/dep_home/do_add', 'MainMenu\Department@do_add_dep');
		Route::get('mainmenu/dep_home/edit/{id}', 'MainMenu\Department@edit_dep');
		Route::put('mainmenu/dep_home/do_edit/{id}', 'MainMenu\Department@do_edit_dep');
		Route::get('mainmenu/dep_home/delete/{id}', 'MainMenu\Department@delete_dep');

		Route::post('mainmenu/dep_home/getrange', 'MainMenu\Department@dep_range');

		// position route-------------------------------------------------------------
		Route::get('mainmenu/pos_home', 'MainMenu\Position@index_pos');
		Route::get('mainmenu/pos_home/add', 'MainMenu\Position@add_pos');
		Route::post('mainmenu/pos_home/do_add', 'MainMenu\Position@do_add_pos');
		Route::get('mainmenu/pos_home/edit/{id}', 'MainMenu\Position@edit_pos');
		Route::put('mainmenu/pos_home/do_edit/{id}', 'MainMenu\Position@do_edit_pos');
		Route::get('mainmenu/pos_home/delete/{id}', 'MainMenu\Position@delete_pos');

		Route::post('mainmenu/pos_home/getrange', 'MainMenu\Position@pos_range');

		// employee route-------------------------------------------------------------
		Route::get('mainmenu/emp_home', 'MainMenu\Employee@index_emp');
		Route::get('mainmenu/emp_home/detail/{id}', 'MainMenu\Employee@detail_emp');
		Route::get('mainmenu/emp_home/add', 'MainMenu\Employee@add_emp');
		Route::post('mainmenu/emp_home/do_add', 'MainMenu\Employee@do_add_emp');
		Route::get('mainmenu/emp_home/edit/{id}', 'MainMenu\Employee@edit_emp');
		Route::put('mainmenu/emp_home/do_edit/{id}', 'MainMenu\Employee@do_edit_emp');
		Route::get('mainmenu/emp_home/delete/{id}', 'MainMenu\Employee@delete_emp');

		Route::post('mainmenu/emp_home/getrange', 'MainMenu\Employee@emp_range');
		Route::post('mainmenu/emp_home/export', 'MainMenu\Employee@emp_export');

		// car route-----------------------------------------------
		Route::get('mainmenu/car_home', 'MainMenu\Car@index_car');
    Route::get('mainmenu/car_home/detail/{id}', 'MainMenu\Car@detail_car');
		Route::get('mainmenu/car_home/add', 'MainMenu\Car@add_car');
		Route::post('mainmenu/car_home/do_add', 'MainMenu\Car@do_add_car');
		Route::get('mainmenu/car_home/edit/{id}', 'MainMenu\Car@edit_car');
		Route::put('mainmenu/car_home/do_edit/{id}', 'MainMenu\Car@do_edit_car');
		Route::get('mainmenu/car_home/delete/{id}', 'MainMenu\Car@delete_car');

		Route::get('mainmenu/car_home/search_cust', 'MainMenu\Car@search_cust');
		Route::post('mainmenu/car_home/getrange', 'MainMenu\Car@car_range');
		Route::post('mainmenu/car_home/export', 'MainMenu\Car@car_export');

		// quarry route-----------------------------------------------
		Route::get('mainmenu/quar_home', 'MainMenu\Quarry@index_quar');
		Route::get('mainmenu/quar_home/add', 'MainMenu\Quarry@add_quar');
		Route::post('mainmenu/quar_home/do_add', 'MainMenu\Quarry@do_add_quar');
		Route::get('mainmenu/quar_home/edit/{id}', 'MainMenu\Quarry@edit_quar');
		Route::put('mainmenu/quar_home/do_edit/{id}', 'MainMenu\Quarry@do_edit_quar');
		Route::get('mainmenu/quar_home/delete/{id}', 'MainMenu\Quarry@delete_quar');

		Route::post('mainmenu/quar_home/getrange', 'MainMenu\Quarry@quar_range');
		Route::post('mainmenu/quar_home/export', 'MainMenu\Quarry@quar_export');

		// port route-----------------------------------------------
		Route::get('mainmenu/port_home', 'MainMenu\Port@index_port');
		Route::get('mainmenu/port_home/add', 'MainMenu\Port@add_port');
		Route::post('mainmenu/port_home/do_add', 'MainMenu\Port@do_add_port');
		Route::get('mainmenu/port_home/edit/{id}', 'MainMenu\Port@edit_port');
		Route::put('mainmenu/port_home/do_edit/{id}', 'MainMenu\Port@do_edit_port');
		Route::get('mainmenu/port_home/delete/{id}', 'MainMenu\Port@delete_port');

		Route::post('mainmenu/port_home/getrange', 'MainMenu\Port@port_range');
		Route::post('mainmenu/port_home/export', 'MainMenu\Port@port_export');

		// route manage route----------------------------------------------
		Route::get('mainmenu/route_home', 'MainMenu\Route@index_route');
		Route::get('mainmenu/route_home/add', 'MainMenu\Route@add_route');
		Route::post('mainmenu/route_home/do_add', 'MainMenu\Route@do_add_route');
		Route::get('mainmenu/route_home/edit/{id}', 'MainMenu\Route@edit_route');
		Route::put('mainmenu/route_home/do_edit/{id}', 'MainMenu\Route@do_edit_route');
		Route::get('mainmenu/route_home/delete/{id}', 'MainMenu\Route@delete_route');

		Route::get('mainmenu/route_home/search_route_a', 'MainMenu\Route@search_route_a');
		Route::get('mainmenu/route_home/search_route_b', 'MainMenu\Route@search_route_b');

		Route::post('mainmenu/route_home/getrange', 'MainMenu\Route@route_range');
		Route::post('mainmenu/route_home/export', 'MainMenu\Route@route_export');

		// solar type route----------------------------------------------
		Route::get('mainmenu/solar_type_home', 'MainMenu\SolarType@index_solar_type');
		Route::get('mainmenu/solar_type_home/add', 'MainMenu\SolarType@add_solar_type');
		Route::post('mainmenu/solar_type_home/do_add', 'MainMenu\SolarType@do_add_solar_type');
		Route::get('mainmenu/solar_type_home/edit/{id}', 'MainMenu\SolarType@edit_solar_type');
		Route::put('mainmenu/solar_type_home/do_edit/{id}', 'MainMenu\SolarType@do_edit_solar_type');
		Route::get('mainmenu/solar_type_home/delete/{id}', 'MainMenu\SolarType@delete_solar_type');

		Route::post('mainmenu/solar_type_home/getrange', 'MainMenu\SolarType@solar_type_range');
		Route::post('mainmenu/solar_type_home/export', 'MainMenu\SolarType@solar_type_export');

		// tonase route----------------------------------------------
		Route::get('mainmenu/tonase_home', 'MainMenu\Tonase@index_tonase');
		Route::get('mainmenu/tonase_home/add', 'MainMenu\Tonase@add_tonase');
		Route::post('mainmenu/tonase_home/do_add', 'MainMenu\Tonase@do_add_tonase');
		Route::get('mainmenu/tonase_home/edit/{id}', 'MainMenu\Tonase@edit_tonase');
		Route::put('mainmenu/tonase_home/do_edit/{id}', 'MainMenu\Tonase@do_edit_tonase');
		Route::get('mainmenu/tonase_home/delete/{id}', 'MainMenu\Tonase@delete_tonase');

		Route::get('mainmenu/tonase_home/search_tonase_route', 'MainMenu\Tonase@search_tonase_route');
		Route::post('mainmenu/tonase_home/getrange', 'MainMenu\Tonase@tonase_range');
		Route::post('mainmenu/tonase_home/export', 'MainMenu\Tonase@tonase_export');

	// --------------------------------------------------------------------------

	// HRD
		// salary route-----------------------------------------------
		Route::get('hrd/sal_home', 'Hrd\Salary@index_sal');
		Route::get('hrd/sal_home/add', 'Hrd\Salary@add_sal');
		Route::post('hrd/sal_home/do_add', 'Hrd\Salary@do_add_sal');
		Route::get('hrd/sal_home/edit/{id}', 'Hrd\Salary@edit_sal');
		Route::put('hrd/sal_home/do_edit/{id}', 'Hrd\Salary@do_edit_sal');
		Route::get('hrd/sal_home/delete/{id}', 'Hrd\Salary@delete_sal');

		Route::post('hrd/sal_home/getrange', 'Hrd\Salary@sal_range');
		Route::get('hrd/sal_home/search_employee', 'Hrd\Salary@search_employee');
		Route::post('hrd/sal_home/export', 'Hrd\Salary@sal_export');

	// --------------------------------------------------------------------------

	// BUYING
		// buying route----------------------------------------------------------
		Route::get('buying/buying_home', 'Buying\ManageBuying@index_buying');
		Route::get('buying/buying_home/detail/{id}', 'Buying\ManageBuying@detail_buying');
		Route::get('buying/buying_home/checkout/{id}', 'Buying\ManageBuying@index_buying_checkout');
		Route::post('buying/buying_home/do_checkout', 'Buying\ManageBuying@index_buying_docheckout');
		Route::get('buying/buying_home/add', 'Buying\ManageBuying@add_buying');
		Route::get('buying/buying_home/addnext/{id}', 'Buying\ManageBuying@addnext_buying');
		Route::post('buying/buying_home/do_add', 'Buying\ManageBuying@do_add_buying');
		Route::get('buying/buying_home/edit/{id}', 'Buying\ManageBuying@edit_buying');
		Route::put('buying/buying_home/do_edit/{id}', 'Buying\ManageBuying@do_edit_buying');
		Route::get('buying/buying_home/delete/{id}', 'Buying\ManageBuying@delete_buying');
		Route::get('buying/buying_home/delete_buying_detail/{id_detail}/{buyingid}', 'Buying\ManageBuying@delete_buying_detail');

		Route::get('buying/buying_home/search_items_buying', 'Buying\ManageBuying@search_items_buying');
		Route::get('buying/buying_home/search_supp_buying', 'Buying\ManageBuying@search_supp_buying');
		Route::post('buying/buying_home/getrange', 'Buying\ManageBuying@buying_range');
		Route::post('buying/buying_home/export', 'Buying\ManageBuying@index_buying_export');

		// TIG FINANCE
			Route::get('buying/buying_home/tig/add/{totaluang}/{idretur}/{idreturdetail}', 'Tig\FinanceTig@tigadd_buying');
			Route::get('buying/buying_home/tig/addnext/{id}/{totaluang}/{idretur}', 'Tig\FinanceTig@tigaddnext_buying');
			Route::post('buying/buying_home/tig/do_add', 'Tig\FinanceTig@tigdo_add_buying');
			Route::get('buying/buying_home/tig/checkout/{id}/{totaluang}/{idretur}', 'Tig\FinanceTig@tigindex_buying_checkout');
			Route::post('buying/buying_home/tig/do_checkout', 'Tig\FinanceTig@tigindex_buying_docheckout');
			Route::get('buying/buying_home/tig/delete_buying_detail/{id_detail}/{buyingid}/{totaluang}/{idretur}', 'Tig\FinanceTig@delete_buying_detail');
		// TIG FINANCE

		// debt route------------------------------------------------------------
		Route::get('buying/debt_home', 'Buying\Debt@index_debt');
		Route::get('buying/debt_home/edit/{id}', 'Buying\Debt@edit_debt');
		Route::put('buying/debt_home/do_edit', 'Buying\Debt@do_edit_debt');
		Route::post('buying/debt_home/getrange', 'Buying\Debt@debt_range');
		Route::post('buying/debt_home/export', 'Buying\Debt@index_debt_export');

	// --------------------------------------------------------------------------

	// SELLING
		// selling route---------------------------------------------------------
		Route::get('selling/selling_home', 'Selling\ManageSelling@index_selling');
    Route::get('selling/selling_home/detail/{id}', 'Selling\ManageSelling@detail_selling');
		Route::get('selling/selling_home/checkout/{id}', 'Selling\ManageSelling@index_selling_checkout');
		Route::post('selling/selling_home/do_checkout', 'Selling\ManageSelling@index_selling_docheckout');
		Route::get('selling/selling_home/add', 'Selling\ManageSelling@add_selling');
		Route::get('selling/selling_home/addnext/{id}', 'Selling\ManageSelling@addnext_selling');
		Route::post('selling/selling_home/do_add', 'Selling\ManageSelling@do_add_selling');
		Route::get('selling/selling_home/edit/{id}', 'Selling\ManageSelling@edit_selling');
		Route::put('selling/selling_home/do_edit/{id}', 'Selling\ManageSelling@do_edit_selling');
		Route::get('selling/selling_home/delete/{id}', 'Selling\ManageSelling@delete_selling');
		Route::get('selling/selling_home/delete_selling_detail/{id_detail}/{id_penjualan}', 'Selling\ManageSelling@delete_selling_detail');

		Route::get('selling/selling_home/search_employee_1', 'Selling\ManageSelling@search_employee_1');
		Route::get('selling/selling_home/search_employee_2', 'Selling\ManageSelling@search_employee_2');
		Route::get('selling/selling_home/search_employee_3', 'Selling\ManageSelling@search_employee_3');
		Route::get('selling/selling_home/search_employee_4', 'Selling\ManageSelling@search_employee_4');
		Route::get('selling/selling_home/search_employee_5', 'Selling\ManageSelling@search_employee_5');
		Route::get('selling/selling_home/search_items_selling', 'Selling\ManageSelling@search_items_selling');
		Route::get('selling/selling_home/search_customer_selling', 'Selling\ManageSelling@search_customer_selling');

		Route::post('selling/selling_home/getrange', 'Selling\ManageSelling@selling_range');
		Route::post('selling/selling_home/export', 'Selling\ManageSelling@index_selling_export');

		// TIG FINANCE
			Route::get('selling/selling_home/tig/add/{totaluang}/{idretur}/{idreturdetail}', 'Tig\FinanceTig@tigadd_selling');
			Route::get('selling/selling_home/tig/addnext/{id}/{totaluang}/{idretur}', 'Tig\FinanceTig@tigaddnext_selling');
			Route::post('selling/selling_home/tig/do_add', 'Tig\FinanceTig@tigdo_add_selling');
			Route::get('selling/selling_home/tig/checkout/{id}/{totaluang}/{idretur}', 'Tig\FinanceTig@tigindex_selling_checkout');
			Route::post('selling/selling_home/tig/do_checkout', 'Tig\FinanceTig@tigindex_selling_docheckout');
			Route::get('selling/selling_home/tig/delete_selling_detail/{id_detail}/{id_penjualan}/{totaluang}/{idretur}', 'Tig\FinanceTig@delete_selling_detail');
		// TIG FINANCE

		// credit route----------------------------------------------------------
		Route::get('selling/credit_home', 'Selling\Credit@index_credit');
		Route::get('selling/credit_home/edit/{id}', 'Selling\Credit@edit_credit');
		Route::put('selling/credit_home/do_edit', 'Selling\Credit@do_edit_credit');
		Route::post('selling/credit_home/getrange', 'Selling\Credit@credit_range');
		Route::post('selling/credit_home/export', 'Selling\Credit@index_credit_export');

	// --------------------------------------------------------------------------

	// PURCHASE RETURN
		// purchase return route-------------------------------------------------
		Route::get('purchase/return_home', 'Purchase\ReturnBuying@index_return');
		Route::get('purchase/return_home/add', 'Purchase\ReturnBuying@add_return');
		Route::get('purchase/return_home/addnext/{id}', 'Purchase\ReturnBuying@next_return');
		Route::get('purchase/return_home/addnext/getbarang/{id}', 'Purchase\ReturnBuying@next_return_get');
		Route::post('purchase/return_home/do_add', 'Purchase\ReturnBuying@do_add_return');
		Route::post('purchase/return_home/do_addnext', 'Purchase\ReturnBuying@do_addnext_return');
		Route::get('purchase/return_home/edit/{id}', 'Purchase\ReturnBuying@edit_return');
		Route::put('purchase/return_home/do_edit/{id}', 'Purchase\ReturnBuying@do_edit_return');
		Route::get('purchase/return_home/delete/{id}', 'Purchase\ReturnBuying@delete_return');
		Route::get('purchase/return_home/addnext/cdb/{detailid}/{id}/{total}', 'Purchase\ReturnBuying@addnext_cdb');
		Route::post('purchase/return_home/addnext/docdb', 'Purchase\ReturnBuying@do_addnext_cdb');

		Route::get('purchase/return_home/search_buying', 'Purchase\ReturnBuying@search_buying');

		Route::post('purchase/return_home/getrange', 'Purchase\ReturnBuying@return_range');
		Route::post('purchase/return_home/export', 'Purchase\ReturnBuying@index_return_export');

	// --------------------------------------------------------------------------

	// SELLING RETURN
		// sales return route-----------------------------------------------
		Route::get('sales/salreturn_home/addnext/getbarang/{id}', 'Sales\ReturnSelling@salnext_return_get');
		Route::get('sales/salreturn_home', 'Sales\ReturnSelling@index_salreturn');
		Route::get('sales/salreturn_home/add', 'Sales\ReturnSelling@add_salreturn');
		Route::post('sales/salreturn_home/do_add', 'Sales\ReturnSelling@do_add_salreturn');
		Route::post('sales/salreturn_home/do_addnext', 'Sales\ReturnSelling@saldo_addnext_salreturn');
		Route::get('sales/salreturn_home/edit/{id}', 'Sales\ReturnSelling@edit_salreturn');
		Route::put('sales/salreturn_home/do_edit/{id}', 'Sales\ReturnSelling@do_edit_salreturn');
		Route::get('sales/salreturn_home/delete/{id}', 'Sales\ReturnSelling@delete_salreturn');
		Route::get('sales/salreturn_home/addnext/cdb/{detailid}/{id}/{total}', 'Sales\ReturnSelling@saladdnext_cdb');
		Route::post('sales/salreturn_home/addnext/docdb', 'Sales\ReturnSelling@saldo_addnext_cdb');

		Route::get('sales/salreturn_home/search_selling', 'Sales\ReturnSelling@search_selling');
		Route::get('sales/salreturn_home/addnext/{id}', 'Sales\ReturnSelling@salnext_return');

		Route::post('sales/salreturn_home/getrange', 'Sales\ReturnSelling@salreturn_range');
		Route::post('sales/salreturn_home/export', 'Sales\ReturnSelling@index_salreturn_export');

	// --------------------------------------------------------------------------

	// ADDTIONAL
		// addtional route-------------------------------------------------------
		Route::get('addtional/addtional_home', 'Addtional\AddtionalCost@index_addtional');
		Route::get('addtional/addtional_home/detail/{id}', 'Addtional\AddtionalCost@detail_addtional');
		Route::get('addtional/addtional_home/add', 'Addtional\AddtionalCost@add_addtional');
		Route::post('addtional/addtional_home/do_addnext', 'Addtional\AddtionalCost@do_add_addtional');
		Route::get('addtional/addtional_home/addnext/{id}', 'Addtional\AddtionalCost@addnext_addtional');
		Route::post('addtional/addtional_home/do_add', 'Addtional\AddtionalCost@do_add_addtional');
		Route::get('addtional/addtional_home/edit/{id}', 'Addtional\AddtionalCost@edit_addtional');
		Route::put('addtional/addtional_home/do_edit/{id}', 'Addtional\AddtionalCost@do_edit_addtional');
		Route::get('addtional/addtional_home/delete/{id}', 'Addtional\AddtionalCost@delete_addtional');
		Route::get('addtional/addtional_home/delete_addtional_detail/{detailid}/{idmaster}', 'Addtional\AddtionalCost@delete_addtional_detail');
		Route::get('addtional/addtional_home/checkout/{id}', 'Addtional\AddtionalCost@index_addtional_checkout');
		Route::post('addtional/addtional_home/do_checkout', 'Addtional\AddtionalCost@index_addtional_docheckout');

		Route::post('addtional/addtional_home/getrange', 'Addtional\AddtionalCost@addtional_range');
		Route::post('addtional/addtional_home/exportt', 'Addtional\AddtionalCost@export_addtional');

		// loan route------------------------------------------------------------
		Route::get('addtional/loan_home', 'Addtional\Loan@index_loan');
		Route::get('addtional/loan_home/add', 'Addtional\Loan@add_loan');
		Route::post('addtional/loan_home/do_add', 'Addtional\Loan@do_add_loan');
		Route::get('addtional/loan_home/edit/{id}', 'Addtional\Loan@edit_loan');
		Route::put('addtional/loan_home/do_edit/{id}', 'Addtional\Loan@do_edit_loan');
		Route::get('addtional/loan_home/delete/{id}', 'Addtional\Loan@delete_loan');

		Route::get('addtional/loan_home/search_loan_user', 'Addtional\Loan@search_loan_user');

		Route::post('addtional/loan_home/getrange', 'Addtional\Loan@loan_range');
		Route::post('addtional/loan_home/export', 'Addtional\Loan@loan_export');

	// --------------------------------------------------------------------------

	// HOULING
		// houling route-----------------------------------------------
		Route::get('houling/houling_home', 'Houling\ManageHouling@index_houling');
		Route::get('houling/houling_home/add', 'Houling\ManageHouling@add_houling');
		Route::post('houling/houling_home/do_add', 'Houling\ManageHouling@do_add_houling');
		Route::get('houling/houling_home/edit/{id}', 'Houling\ManageHouling@edit_houling');
		Route::put('houling/houling_home/do_edit/{id}', 'Houling\ManageHouling@do_edit_houling');
		Route::get('houling/houling_home/delete/{id}', 'Houling\ManageHouling@delete_houling');

		Route::get('houling/houling_home/search_car_houling', 'Houling\ManageHouling@search_car_houling');
		Route::get('houling/houling_home/search_employee', 'Houling\ManageHouling@search_employee');
		Route::get('houling/houling_home/search_route', 'Houling\ManageHouling@search_route');
		Route::get('houling/houling_home/search_tonase', 'Houling\ManageHouling@search_tonase');

		Route::post('houling/houling_home/getrange', 'Houling\ManageHouling@houling_range');
		Route::post('houling/houling_home/export', 'Houling\ManageHouling@houling_export');

		// solar usage route-----------------------------------------------
		Route::get('houling/solar_home', 'Houling\SolarUsage@index_solar');
		Route::get('houling/solar_home/add', 'Houling\SolarUsage@add_solar');
		Route::post('houling/solar_home/do_add', 'Houling\SolarUsage@do_add_solar');
		Route::get('houling/solar_home/edit/{id}', 'Houling\SolarUsage@edit_solar');
		Route::put('houling/solar_home/do_edit/{id}', 'Houling\SolarUsage@do_edit_solar');
		Route::get('houling/solar_home/delete/{id}', 'Houling\SolarUsage@delete_solar');

		Route::get('houling/solar_home/search_car', 'Houling\SolarUsage@search_car');
		Route::get('houling/solar_home/search_employee_solar', 'Houling\SolarUsage@search_employee_solar');

		Route::post('houling/solar_home/getrange', 'Houling\SolarUsage@solar_range');
		Route::post('houling/solar_home/export', 'Houling\SolarUsage@solar_export');

	// --------------------------------------------------------------------------

	// ADMIN
		// user list route-------------------------------------------------------
		Route::get('admin/user_home', 'Admin\UserList@index');
		Route::get('admin/user_home/add', 'Admin\UserList@add_user');
		Route::post('admin/user_home/do_add', 'Admin\UserList@do_add_user');
		Route::get('admin/user_home/edit/{id}', 'Admin\UserList@edit_user');
		Route::put('admin/user_home/do_edit/{id}', 'Admin\UserList@do_edit_user');
		Route::get('admin/user_home/delete/{id}', 'Admin\UserList@delete_user');

		Route::get('admin/user_home/search_employee', 'Admin\UserList@search_employee');
		Route::post('admin/user_home/getrange', 'Admin\UserList@user_range');

		// user pass route-------------------------------------------------------
		Route::get('admin/user_pass_home', 'Admin\UserPass@index_pass');
		Route::get('admin/user_pass_home/edit/{id}', 'Admin\UserPass@edit_user_pass');
		Route::put('admin/user_pass_home/do_edit/{id}', 'Admin\UserPass@do_edit_user_pass');
		Route::get('admin/user_pass_home/delete/{id}', 'Admin\UserPass@delete_user_pass');

		Route::get('admin/user_pass_home/search_employee', 'Admin\UserPass@search_employee_pass');
		Route::post('admin/user_pass_home/getrange', 'Admin\UserPass@user_range_pass');

		// user level route-------------------------------------------------------
		Route::get('admin/level_home', 'Admin\UserLevel@index_level');
		Route::get('admin/level_home/add', 'Admin\UserLevel@add_level');
		Route::post('admin/level_home/do_add', 'Admin\UserLevel@do_add_level');
		Route::get('admin/level_home/edit/{id}', 'Admin\UserLevel@edit_level');
		Route::put('admin/level_home/do_edit/{id}', 'Admin\UserLevel@do_edit_level');
		Route::get('admin/level_home/delete/{id}', 'Admin\UserLevel@delete_level');

		Route::get('admin/level_home/set_menu/{id}', 'Admin\UserLevel@edit_set_level');
		Route::post('admin/level_home/do_add_set_menu/', 'Admin\UserLevel@do_add_set_menu');

		// Logs list route--------------------------------------------------------
		Route::get('admin/logs_home', 'Admin\LogsList@index_logs');
		Route::post('admin/logs_home/getrange', 'Admin\LogsList@logs_range');
		Route::put('admin/logs_home/do_edit/{id}', 'Admin\LogsList@do_edit_logs');

		// Destroy route----------------------------------------------------------
		Route::get('admin/destroy_home', 'Admin\DestroyData@index_destroy');
		Route::post('admin/destroy_home/login', 'Admin\DestroyData@login_destroy');

		// Profile setting--------------------------------------------------
		Route::get('usermanagement/user_profile/{id}', 'Profile\UserProfile@profile');
		Route::put('usermanagement/user_profile/do_edit/{id}', 'Profile\UserProfile@do_edit_profile');

	// --------------------------------------------------------------------------

// ------------------------------------------------------------------------------


// SECURITY----------------------------------------------------------------------
	// fix
		Route::post('temp/fix', 'HomeController@tempfix');
		Route::get('temp/fix/get/{id}', 'HomeController@tempfixget');
	// 404 page
		// Route::get('404', 'Developers@error');
// ------------------------------------------------------------------------------
