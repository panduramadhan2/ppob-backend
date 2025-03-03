<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductPrepaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DigiflazController extends Controller
{
    protected $header = null;
    protected $url = null;
    protected $user = null;
    protected $key = null;
    protected $model = null;
    public function __construct()
    {
        $this->header = array(
            'Content-Type:application/json'
        );

        $this->url = env('DIGIFLAZ_URL');
        $this->user = env('DIGIFLAZ_USER');
        $this->key = env('DIGIFLAZ_KEY');

        $this->model = new ProductPrepaid();
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
}
