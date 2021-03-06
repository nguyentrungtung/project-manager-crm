<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->client_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$wthout_all_goal = 0;
$direct_complete_achivement = 0;
$without_complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->client_model->get_progress($v_goal_track, true);

        if ($v_goal_track->goal_type_id == 11) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $direct_complete_achivement += $goal_achieve['achievement'];
        }
        if ($v_goal_track->goal_type_id == 10) {

            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->client_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $wthout_all_goal += $v_goal_track->achievement;
            $without_complete_achivement += $goal_achieve['achievement'];
        }
    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $where = array('date_added >=' => $date . " 00:00:00", 'date_added <=' => $date . " 23:59:59");
    $invoice_result[$date] = count($this->db->where($where)->get('tbl_client')->result());
}

$all_terget_achievement = $this->db->where(array('goal_type_id' => 11, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();
$without_terget_achievement = $this->db->where(array('goal_type_id' => 10, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();
if (!empty($all_terget_achievement)) {
    $all_terget_achievement = $all_terget_achievement;
} else {
    $all_terget_achievement = array();
}
if (!empty($without_terget_achievement)) {
    $without_terget_achievement = $without_terget_achievement;
} else {
    $without_terget_achievement = array();
}
$terget_achievement = array_merge($all_terget_achievement, $without_terget_achievement);
$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}

$curency = $this->client_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($all_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('without_converted') ?></small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead"><?= ($direct_complete_achivement) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>

            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table pv-lg">

                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= ($wthout_all_goal) ?></p>
                    <p class="m0">
                        <small><?= lang('converted_client') ?></small>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="m0 lead">

                        <?= $without_complete_achivement ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>

            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">

                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($invoice_result)) {
                        foreach ($invoice_result as $v_invoice_result) {
                            echo $v_invoice_result . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($invoice_result)) {
                                foreach ($invoice_result as $date => $v_invoice_result) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
                <?php
                $total_goal = $all_goal + $wthout_all_goal;
                $complete_achivement = $direct_complete_achivement + $without_complete_achivement;
                if ($total_goal <= $complete_achivement) {
                    $total_progress = 100;
                } else {
                    $progress = ($complete_achivement / $total_goal) * 100;
                    $total_progress = round($progress);
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-sm-12">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#client_list"
                                                                   data-toggle="tab"><?= lang('client_list') ?></a></li>
                <li class="<?= $active == 2 ? 'active' : '' ?>"><a href="#new_client"
                                                                   data-toggle="tab"><?= lang('new_client') ?></a></li>
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/client/import"><?= lang('import') . ' ' . lang('client') ?></a>
                </li>
            </ul>
            <div class="tab-content bg-white">
                <!-- Stock Category List tab Starts -->
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="client_list" style="position: relative;">
                    <div class="box">
                        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                            <thead>
                            <tr>

                                <th><?= lang('name') ?> </th>
                                <th><?= lang('contacts') ?></th>
                                <th class="hidden-sm"><?= lang('primary_contact') ?></th>
                                <th><?= lang('website') ?> </th>
                                <th><?= lang('email') ?> </th>
                                <th><?= lang('type') ?> </th>
                                <th class=""><?= lang('action') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($all_client_info)) {
                                foreach ($all_client_info as $client_details) {
                                    ?>
                                    <tr>

                                        <td>
                                            <a href="<?= base_url() ?>admin/client/client_details/<?= $client_details->client_id ?>"
                                               class="text-info">
                                                <?= $client_details->name ?></a></td>
                                        <td><span class="label label-success" data-toggle="tooltip"
                                                  data-palcement="top"
                                                  title="<?= lang('contacts') ?>"><?= $this->client_model->count_rows('tbl_account_details', array('company' => $client_details->client_id)) ?></span>
                                        </td>
                                        <td class="hidden-sm"><?php
                                            if ($client_details->primary_contact != 0) {
                                                $contacts = $client_details->primary_contact;
                                            } else {
                                                $contacts = NULL;
                                            }
                                            $primary_contact = $this->client_model->check_by(array('account_details_id' => $contacts), 'tbl_account_details');
                                            if ($primary_contact) {
                                                echo $primary_contact->fullname;
                                            }
                                            ?></td>
                                        <td><a href="<?= $client_details->website ?>" class="text-info"
                                               target="_blank">
                                                <?= $client_details->website ?></a>
                                        </td>
                                        <td><?= $client_details->email ?></td>
                                        <td><?php
                                            if ($client_details->client_status == 1) {
                                                $status = lang('person');
                                            } else {
                                                $status = lang('company');;
                                            }
                                            ?></td>
                                        <td>
                                            <?php echo btn_edit('admin/client/manage_client/' . $client_details->client_id) ?>
                                            <?php echo btn_delete('admin/client/delete_client/' . $client_details->client_id) ?>
                                            <?php echo btn_view('admin/client/client_details/' . $client_details->client_id) ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7">
                                        <?= lang('no_data') ?>
                                    </td>
                                </tr>
                            <?php }
                            ?>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane <?= $active == 2 ? 'active' : '' ?>" id="new_client" style="position: relative;">
                    <form role="form" enctype="multipart/form-data" id="form"
                          action="<?php echo base_url(); ?>admin/client/save_client/<?php
                          if (!empty($client_info)) {
                              echo $client_info->client_id;
                          }
                          ?>" method="post" class="form-horizontal  ">
                        <div class="panel-body">
                            <div class="row bg-box">

                                <div class="col-md-6 col-sm-12 col-xs-12 ">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6"><?= lang('select_type') ?></label>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="client_status" id="client_stusus">
                                                <option value="1" <?php
                                                if (!empty($client_info) && $client_info->client_status == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>Person
                                                </option>
                                                <option value="2" <?php
                                                if (!empty($client_info) && $client_info->client_status == 2) {
                                                    echo 'selected';
                                                }
                                                ?>>Company
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <label class="control-label col-sm-3"></label

                            <div class="col-sm-6">
                                <!--- /************ Person Start ***************/ --->
                                <div class="person" style="<?php
                                if (!empty($person)) {
                                    echo 'display:block';
                                } else {
                                    echo 'display:none';
                                }
                                ?>">
                                    <div class="nav-tabs-custom">
                                        <!-- Tabs within a box -->
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#general"
                                                                  data-toggle="tab"><?= lang('general') ?></a></li>
                                            <li><a href="#contact" data-toggle="tab"><?= lang('client_contact') ?></a>
                                            </li>
                                            <li><a href="#web" data-toggle="tab"><?= lang('web') ?></a></li>
                                            <li><a href="#hosting" data-toggle="tab"><?= lang('hosting') ?></a></li>
                                        </ul>
                                        <div class="tab-content bg-white">
                                            <!-- ************** general *************-->
                                            <div class="chart tab-pane active" id="general">
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('full_name') ?><span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" required=""
                                                               value="<?php
                                                               if (!empty($client_info->name)) {
                                                                   echo $client_info->name;
                                                               }
                                                               ?>" name="name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('email') ?><span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="email" class="form-control person" required=""
                                                               value="<?php
                                                               if (!empty($client_info->email)) {
                                                                   echo $client_info->email;
                                                               }
                                                               ?>" name="email">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="col-sm-3 control-label"><?= lang('language') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="language" class="form-control person select_box"
                                                                style="width: 100%">
                                                            <?php foreach ($languages as $lang) : ?>
                                                                <option
                                                                    value="<?= $lang->name ?>"<?php
                                                                if (!empty($client_info->language) && $client_info->language == $lang->name) {
                                                                    echo 'selected';
                                                                } elseif (empty($client_info->language) && $this->config->item('language') == $lang->name) {
                                                                    echo 'selected';
                                                                } ?>
                                                                ><?= ucfirst($lang->name) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('currency') ?></label>
                                                    <div class="col-lg-5">
                                                        <select name="currency" class="form-control person select_box"
                                                                style="width: 100%">

                                                            <?php if (!empty($currencies)): foreach ($currencies as $currency): ?>
                                                                <option
                                                                    value="<?= $currency->code ?>"
                                                                    <?php
                                                                    if (!empty($client_info->currency) && $client_info->currency == $currency->code) {
                                                                        echo 'selected';
                                                                    } elseif (empty($client_info->currency) && $this->config->item('default_currency') == $currency->code) {
                                                                        echo 'selected';
                                                                    } ?>
                                                                ><?= $currency->name ?></option>
                                                                <?php
                                                            endforeach;
                                                            endif;
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('short_note') ?></label>
                                                    <div class="col-lg-5">
                                            <textarea class="form-control person" name="short_note"><?php
                                                if (!empty($client_info->short_note)) {
                                                    echo $client_info->short_note;
                                                }
                                                ?></textarea>
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('profile_photo') ?></label>
                                                    <div class="col-lg-7">
                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <div class="fileinput-new thumbnail" style="width: 210px;">
                                                                <?php if (!empty($client_info->profile_photo)) : ?>
                                                                    <img
                                                                        src="<?php echo base_url() . $client_info->profile_photo; ?>">
                                                                <?php else: ?>
                                                                    <img src="http://placehold.it/350x260"
                                                                         alt="Please Connect Your Internet">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="fileinput-preview fileinput-exists thumbnail"
                                                                 style="width: 210px;"></div>
                                                            <div>
                                                    <span class="btn btn-default btn-file">
                                                        <span class="fileinput-new">
                                                            <input class="person" type="file" name="profile_photo"
                                                                   data-buttonText="<?= lang('choose_file') ?>"
                                                                   id="myImg">
                                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                                        </span>
                                                        <a href="#" class="btn btn-default fileinput-exists"
                                                           data-dismiss="fileinput"><?= lang('remove') ?></a>

                                                            </div>

                                                            <div id="valid_msg" style="color: #e11221"></div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- ************** general *************-->

                                            <!-- ************** Contact *************-->
                                            <div class="chart tab-pane" id="contact">
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('phone') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person"
                                                               value="<?php
                                                               if (!empty($client_info->phone)) {
                                                                   echo $client_info->phone;
                                                               }
                                                               ?>" name="phone">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('mobile') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person"
                                                               value="<?php
                                                               if (!empty($client_info->mobile)) {
                                                                   echo $client_info->mobile;
                                                               }
                                                               ?>" name="mobile">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('fax') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->fax)) {
                                                            echo $client_info->fax;
                                                        }
                                                        ?>" name="fax">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('city') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->city)) {
                                                            echo $client_info->city;
                                                        }
                                                        ?>" name="city">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('country') ?></label>
                                                    <div class="col-lg-5">
                                                        <select name="country" class="form-control person select_box"
                                                                style="width: 100%">
                                                            <optgroup label="Default Country">
                                                                <option
                                                                    value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                                                            </optgroup>
                                                            <optgroup label="<?= lang('other_countries') ?>">
                                                                <?php if (!empty($countries)): foreach ($countries as $country): ?>
                                                                    <option
                                                                        value="<?= $country->value ?>" <?= (!empty($client_info->country) && $client_info->country == $country->value ? 'selected' : NULL) ?>><?= $country->value ?>
                                                                    </option>
                                                                    <?php
                                                                endforeach;
                                                                endif;
                                                                ?>
                                                            </optgroup>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('zipcode') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->zipcode)) {
                                                            echo $client_info->zipcode;
                                                        }
                                                        ?>" name="zipcode">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('address') ?></label>
                                                    <div class="col-lg-5">
                                            <textarea class="form-control person" name="address"><?php
                                                if (!empty($client_info->address)) {
                                                    echo $client_info->address;
                                                }
                                                ?></textarea>
                                                    </div>
                                                </div>
                                            </div><!-- ************** Contact *************-->
                                            <!-- ************** Web *************-->
                                            <div class="chart tab-pane" id="web">
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('website') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->website)) {
                                                            echo $client_info->website;
                                                        }
                                                        ?>" name="website">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('skype_id') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->skype_id)) {
                                                            echo $client_info->skype_id;
                                                        }
                                                        ?>" name="skype_id">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('facebook_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->facebook)) {
                                                            echo $client_info->facebook;
                                                        }
                                                        ?>" name="facebook">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('twitter_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->twitter)) {
                                                            echo $client_info->twitter;
                                                        }
                                                        ?>" name="twitter">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('linkedin_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->linkedin)) {
                                                            echo $client_info->linkedin;
                                                        }
                                                        ?>" name="linkedin">
                                                    </div>
                                                </div>
                                            </div><!-- ************** Web *************-->
                                            <!-- ************** Hosting *************-->
                                            <div class="chart tab-pane" id="hosting">
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('hosting_company') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->hosting_company)) {
                                                            echo $client_info->hosting_company;
                                                        }
                                                        ?>" name="hosting_company">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('hostname') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->hostname)) {
                                                            echo $client_info->hostname;
                                                        }
                                                        ?>" name="hostname">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('username') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->username)) {
                                                            echo $client_info->username;
                                                        }
                                                        ?>" name="username">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('password') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="password" class="form-control person" value="<?php
                                                        if (!empty($client_info->password)) {
                                                            echo $client_info->password;
                                                        }
                                                        ?>" name="password">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('port') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control person" value="<?php
                                                        if (!empty($client_info->port)) {
                                                            echo $client_info->port;
                                                        }
                                                        ?>" name="port">
                                                    </div>
                                                </div>
                                            </div><!-- ************** Hosting *************-->
                                        </div>
                                    </div><!-- /.nav-tabs-custom -->

                                </div><!--- /************ Person End ***************/ --->

                                <!--- /************ Company Start ***************/ --->
                                <div class="company" style="<?php
                                if (!empty($company)) {
                                    echo 'display:block';
                                } else {
                                    echo 'display:none';
                                }
                                ?>">
                                    <div class="nav-tabs-custom">
                                        <!-- Tabs within a box -->
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#general_compnay" data-toggle="tab"><?= lang('general') ?></a>
                                            </li>
                                            <li><a href="#contact_compnay" data-toggle="tab"><?= lang('client_contact') ?></a></li>
                                            <li><a href="#web_compnay" data-toggle="tab"><?= lang('web') ?></a></li>
                                            <li><a href="#hosting_compnay" data-toggle="tab"><?= lang('hosting') ?></a></li>
                                        </ul>
                                        <div class="tab-content bg-white">
                                            <!-- ************** general *************-->
                                            <div class="chart tab-pane active" id="general_compnay">
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('company_name') ?>
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" required=""
                                                               value="<?php
                                                               if (!empty($client_info->name)) {
                                                                   echo $client_info->name;
                                                               }
                                                               ?>" name="name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('company_email') ?>
                                                        <span
                                                            class="text-danger"> *</span></label>
                                                    <div class="col-lg-5">
                                                        <input type="email" class="form-control company" required=""
                                                               value="<?php
                                                               if (!empty($client_info->email)) {
                                                                   echo $client_info->email;
                                                               }
                                                               ?>" name="email">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_vat') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->vat)) {
                                                            echo $client_info->vat;
                                                        }
                                                        ?>" name="vat">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-sm-3 control-label"><?= lang('language') ?></label>
                                                    <div class="col-sm-5">
                                                        <select name="language" class="form-control company select_box"
                                                                style="width: 100%">
                                                            <?php foreach ($languages as $lang) : ?>
                                                                <option
                                                                    value="<?= $lang->name ?>"<?php
                                                                if (!empty($client_info->language) && $client_info->language == $lang->name) {
                                                                    echo 'selected';
                                                                } elseif (empty($client_info->language) && $this->config->item('language') == $lang->name) {
                                                                    echo 'selected';
                                                                } ?>
                                                                ><?= ucfirst($lang->name) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('currency') ?></label>
                                                    <div class="col-lg-5">
                                                        <select name="currency" class="form-control company select_box"
                                                                style="width: 100%">

                                                            <?php if (!empty($currencies)): foreach ($currencies as $currency): ?>
                                                                <option
                                                                    value="<?= $currency->code ?>"
                                                                    <?php
                                                                    if (!empty($client_info->currency) && $client_info->currency == $currency->code) {
                                                                        echo 'selected';
                                                                    } elseif (empty($client_info->currency) && $this->config->item('default_currency') == $currency->code) {
                                                                        echo 'selected';
                                                                    } ?>
                                                                ><?= $currency->name ?></option>
                                                                <?php
                                                            endforeach;
                                                            endif;
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('short_note') ?></label>
                                                    <div class="col-lg-5">
                                            <textarea class="form-control company" name="short_note"><?php
                                                if (!empty($client_info->short_note)) {
                                                    echo $client_info->short_note;
                                                }
                                                ?></textarea>
                                                    </div>
                                                </div>
                                            </div><!-- ************** general *************-->

                                            <!-- ************** Contact *************-->
                                            <div class="chart tab-pane" id="contact_compnay">
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_phone') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->phone)) {
                                                            echo $client_info->phone;
                                                        }
                                                        ?>" name="phone">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('company_mobile') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company"
                                                               value="<?php
                                                               if (!empty($client_info->mobile)) {
                                                                   echo $client_info->mobile;
                                                               }
                                                               ?>" name="mobile">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_fax') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->fax)) {
                                                            echo $client_info->fax;
                                                        }
                                                        ?>" name="fax">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_city') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->city)) {
                                                            echo $client_info->city;
                                                        }
                                                        ?>" name="city">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_country') ?></label>
                                                    <div class="col-lg-5">
                                                        <select name="country" class="form-control company select_box"
                                                                style="width: 100%">
                                                            <optgroup label="Default Country">
                                                                <option
                                                                    value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                                                            </optgroup>
                                                            <optgroup label="<?= lang('other_countries') ?>">
                                                                <?php if (!empty($countries)): foreach ($countries as $country): ?>
                                                                    <option
                                                                        value="<?= $country->value ?>" <?= (!empty($client_info->country) && $client_info->country == $country->value ? 'selected' : NULL) ?>><?= $country->value ?>
                                                                    </option>
                                                                    <?php
                                                                endforeach;
                                                                endif;
                                                                ?>
                                                            </optgroup>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_address') ?></label>
                                                    <div class="col-lg-5">
                                            <textarea class="form-control company" name="address"><?php
                                                if (!empty($client_info->address)) {
                                                    echo $client_info->address;
                                                }
                                                ?></textarea>
                                                    </div>
                                                </div>
                                            </div><!-- ************** Contact *************-->
                                            <!-- ************** Web *************-->
                                            <div class="chart tab-pane" id="web_compnay">
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('company_domain') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->website)) {
                                                            echo $client_info->website;
                                                        }
                                                        ?>" name="website">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('skype_id') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->skype_id)) {
                                                            echo $client_info->skype_id;
                                                        }
                                                        ?>" name="skype_id">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('facebook_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->facebook)) {
                                                            echo $client_info->facebook;
                                                        }
                                                        ?>" name="facebook">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('twitter_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->twitter)) {
                                                            echo $client_info->twitter;
                                                        }
                                                        ?>" name="twitter">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('linkedin_profile_link') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->linkedin)) {
                                                            echo $client_info->linkedin;
                                                        }
                                                        ?>" name="linkedin">
                                                    </div>
                                                </div>
                                            </div><!-- ************** Web *************-->
                                            <!-- ************** Hosting *************-->
                                            <div class="chart tab-pane" id="hosting_compnay">
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('hosting_company') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->hosting_company)) {
                                                            echo $client_info->hosting_company;
                                                        }
                                                        ?>" name="hosting_company">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('hostname') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->hostname)) {
                                                            echo $client_info->hostname;
                                                        }
                                                        ?>" name="hostname">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('username') ?> </label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->username)) {
                                                            echo $client_info->username;
                                                        }
                                                        ?>" name="username">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                        class="col-lg-3 control-label"><?= lang('password') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="password" class="form-control company" value="<?php
                                                        if (!empty($client_info->password)) {
                                                            echo $client_info->password;
                                                        }
                                                        ?>" name="password">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-lg-3 control-label"><?= lang('port') ?></label>
                                                    <div class="col-lg-5">
                                                        <input type="text" class="form-control company" value="<?php
                                                        if (!empty($client_info->port)) {
                                                            echo $client_info->port;
                                                        }
                                                        ?>" name="port">
                                                    </div>
                                                </div>
                                            </div><!-- ************** Hosting *************-->
                                        </div>
                                    </div><!-- /.nav-tabs-custom -->
                                </div><!--- /************ Company End ***************/ --->

                                <div class="form-group mt">
                                    <label class="col-lg-3"></label>
                                    <div class="col-lg-5">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

