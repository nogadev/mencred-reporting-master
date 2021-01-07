<?php

use Illuminate\Database\Seeder;

class PaymentTerm extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array(
                'id'    =>  1,
                'name'  =>   'CONTADO'
            ),
            array(
                'id'	=>  2,
                'name' 	=>  'CUENTA CORRIENTE'
            )
        );

        DB::table('payment_terms')->insert($data);
    }
}
