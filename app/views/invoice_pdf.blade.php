@extends('layoutpdf')
@section('content')
<style>
    .spa{
        width: 100%;
    }
    .half{
        width: 50%;
    }
</style>
<div class="page-content">
    <div class="box box-info tbl-box">
        <table width="100%">
            <tbody>
                <tr align="left">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><h3>{{trans('payment.invoice_emission')}} {{ Config::get('app.website_title') }}</h3>
                    </td>
                </tr>
                <tr align="left">
                    <th>{{trans('payment.tax_date')}}</th>
                    <th>{{trans('payment.invoice_number')}}</th>
                    <th>{{trans('payment.description')}}</th>
                    <th>{{trans('payment.week_total')}}</th>
                    <th>{{trans('payment.week_end')}}</th>
                    <th>{{trans('payment.value_pay')}} {{ Config::get('app.generic_keywords.Provider') }}</th>
                    <th>{{trans('payment.receive_value')}} {{ Config::get('app.generic_keywords.Provider') }}</th>
                    <th>{{trans('payment.net_value')}}</th>
                </tr>
                <?php
                /* $settings = Settings::where('key', 'rider_fee')->first();
                  if (isset($settings->value)) {
                  $unit = $settings->value;
                  } else {
                  $unit = 1;
                  } */
                ?>
                <tr>
                    <td><?= $trips; ?></td>
                    <td><?php echo Config::get('app.website_title') . " " . Date('y-m') . $id; ?></td>
                    <td>{{trans('payment.transport_service')}}</td>
                    <td>
                        <?= Config::get('app.currency_symb') . sprintf2($total, 2); ?>
                    </td>
                    <td><?= $weekend; ?></td>
                    <td><?= $pay_to_provider; ?></td>
                    <td><?= $take_from_provider; ?></td>
                    <td>
                        <?= Config::get('app.currency_symb') . sprintf2(($total - $pay_to_provider + $take_from_provider), 2); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="8"><hr style="color: black;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{trans('payment.liquid')}}</td>
                    <td><?= Config::get('app.currency_symb') . sprintf2(($total - $pay_to_provider + $take_from_provider), 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@stop