<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentModel extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $fillable = ['transaction_date', 'transaction_time', 'transaction_id', 'order_id', 'merchant_id', 'gross_amount', 'payment_type', 'transaction_message', 'transaction_status', 'bank', 'va_number', 'user_id'];

    public function insert_payment($data)
    {

        self::create([
            'transaction_date' => Carbon::now(),
            'transaction_time' => $data['transaction_time'],
            'transaction_id' => $data['transaction_id'],
            'order_id' => $data['order_id'],
            'merchant_id' => $data['merchant_id'],
            'gross_amount' => $data['gross_amount'],
            'payment_type' => $data['payment_type'],
            'transaction_message' => $data['status_message'],
            'transaction_status' => $data['transaction_status'],
            'bank' => $data['va_numbers'][0]['bank'],
            'va_number' => $data['va_numbers'][0]['va_number'],
            'user_id' => Auth::user()->id
        ]);
    }

    public function update_payment($data)
    {
        $status = "";

        $data_payment = self::where('order_id', $data['order_id'])->first();
        $user = User::where('id', $data_payment->user_id)->first();
        if ($data['transaction_status'] == "settlement" || $data['transaction_status'] == "capture") {
            $status = "Sukses";
        } else {
            $status = $data['transaction_status'];
        }

        if ($data['transaction_status'] == "settlement" || $data['transaction_status'] == "capture") {
            $status = "Sukses";
            if ($user->roles_id == 2) {
                $last_saldo = DB::table('user_saldo')->where('user_id', $user->id)->first();

                if ($last_saldo) {

                    DB::table('user_saldo')->where('user_id', $user->id)->update([
                        'user_last_saldo' => $last_saldo->user_last_saldo + $data_payment->gross_amount
                    ]);
                } else {
                    DB::table('user_saldo')->insert([
                        'user_id' => $user->id,
                        'user_last_saldo' => $last_saldo + $data_payment->gross_amount
                    ]);
                }
            }
        }


        //update status payment
        self::where('order_id', $data['order_id'])->update([
            'transaction_status' => $status
        ]);
    }
}
