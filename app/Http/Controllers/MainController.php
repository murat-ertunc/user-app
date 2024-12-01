<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Jobs\CustomerImportJob;
use Yajra\DataTables\DataTables;

class MainController extends Controller
{
    public function index() {
        return view('pages.index');
    }

    public function storeCustomer(Request $request) {
        $rules = [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:50',
        ];

        $reponseMesseages = [
            'name.required' => 'Müşteri adı alanı gereklidir',
            'name.string' => 'Müşteri adı alanı metin tipinde olmalıdır',
            'name.max' => 'Müşteri adı alanı en fazla 50 karakter olabilir',
            'email.required' => 'Müşteri e-posta alanı gereklidir',
            'email.email' => 'Müşteri e-posta alanı geçerli bir e-posta adresi olmalıdır',
            'email.max' => 'Müşteri e-posta alanı en fazla 50 karakter olabilir',
            'phone.required' => 'Müşteri telefon alanı gereklidir',
            'phone.string' => 'Müşteri telefon alanı metin tipinde olmalıdır',
            'phone.max' => 'Müşteri telefon alanı en fazla 20 karakter olabilir',
            'company.string' => 'Müşteri şirket alanı metin tipinde olmalıdır',
            'company.max' => 'Müşteri şirket alanı en fazla 50 karakter olabilir',
        ];

        if (blank($request->customerId)) {
            $rules['email'] .= '|unique:customers,email';
            $rules['phone'] .= '|unique:customers,phone';
            $reponseMesseages['email.unique'] = 'Bu e-posta adresi zaten kullanılmaktadır';
            $reponseMesseages['phone.unique'] = 'Bu telefon numarası zaten kullanılmaktadır';
        }

        $request->validate($rules, $reponseMesseages);

        if(blank($request->customerId)){
            Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company ?? null,
            ]);
        }else{
            Customer::where('id', $request->customerId)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company ?? null,
            ]);
        }

        return response()->json(['message' => 'Müşteri başarıyla oluşturuldu'], 201);
    }

    public function deleteCustomer($id) {
        Customer::findOrFail($id)->delete();

        return response()->json(['message' => 'Müşteri başarıyla silindi'], 200);
    }

    public function customers(Request $request) {
        $customers = Customer::orderBy('id', 'desc');

        return Datatables::of($customers)->addIndexColumn()->escapeColumns([])->make(true);
    }

    public function customer($id) {
        $customer = Customer::findOrFail($id);

        return response()->json($customer);
    }

    public function import(Request $request) {
        $request->validate([
            'jsonData' => 'required',
        ],[
            'jsonData.required' => 'Veri alanı gereklidir',
        ]);

        $importDatas = json_decode($request->jsonData);
        $importDatas = array_chunk($importDatas, 100);

        foreach ($importDatas as $importData) {
            dispatch(new CustomerImportJob($importData));
        }

        return response()->json(['message' => 'Aktarım işlemi başladı'], 200);
    }
}
