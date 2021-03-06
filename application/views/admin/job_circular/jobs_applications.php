<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <h3 class="panel-title">
            <strong><?= lang('job_application_list') ?></strong>
        </h3>
    </div>
    <!-- Table -->
    <table class="table table-bordered table-hover" id="dataTables-example">
        <thead>
        <tr>
            <th><?= lang('job_title') ?></th>
            <th><?= lang('name') ?></th>
            <th><?= lang('email') ?></th>
            <th class="col-sm-1"><?= lang('mobile') ?></th>
            <th class="col-sm-1"><?= lang('apply_on') ?></th>
            <th class="col-sm-1"><?= lang('status') ?></th>
            <th class="col-sm-2"><?= lang('action') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($job_application_info)): foreach ($job_application_info as $v_job_application): ?>
            <tr>
                <td><?php echo $v_job_application->job_title; ?></td>
                <td><?php echo $v_job_application->name; ?></td>
                <td><?php echo $v_job_application->email; ?></td>
                <td><?php echo $v_job_application->mobile; ?></td>
                <td><?php echo strftime(config_item('date_format'), strtotime($v_job_application->apply_date)); ?></td>

                <td>
                    <?php
                    if ($v_job_application->application_status == 0) {
                        echo '<span class="label label-warning">' . lang('pending') . '</span>';
                    } elseif ($v_job_application->application_status == 1) {
                        echo '<span class="label label-success">' . lang('approved') . '</span>';
                    } else {
                        echo '<span class="label label-danger">' . lang('rejected') . '</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($v_job_application->application_status == 0) {
                        echo btn_approve('admin/job_circular/change_application_status/2/' . $v_job_application->job_appliactions_id);
                        ?>
                        <?php
                        echo btn_reject('admin/job_circular/change_application_status/1/' . $v_job_application->job_appliactions_id);
                    } elseif ($v_job_application->application_status == 1) {
                        echo btn_approve('admin/job_circular/change_application_status/2/' . $v_job_application->job_appliactions_id);
                    } else {
                        echo btn_reject('admin/job_circular/change_application_status/1/' . $v_job_application->job_appliactions_id);
                    }
                    ?>
                    <a href="<?= base_url() ?>admin/job_circular/jobs_application_details/<?= $v_job_application->job_appliactions_id ?>"
                       class="btn btn-info btn-xs" title="View" data-toggle="modal" data-target="#myModal"><span
                            class="fa fa-list-alt"></span></a>
                    <?php echo btn_delete('admin/job_circular/delete_jobs_application/' . $v_job_application->job_appliactions_id); ?>
                </td>
            </tr>
            <?php

        endforeach;
            ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
