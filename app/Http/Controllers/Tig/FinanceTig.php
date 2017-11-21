<?php

namespace App\Http\Controllers\Tig;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use App\Models\manage_buying;
use App\Models\manage_buying_history;
use App\Models\manage_biayalain;
use App\Models\manage_biayalain_detail;
use App\Models\manage_debt;
use App\Models\manage_debt_history;
use App\Models\manage_purchase_return;
use App\Models\manage_selling;
use App\Models\manage_selling_history;
use App\Models\manage_sales_return;
use App\Models\manage_credit;
use App\Models\manage_credit_history;
use App\Models\manage_returnpembelian;
use App\Models\manage_returnpenjualan;
use App\Models\manage_service;

use App\Models\manage_items;
use App\Models\manage_items_temp;
use App\Models\deposit_cust;
use App\Models\deposit_supp;
use Auth;

class FinanceTig extends Controller
{
    public function tigadd_buying($totaluang,$idretur,$iddetailretur)
    {
      $validasi = 0;
      return view('buying/buying/tig/buyingadd')->with('validasi', $validasi)
                                                ->with('idretur', $idretur)
                                                ->with('iddetailretur', $iddetailretur)
                                                ->with('totaluang', $totaluang);
    }

    public function tigaddnext_buying($id,$totaluang,$idretur)
    {
      $buying = manage_buying::find($id);
      $validasi = 1;

      //menghitung total------------------------------------------------------------------------
      $total = 0;
      $details = manage_buying_history::where('ms_pembelian_id', $id)->get();
      foreach ($details as $detail) {
        $total += $detail->sub_total_pembelian * $detail->qty;
      }
      //menghitung total------------------------------------------------------------------------

      return view('buying/buying/tig/buying_addnext')->with('buying', $buying)
                                              ->with('validasi', $validasi)
                                              ->with('totaluang', $totaluang)
                                              ->with('idretur', $idretur)
                                              ->with('total', $total);
    }

    public function tigdo_add_buying(Request $request)
    {
        $idretur = $request->idretur;

        // validasi untuk validate 2 kondisi
        if($request->status_payment != '1'){
            $this->validate($request, [
                'items_id'              => 'required|min:1|max:20',
                'items_qty_view'        => 'required|max:20',
                'items_sub_total_view'  => 'required|max:20'
            ]);
        }else{
            $this->validate($request, [
                'items_id'              => 'required|min:1|max:20',
                'items_qty_view'        => 'required|max:20',
                'items_sub_total_view'  => 'required|max:20'
            ]);
        }
        // validasi untuk validate 2 kondisi

        //buying id validasi----------------------------------------------------------------------
        if($request->validasi==0){
          //$msid = $data->id;
        } else {
          $msid = $request->id_pembelian;
        }
        //buying id validasi----------------------------------------------------------------------

        // calculate total pembelian-------------------------------------------------------------
        $total_price = $request->items_qty_view * $request->items_sub_total_view;
        // calculate total pembelian-------------------------------------------------------------
        // set format id hutang------------------------------------------------------------------

        // validasi quantities-------------------------------------------------------------------
        if($total_price == 0){
          if($request->validasi==0){
            return redirect(url('buying/buying_home/tig/add/'.$totaluang.'/'.$idretur.'/'.$request->iddetailretur))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
          } else {
            return redirect(url('buying/buying_home/tig/addnext/'.$msid.'/'.$totaluang.'/'.$idretur))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
          }
        }
        // validasi quantities-------------------------------------------------------------------

        if($request->validasi==0){
          //buat id
          $buying = manage_buying::orderBy('id', 'DESC')->first();
          if($buying){
            $lastnumber = substr($buying->pembelian_id, 1, 9);
            $idnumber = $lastnumber + 1;

            $buyingid = "B".sprintf("%09d", $idnumber);
          } else {
            $buyingid = "B000000001";
          }

          //buat id
          // save to ms_pembelian------------------------------------------------------------------
          $data = new manage_buying;
          $data->pembelian_id                     = $buyingid;
          $data->created_date                     = date('Y-m-d H:i:s');
          $data->last_modify_date                 = date('Y-m-d H:i:s');
          $data->returdetail_id                   = $request->iddetailretur;
          $data->modify_user_id                   = Auth::user()->karyawan_id;
          $data->status_transaksi                 = 0;
          $data->status                           = 'A';
          $data->save();
        }
        // save to ms_pembelian-------------------------------------------------------------------

        //buying id validasi----------------------------------------------------------------------
        if($request->validasi==0){
          $msid = $data->id;
        } else {
          $msid = $request->id_pembelian;
        }
        //buying id validasi----------------------------------------------------------------------

        // save to tr_detail_pembelian------------------------------------------------------------
        if($request->items_id=="addnew"){
          $datatemp = new manage_items_temp;
          $datatemp->nama = $request->new_item;
          $datatemp->qty = $request->items_qty_view;
          $datatemp->harga_jual = $request->items_sellprice;
          $datatemp->harga_beli = $request->items_sub_total_view;
          $datatemp->created_date = date('Y-m-d H:i:s');
          $datatemp->save();

          $datadetail = new manage_buying_history;
          $datadetail->ms_pembelian_id             	 		= $msid;
          $datadetail->barang_id                        = $datatemp->id;
          $datadetail->qty                              = $request->items_qty_view;
          $datadetail->sub_total_pembelian              = $request->items_sub_total_view;
          $datadetail->created_date                     = date('Y-m-d H:i:s');
          $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
          $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
          $datadetail->status                           = 'T';
          $datadetail->save();

        } else {
          $datadetail = new manage_buying_history;
          $datadetail->ms_pembelian_id             	 		= $msid;
          $datadetail->barang_id                        = $request->items_id;
          $datadetail->qty                              = $request->items_qty_view;
          $datadetail->sub_total_pembelian              = $request->items_sub_total_view;
          $datadetail->created_date                     = date('Y-m-d H:i:s');
          $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
          $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
          $datadetail->status                           = 'A';
          $datadetail->save();
        }
        // save to tr_detail_pembelian------------------------------------------------------------

        $totaluang = $request->totaluang;

        return redirect(url('buying/buying_home/tig/addnext/'.$msid.'/'.$totaluang.'/'.$idretur))->with('status', ' Created new buying has been success.');
    }

    // manage buying checkout function--------------------------------------------------
		public function tigindex_buying_checkout($id,$totaluang,$idretur)
		{
			$buying = manage_buying::find($id);

			//menghitung total------------------------------------------------------------------------
			$total = 0;
			$details = manage_buying_history::where('ms_pembelian_id', $id)->get();
			foreach ($details as $detail) {
				$total += $detail->sub_total_pembelian * $detail->qty;
			}
			//menghitung total------------------------------------------------------------------------

			return view('buying/buying/tig/checkout')->with('total', $total)
																						->with('buying', $buying)
                                            ->with('idretur', $idretur)
																						->with('totaluang', $totaluang);
		}
		// ---------------------------------------------------------------------------------

		// manage buying do checkout function-----------------------------------------------
		public function tigindex_buying_docheckout(Request $request)
		{
		      $idretur = $request->idretur;
					$buying = manage_buying::find($request->buyingid);
		      $totaluang = $request->totaluang;
		      $totalpricereal = 0;

					//menghitung total------------------------------------------------------------------------
					$total_price = 0;
					$details = manage_buying_history::where('ms_pembelian_id', $request->buyingid)->get();
					foreach ($details as $detail) {
						$total_price += $detail->sub_total_pembelian * $detail->qty;
						// // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
						if($detail->status!="T"){
							$item = manage_items::find($detail->barang_id);
							if($item->harga!=$detail->sub_total_pembelian){
								$itemadd = new manage_items;
								$itemadd->barang_id = $item->barang_id;
								$itemadd->nama = $item->nama;
								$itemadd->kategori = $item->kategori;
								$itemadd->spesifikasi = $item->spesifikasi;
								$itemadd->harga = $detail->sub_total_pembelian;
								$itemadd->harga_jual = $item->harga_jual;
								$itemadd->stock = $detail->qty;
								$itemadd->is_available = $item->is_available;
								$itemadd->created_date = date('Y-m-d H:i:s');
								$itemadd->last_modify_date = date('Y-m-d H:i:s');
								$itemadd->modify_user_id = Auth::user()->karyawan_id;
								$itemadd->status = "A";
								$itemadd->save();
							} else {
								if($item->is_available==0){
									$item->stock = $detail->qty;
								} else {
									$item->stock = $item->stock + $detail->qty;
								}
								$item->is_available = 1;
								$item->last_modify_date = date('Y-m-d H:i:s');
								$item->modify_user_id = Auth::user()->karyawan_id;
								$item->status = 'A';
								$item->save();
							}
						}
						// update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
					}
					//menghitung total------------------------------------------------------------------------
				      $totalpricereal = $total_price - $totaluang;
				      if($totalpricereal<0){
				        $totalpricereal = 0;
				      }

					// set format id hutang------------------------------------------------------------------
					$for_date_debt      = date("dmy",strtotime($request->buy_date));
					$for_buy_id         = substr($buying->pembelian_id,0,10);

					$format_debt_id = $for_buy_id .'-'. $for_date_debt;
					// set format id hutang------------------------------------------------------------------

					// // validasi saat unpaid dan paid---------------------------------------------------------
					if($request->status_payment != '1'){
					    if($request->sell_dp > $totalpricereal){
					        return redirect(url('buying/buying_home/tig/checkout'))->with('error', ' Down payment must less than Grand Total');
					    }

					    if($request->sell_dp == $totalpricereal){
					        return redirect(url('buying/buying_home/tig/checkout'))->with('error', ' Down payment must less than Grand Total');
					    }
					}
					// validasi saat unpaid dan paid---------------------------------------------------------

					// Edit to ms_pembelian------------------------------------------------------------------
						$data = manage_buying::find($request->buyingid);
						$data->no_nota                          = $request->no_nota;
						$data->supplier_id                      = $request->supp_id;
						$data->status_bayar_pembelian           = $request->status_payment;
						$data->tanggal                     			= date('Y-m-d H:i:s');
						$data->status_transaksi                 = 0;
						if($request->status_payment != '0'){
						    $data->pembelian_bayar                  = $totalpricereal;
						    $data->tanggal_jatuh_tempo_pembelian    = date('Y-m-d H:i:s');
						    $data->pembelian_dp                     = 0;
						}else{
						    $data->pembelian_bayar                  = $request->sell_dp;
						    $data->tanggal_jatuh_tempo_pembelian    = $request->sell_due_date;
						    $data->pembelian_dp                     = $request->sell_dp;
						}
						$data->pembelian_total                  = $totalpricereal;
						$data->save();
					// Edit to ms_pembelian-------------------------------------------------------------------

					//cek manage isinya//
					$debttt = manage_debt::orderBy('id', 'DESC')->first();
					if($debttt){
						$lastnumber = substr($debttt->hutang_id, 3, 7);
						$idnumber = $lastnumber + 1;

						$debtttid = "DEB".sprintf("%07d", $idnumber);
					} else {
						$debtttid = "DEB0000001";
					}
					//cek manage isinya//

					// // save ke lt_hutang-----------------------------------------------------------------------
					$data = new manage_debt;
					$data->hutang_id                        = $debtttid;
					$data->pembelian_id                     = $request->buyingid;

					// jika pembelian langsung lunas
					if($request->status_payment > 0){
					    $data->total                        = 0;
					    $data->bayar                        = 0;
					    $data->status_hutang                = 2;
					// jika pembelian tidak langsung lunas
					}else if($request->status_payment < 1){
					    $data->total                        = $totalpricereal;
					    $data->bayar                        = $request->sell_dp;
					    $data->status_hutang                = 1;
					}

					$data->created_date                     = date('Y-m-d H:i:s');
					$data->last_modify_date                 = date('Y-m-d H:i:s');
					$data->modify_user_id                   = Auth::user()->karyawan_id;
					$data->status                           = 'A';
					$data->save();
					// save ke lt_hutang-----------------------------------------------------------------------

					// update total hutang ke tr_deposit_supplier ---------------------------------------------
					if($request->status_payment < 1){
					    deposit_supp::where('supplier_id',$request->supp_id)->update([
					        'deposit' => ($totalpricereal -$request->sell_dp) + $request->supp_deposit,
					        'last_modify_date' => date('Y-m-d H:i:s'),
					        'modify_user_id' => Auth::user()->karyawan_id,
					        'status' => 'A'
					    ]);
					}
					// update total hutang ke tr_deposit_supplier ---------------------------------------------

					if($request->status_payment < 1){
						$debthistory = new manage_debt_history;
						$debthistory->hutang_id 								= $data->id;
						$debthistory->total_hutang	 						= $totalpricereal;
						$debthistory->total_pembayaran_hutang 	= $request->sell_dp;;
						$debthistory->created_date              = date('Y-m-d H:i:s');
						$debthistory->last_modify_date          = date('Y-m-d H:i:s');
						$debthistory->modify_user_id            = Auth::user()->karyawan_id;
						$debthistory->status                  	= 'A';
						$debthistory->save();
					}

					return redirect(url('purchase/return_home/addnext/'.$idretur));
				}
		// ---------------------------------------------------------------------------------

    public function delete_buying_detail($detailid,$buyingid,$totaluang,$idretur)
		{
			$detail = manage_buying_history::find($detailid);
			manage_items_temp::destroy($detail->barang_id);
			manage_buying_history::destroy($detailid);
			return redirect(url('buying/buying_home/tig/addnext/'.$buyingid.'/'.$totaluang.'/'.$idretur))->with('status', ' Deleted buying has been success.');
		}


    public function delete_selling_detail($detailid,$sellingid,$totaluang,$idretur)
		{
      $detail = manage_selling_history::find($detailid);
      manage_items_temp::destroy($detail->barang_id);
      manage_selling_history::destroy($detailid);
			return redirect(url('selling/selling_home/tig/addnext/'.$sellingid.'/'.$totaluang.'/'.$idretur))->with('status', ' Deleted buying has been success.');
		}


    // manage selling add function------------------------------------------------------
    public function tigadd_selling($totaluang,$idretur,$iddetailretur)
    {
        $validasi = 0;
        return view('selling/selling/tig/sellingadd')->with('validasi', $validasi)
                                                  ->with('idretur', $idretur)
                                                  ->with('iddetailretur', $iddetailretur)
                                                  ->with('totaluang', $totaluang);
    }
    // ---------------------------------------------------------------------------------

    // manage selling add next function-------------------------------------------------
    public function tigaddnext_selling($id,$totaluang,$idretur)
    {
      $selling = manage_selling::find($id);
      $validasi = 1;

      //menghitung total------------------------------------------------------------------------
      $total = 0;
      $details = manage_selling_history::where('detail_penjualan_id', $id)->get();
      foreach ($details as $detail) {
        $total += $detail->sub_total_penjualan * $detail->qty;
      }
      //menghitung total------------------------------------------------------------------------

      return view('selling/selling/tig/sellingaddnext')->with('selling', $selling)
                                                  ->with('validasi', $validasi)
                                                  ->with('idretur', $idretur)
                                                  ->with('totaluang', $totaluang)
                                                  ->with('total', $total);
    }
    // ---------------------------------------------------------------------------------

    // manage selling do add function---------------------------------------------------
    public function tigdo_add_selling(Request $request)
    {
      $idretur = $request->idretur;

      $type = $request->type_id;
      //buying id validasi----------------------------------------------------------------------
      if($request->validasi==0){
        //$msid = $data->id;
      } else {
        $msid = $request->id_penjualan;
      }
      //buying id validasi----------------------------------------------------------------------

      if($request->items_id!="addnew"){
          if($request->items_qty_view > $request->items_qty_viewasli){
              if($request->validasi==0){
                return redirect(url('selling/selling_home/tig/add/'.$totaluang.'/'.$idretur.'/'.$request->iddetailretur))->with('error', ' Quantities not Enough.');
              } else {
                return redirect(url('selling/selling_home/tig/addnext/'.$request->id_penjualan.'/'.$totaluang.'/'.$idretur))->with('error', ' Quantities not Enough.');
              }
          }
      }

      // calculate total pembelian-------------------------------------------------------------
      $total_price = $request->items_qty_view * $request->items_sub_total_view;
      // calculate total pembelian-------------------------------------------------------------

      // validasi quantities-------------------------------------------------------------------
      if($total_price == 0){
        if($request->validasi==0){
          return redirect(url('selling/selling_home/tig/add/'.$totaluang.'/'.$idretur.'/'.$request->iddetailretur))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
        } else {
          return redirect(url('selling/selling_home/tig/addnext/'.$msid.'/'.$totaluang.'/'.$idretur))->with('error', ' Grand Total = 0, Please check Sub Price and Quantities.');
        }
      }
      // validasi quantities-------------------------------------------------------------------

      // validasi saat unpaid dan paid---------------------------------------------------------
      if($request->validasi==0){
        //buat id
        $selling = manage_selling::orderBy('id', 'DESC')->first();
        if($selling){
          $lastnumber = substr($selling->penjualan_id, 1, 9);
          $idnumber = $lastnumber + 1;

          $sellingid = "S".sprintf("%09d", $idnumber);
        } else {
          $sellingid = "S000000001";
        }

        //buat id
        // save to ms_penjualan------------------------------------------------------------------
        $data = new manage_selling;
        $data->penjualan_id                     = $sellingid;
        // $data->tanggal                          = $request->buy_date;
        $data->created_date                     = date('Y-m-d H:i:s');
        $data->last_modify_date                 = date('Y-m-d H:i:s');
        $data->modify_user_id                   = Auth::user()->karyawan_id;
        $data->returdetail_id                   = $request->iddetailretur;
        $data->status_transaksi                 = 0;
        $data->status                           = 'A';
        $data->save();
      }
      // save to ms_pembelian-------------------------------------------------------------------

      //buying id validasi----------------------------------------------------------------------
      if($request->validasi==0){
        $msid = $data->id;
      } else {
        $msid = $request->id_penjualan;
      }
      //buying id validasi----------------------------------------------------------------------

      // save to tr_detail_pembelian------------------------------------------------------------
      if($request->items_id=="addnew"){
        $datatemp = new manage_items_temp;
        $datatemp->nama = $request->new_item;
        $datatemp->qty = $request->items_qty_view;
        $datatemp->harga_jual = $request->items_sub_total_view;
        $datatemp->created_date = date('Y-m-d H:i:s');
        $datatemp->save();

        $datadetail = new manage_selling_history;
        $datadetail->detail_penjualan_id             	= $msid;
        $datadetail->barang_id                        = $datatemp->id;
        $datadetail->qty                              = $request->items_qty_view;
        $datadetail->type_sell                        = $type;
        $datadetail->sub_total_penjualan              = $request->items_sub_total_view;
        $datadetail->created_date                     = date('Y-m-d H:i:s');
        $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
        $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
        $datadetail->status                           = 'T';
        $datadetail->id_karyawan_kerja1         						= $request->emp1_id;
              $datadetail->id_karyawan_kerja2         						= $request->emp2_id;
              $datadetail->id_karyawan_kerja3         						= $request->emp3_id;
              $datadetail->id_karyawan_kerja4         						= $request->emp4_id;
              $datadetail->id_karyawan_kerja5         						= $request->emp5_id;
        $datadetail->save();

      } else {
        $datadetail = new manage_selling_history;
        $datadetail->detail_penjualan_id             	 = $msid;
        $datadetail->barang_id                        = $request->items_id;
        $datadetail->qty                              = $request->items_qty_view;
        $datadetail->type_sell                        = $type;
        $datadetail->sub_total_penjualan              = $request->items_sub_total_view;
        $datadetail->created_date                     = date('Y-m-d H:i:s');
        $datadetail->last_modify_date                 = date('Y-m-d H:i:s');
        $datadetail->modify_user_id                   = Auth::user()->karyawan_id;
        $datadetail->status                           = 'A';
        $datadetail->id_karyawan_kerja1         			= $request->emp1_id;
        $datadetail->id_karyawan_kerja2         			= $request->emp2_id;
        $datadetail->id_karyawan_kerja3         			= $request->emp3_id;
        $datadetail->id_karyawan_kerja4         			= $request->emp4_id;
        $datadetail->id_karyawan_kerja5         			= $request->emp5_id;
        $datadetail->save();
      }
      // save to tr_detail_pembelian------------------------------------------------------------

      $totaluang = $request->totaluang;

      return redirect(url('selling/selling_home/tig/addnext/'.$msid.'/'.$totaluang.'/'.$idretur))->with('status', ' Created new buying has been success.');
    }
    // ---------------------------------------------------------------------------------

    // manage selling checkout function-------------------------------------------------
    public function tigindex_selling_checkout($id,$totaluang,$idretur)
    {
      $selling = manage_selling::find($id);

      //menghitung total------------------------------------------------------------------------
      $total = 0;
      $details = manage_selling_history::where('detail_penjualan_id', $id)->get();
      foreach ($details as $detail) {
        $total += $detail->sub_total_penjualan * $detail->qty;
      }
      //menghitung total------------------------------------------------------------------------

      return view('selling/selling/tig/sellingcheckout')->with('total', $total)
                                            ->with('selling', $selling)
                                            ->with('idretur', $idretur)
																						->with('totaluang', $totaluang);
    }
    // ---------------------------------------------------------------------------------

    // manage selling do checkout function----------------------------------------------
    public function tigindex_selling_docheckout(Request $request)
    {
      $idretur = $request->idretur;
      $selling = manage_selling::find($request->sellingid);
      $totaluang = $request->totaluang;
      $totalpricereal = 0;

      //menghitung total------------------------------------------------------------------------
      $total_price = 0;
      $details = manage_selling_history::where('detail_penjualan_id', $request->sellingid)->get();
      foreach ($details as $detail) {
        $total_price += $detail->sub_total_penjualan * $detail->qty;
        // // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
        if($detail->status!="T"){
          if($detail->type_sell==2){
            $item = manage_items::find($detail->barang_id);
            $item->stock = $item->stock - $detail->qty;
            $item->is_available = 1;
            $item->last_modify_date = date('Y-m-d H:i:s');
            $item->modify_user_id = Auth::user()->karyawan_id;
            $item->status = 'A';
            $item->save();
            $penjualan_detail = manage_selling_history::find($detail->id);
            if($penjualan_detail->sub_total_penjualan>$item->harga_jual){
              $penjualan_detail->type_report = 1;
            } else {
              $penjualan_detail->type_report = 0;
            }
            $penjualan_detail->save();
          } else {
            $item = manage_service::find($detail->barang_id);
            $item->qty = $item->qty - $detail->qty;
            $item->last_modify_date = date('Y-m-d H:i:s');
            $item->modify_user_id = Auth::user()->karyawan_id;
            $item->status = 'A';
            $item->save();
            $penjualan_detail = manage_selling_history::find($detail->id);
            if($penjualan_detail->sub_total_penjualan>$item->price){
              $penjualan_detail->type_report = 1;
            } else {
              $penjualan_detail->type_report = 0;
            }
            $penjualan_detail->save();
          }
        }
        // // // update ke ms_barang field is_availble menjadi 1 dan update stock dengan harga-----------
      }
      //menghitung total------------------------------------------------------------------------
      $totalpricereal = $total_price - $totaluang;
      if($totalpricereal<0){
        $totalpricereal = 0;
      }

      // set format id hutang------------------------------------------------------------------
      $for_date_cred      = date("dmy",strtotime($request->buy_date));
      $for_sell_id         = substr($selling->penjualan_id,0,10);

      $format_cred_id = $for_sell_id .'-'. $for_date_cred;
      // set format id hutang------------------------------------------------------------------

      // // validasi saat unpaid dan paid---------------------------------------------------------
      if($request->status_payment != '1'){
          if($request->sell_dp > $total_price){
              return redirect(url('selling/selling_home/tig/checkout'))->with('error', ' Down payment must less than Grand Total');
          }

          if($request->sell_dp == $total_price){
              return redirect(url('selling/selling_home/tig/checkout'))->with('error', ' Down payment must less than Grand Total');
          }
      }
      // validasi saat unpaid dan paid---------------------------------------------------------

      // Edit to ms_pembelian------------------------------------------------------------------
        $data = manage_selling::find($request->sellingid);
        $data->no_nota                          = $request->no_nota;
        $data->customer_id                      = $request->cust_id;
        $data->status_bayar_penjualan           = $request->status_payment;
        $data->tanggal                     			= date('Y-m-d H:i:s');
        $data->status_transaksi                 = 0;
        if($request->status_payment != '0'){
            $data->penjualan_bayar                  = $totalpricereal;
            $data->tanggal_jatuh_tempo_penjualan    = date('Y-m-d H:i:s');
            $data->penjualan_dp                     = 0;
        }else{
            $data->penjualan_bayar                  = $request->sell_dp;
            $data->tanggal_jatuh_tempo_penjualan    = $request->sell_due_date;
            $data->penjualan_dp                     = $request->sell_dp;
        }
        $data->penjualan_total                  = $totalpricereal;
        $data->save();
      // Edit to ms_pembelian-------------------------------------------------------------------

      //cek manage isinya//
      $creddd = manage_credit::orderBy('id', 'DESC')->first();
      if($creddd){
        $lastnumber = substr($creddd->piutang_id, 3, 7);
        $idnumber = $lastnumber + 1;

        $credddid = "CRE".sprintf("%07d", $idnumber);
      } else {
        $credddid = "CRE0000001";
      }
      //cek manage isinya//

      // // save ke lt_hutang-----------------------------------------------------------------------
      $data = new manage_credit;
      $data->piutang_id                        = $credddid;
      $data->penjualan_id                     = $request->sellingid;

      // jika pembelian langsung lunas
      if($request->status_payment > 0){
          $data->total                        = 0;
          $data->bayar                        = 0;
          $data->status_piutang                = 2;
      // jika pembelian tidak langsung lunas
      }else if($request->status_payment < 1){
          $data->total                        = $totalpricereal;
          $data->bayar                        = $request->sell_dp;
          $data->status_piutang                = 1;
      }

      $data->created_date                     = date('Y-m-d H:i:s');
      $data->last_modify_date                 = date('Y-m-d H:i:s');
      $data->modify_user_id                   = Auth::user()->karyawan_id;
      $data->status                           = 'A';
      $data->save();
      // save ke lt_hutang-----------------------------------------------------------------------

      // update total hutang ke tr_deposit_supplier ---------------------------------------------
      if($request->status_payment < 1){
          deposit_cust::where('customer_id',$request->cust_id)->update([
              'deposit' => ($totalpricereal - $request->sell_dp) + $request->cust_deposit,
              'last_modify_date' => date('Y-m-d H:i:s'),
              'modify_user_id' => Auth::user()->karyawan_id,
              'status' => 'A'
          ]);
      }
      // update total hutang ke tr_deposit_supplier ---------------------------------------------

      if($request->status_payment < 1){
        $credithistory = new manage_credit_history;
        $credithistory->piutang_id 								= $data->id;
        $credithistory->total_piutang	 						= $totalpricereal;
        $credithistory->total_pembayaran_piutang 	= $request->sell_dp;;
        $credithistory->created_date              = date('Y-m-d H:i:s');
        $credithistory->last_modify_date          = date('Y-m-d H:i:s');
        $credithistory->modify_user_id            = Auth::user()->karyawan_id;
        $credithistory->status                  	= 'A';
        $credithistory->save();
      }

      return redirect(url('sales/salreturn_home/addnext/'.$idretur));
    }
    // ---------------------------------------------------------------------------------
}
