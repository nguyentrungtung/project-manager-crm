<!DOCTYPE html>
<html>
<head>
    <title><?= lang('transfer_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        th {
            padding: 10px 0px 5px 5px;
            text-align: left;
            font-size: 13px;
            border: 1px solid black;
        }

        td {
            padding: 5px 0px 0px 5px;
            text-align: left;
            border: 1px solid black;
            font-size: 13px;
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
            </td>

            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>

<br/>
<div style="width: 100%;">
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr>
            <th style="width: 15%"><?= lang('date') ?></th>
            <th style="width: 15%"><?= lang('from_account') ?></th>
            <th style="width: 15%"><?= lang('to_account') ?></th>
            <th><?= lang('type') ?></th>
            <th><?= lang('notes') ?></th>
            <th><?= lang('amount') ?></th>
        </tr>
        <?php
        $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        $total_amount = 0;
        $all_transaction_info = $this->db->order_by('transfer_id', 'DESC')->get('tbl_transfer')->result();
        if (!empty($all_transaction_info)):
        foreach ($all_transaction_info as $v_transaction) :
            $from_account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->from_account_id), 'tbl_accounts');
            $to_account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->to_account_id), 'tbl_accounts');
            ?>

            <tr style="width: 100%;">
                <td><?= strftime(config_item('date_format'), strtotime($v_transaction->date)); ?></td>
                <td class="vertical-td"><?= $from_account_info->account_name ?></td>
                <td class="vertical-td"><?= $to_account_info->account_name ?></td>
                <td class="vertical-td"><?= lang($v_transaction->type) ?> </td>
                <td class="vertical-td"><?= $v_transaction->notes ?></td>
                <td><?= display_money($v_transaction->amount, $curency->symbol) ?></td>
            </tr>
            <?php
            $total_amount += $v_transaction->amount;
            ?>
        <?php endforeach; ?>
        <tr class="custom-color-with-td">
            <th style="text-align: right;" colspan="5"><strong><?= lang('total') ?>:</strong></th>
            <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
        <tr>
            <?php else: ?>
                <td colspan="5">
                    <strong>There is no Report to display</strong>
                </td>
            <?php endif; ?>
    </table>

</div>
</body>
</html>
