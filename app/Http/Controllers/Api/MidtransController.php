<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\CodeGenerate;

class MidtransController extends Controller
{
    use CodeGenerate;
    /**
     * Display a listing of the resource.
     */
    protected $model = null;

    public function __construct()
    {
        $this->model = new PaymentModel();
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $username = env('MIDTRANS_SERVER_KEY');
        $midtrans_auth = $username . ':';
        $kode = $this->getCode();
        $type = "";
        $bank = $request->bank;
        if ($bank == 'bca' || $bank == 'bni' || $bank == 'bri' || $bank == 'cimb') {
            $type = 'bank_transfer';
        }
        // else {
        //     return response()->json(['error' => 'Bank tidak didukung'], 400);
        // }

        // $header = array(
        //     'Accept: application/json',
        //     'Content-Type:application/json',
        //     'Authorization: Basic ' . base64_encode($midtrans_auth)
        // );
        $header = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($midtrans_auth)
        ];

        $payment_type = $type;
        $transaction = array(
            'order_id' => $kode,
            'gross_amount' => (int) $request->amount
        );


        $bank_transfer = array(
            'bank' => $bank
        );

        $transaction_data = array(
            'payment_type' => $payment_type,
            'transaction_details' => $transaction,
            'bank_transfer' => $bank_transfer
        );


        $response = Http::withHeaders($header)->post('https://api.sandbox.midtrans.com/v2/charge', $transaction_data);
        $data = json_decode($response->getBody(), true);
        $this->model->insert_payment($data);
        return response()->json($data);
    }

    public function midtrans_hook(Request $request)
    {
        $result = file_get_contents('php://input');
        $data = json_decode($result, true);
        $this->model->update_payment($data);
        return response()->json([
            'message' => 'success',
        ]);
    }
}
