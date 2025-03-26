<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductPasca;
use App\Models\ProductPrepaid;
use App\Models\TransactionModel;
use App\Traits\CodeGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class DigiflazController extends Controller
{
    use CodeGenerate;
    protected $header = null;
    protected $url = null;
    protected $user = null;
    protected $key = null;
    protected $model = null;
    protected $model_pasca = null;
    protected $model_transaction = null;
    public function __construct()
    {
        $this->header = array(
            'Content-Type:application/json'
        );

        $this->url = env('DIGIFLAZ_URL');
        $this->user = env('DIGIFLAZ_USER');
        $this->key = env('DIGIFLAZ_MODE') == 'development' ? env('DIGIFLAZ_DEV_KEY') : env('DIGIFLAZ_PROD_KEY');
        // $this->key = env('DIGIFLAZ_PROD_KEY');

        $this->model = new ProductPrepaid();
        $this->model_pasca = new ProductPasca();
        $this->model_transaction = new TransactionModel();
    }
    public function get_product_prepaid()
    {
        $response = Http::withHeaders($this->header)->post($this->url . '/price-list', [

            "cmd" => "prepaid",
            // "cmd" => "pasca",
            "username" => $this->user,
            "sign" => md5($this->user . $this->key . "pricelist")

        ]);

        // return response()->json($response);
        $data = json_decode($response->getBody(), true);
        $this->model->insert_data($data['data']);
        // return response()->json($data['data']);
    }

    public function get_product_pasca()
    {
        $response = Http::withHeaders($this->header)->post($this->url . '/price-list', [
            "cmd" => "pasca",
            // "cmd" => "pasca",
            "username" => $this->user,
            "sign" => md5($this->user . $this->key . "pricelist")
        ]);

        // return response()->json($response);
        $data = json_decode($response->getBody(), true);
        // return response()->json($data['data']);
        $this->model_pasca->insert_data($data['data']);
    }

    public function digiflazTopup(Request $request)
    {
        $ref_id = $this->getCode();
        $product = ProductPrepaid::findProductBySKU($request->sku)->first();
        $sign = md5($this->user . $this->key . $ref_id);

        // Debugging logs
        // Log::info("Signature: $sign");
        // Log::info("User: " . $this->user);
        // Log::info("Key: " . $this->key);
        // Log::info("Ref ID: " . $ref_id);

        $response = Http::withHeaders($this->header)->post($this->url . '/transaction', [
            "username" => $this->user,
            "buyer_sku_code" => $request->sku,
            "customer_no" => $request->customer_no,
            "ref_id" => $ref_id,
            "sign" => $sign
        ]);

        $data = json_decode($response->getBody(), true);
        $this->model_transaction->insert_transaction_data($data['data'], 'Prepaid', $product->product_provider);

        // Return API response
        // return response()->json($data['data']);
        return response()->json($data['data']);
    }
    // public function digiflazTopup(Request $request)
    // {
    //     $ref_id = $this->getCode();
    //     $product = ProductPrepaid::findProductBySKU($request->sku)->first();
    //     $sign = md5($this->user . $this->key . $ref_id);

    //     $response = Http::withHeaders($this->header)->post($this->url . '/transaction', [
    //         "username" => $this->user,
    //         "buyer_sku_code" => $request->sku,
    //         "customer_no" => $request->customer_no,
    //         "ref_id" => $ref_id,
    //         "sign" => $sign
    //     ]);

    //     $data = json_decode($response->getBody(), true);

    //     // Pastikan data['data'] ada sebelum menyimpannya
    //     if (isset($data['data'])) {
    //         $this->model_transaction->insert_transaction_data($data['data'], 'Prepaid', $product->product_provider);
    //     } else {
    //         Log::error("Gagal menyimpan transaksi: " . json_encode($data));
    //     }

    //     return response()->json($data['data']);
    // }
}
