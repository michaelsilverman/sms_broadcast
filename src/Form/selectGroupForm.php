<?php
/**
 * Created by PhpStorm.
 * User: michaelsilverman
 * Date: 3/1/17
 * Time: 12:20 PM
 */

namespace Drupal\sms_broadcast\Form;


 use Drupal\Core\Form\FormBase;
 use Drupal\Core\Form\FormStateInterface;
 use Drupal\Core\Ajax\AjaxResponse;
 use Drupal\srg_dataentry\Service\crudAppointments;

  class selectGroupForm extends FormBase {

      public function buildForm(array $form, FormStateInterface $form_state, $parameters = NULL) {
          $appt_times = array('' => '--Select--', '08:00' => '08:00', '08:15' => '08:15', '08:30' => '08:30', '08:45' => '08:45',
              '09:00' => '09:00', '09:15' => '09:15', '09:30' => '09:30', '09:45' => '09:45',
              '10:00' => '10:00', '10:15' => '10:15', '10:30' => '10:30', '10:45' => '10:45',
              '11:00' => '11:00', '11:15' => '11:15', '11:30' => '11:30', '11:45' => '11:45',
              '12:00' => '12:00', '12:15' => '12:15', '12:30' => '12:30', '12:45' => '12:45',
              '13:00' => '01:00', '13:15' => '01:15', '13:30' => '01:30', '13:45' => '01:45',
              '14:00' => '02:00', '14:15' => '02:15', '14:30' => '02:30', '14:45' => '02:45',
              '15:00' => '03:00', '15:15' => '03:15', '15:30' => '03:30', '15:45' => '03:45',
              '16:00' => '04:00', '16:15' => '04:15', '16:30' => '04:30', '16:45' => '04:45',
              '17:00' => '05:00', '17:15' => '05:15', '17:30' => '05:30', '17:45' => '05:45',
              '18:00' => '06:00', '18:15' => '06:15', '18:30' => '06:30', '18:45' => '06:45',
              '01:00' => 'x01:00', '01:15' => 'x01:15', '01:30' => 'x01:30', '01:45' => 'x01:45',
              '02:00' => 'x02:00', '02:15' => 'x02:15', '02:30' => 'x02:30', '02:45' => 'x02:45',
              '03:00' => 'x03:00', '03:15' => 'x03:15', '03:30' => 'x03:30', '03:45' => 'x03:45',
              '04:00' => 'x04:00', '04:15' => 'x04:15', '04:30' => 'x04:30', '04:45' => 'x04:45',
              '05:00' => 'x05:00', '05:15' => 'x05:15', '05:30' => 'x05:30', '05:45' => 'x05:45',
              '06:00' => 'x06:00', '06:15' => 'x06:15', '06:30' => 'x06:30', '06:45' => 'x06:45',

          );
          $rep_names = explode("\r\n",$this->config('srg_dataentry.settings')->get('rep_names'));
          $rep_array[''] = '--select--';
          foreach ($rep_names as $rep_name) {
              $rep_array[$rep_name] = $rep_name;
          }

          $outcomes = explode("\r\n",$this->config('srg_dataentry.settings')->get('outcomes'));
          //$rep_array = explode("\r\n",$rep_names);
          $outcome_array[''] = '--select--';
          foreach ($outcomes as $outcome) {
              $outcome_array[strtolower($outcome)] = $outcome;
          }

        $text_sent = \Drupal::state()->get('coyne_appt_call_date', '2017-01-01');
          $now = strtotime('now');
          $now_date = date('Y-m-d',$now);
        $form['header'] = [
            '#type' => 'markup',
            '#markup' => 'Appointment schedule for '.$parameters['date'],
        ];
          $form['appt_date'] = [
              '#type' => 'hidden',
              '#value' => $parameters['date'],
          ];

          $future_date = FALSE;


          if ($parameters['date'] <= $text_sent) {
              $header = array($this->t('Appt Time'), $this->t('Student Name'), $this->t('Phone'), $this->t('Rep'), $this->t('Appointment Type'), $this->t('Status'), $this->t('Outcome'));
              $future_date = TRUE;
          } else {
              $header = array($this->t('Appt Time'), $this->t('Student Name'), $this->t('Phone'), $this->t('Rep'), $this->t('Appt Type'));
          }
          $form['future_date'] = [
              '#type' => 'hidden',
              '#value' => $future_date,
          ];
          $name_field = $form_state->get('num_names');
          $form['#tree'] = TRUE;
          $form['appointments'] = [
              '#type' => 'table',
              '#header' => $header,
              '#prefix' => '<div id="appointments-wrapper">',
              '#suffix' => '</div>',
          ];

          if (empty($name_field)) {
              $value = 1;
              if ($parameters['appt_cnt'] > $value) {
                  $value = $parameters['appt_cnt'];
              }
              $name_field = $form_state->set('num_names', $value);
          }

          if ($form_state->get('num_names')>0) {
              $value = $form_state->get('num_names');
          }
          else {
              $value=1;
          }

          for ($i = 0; $i < $value; $i++) {
              $form['appointments'][$i]['time'] = array(
                  '#type' => 'select',
                  '#title' => $this->t('Appt Time'),
                  '#options' => $appt_times,
                  '#title' => $this->t('Time'),
                  '#title_display' => 'invisible',
     //             '#disabled' => $future_date,

              );

              $form['appointments'][$i]['student_name'] = array(
                  '#type' => 'textfield',
                  '#size' => '18',
                  '#title' => t('Student Name:'),
                  '#title_display' => 'invisible',
    //              '#disabled' => $future_date,
              );

              $form['appointments'][$i]['phone'] = array(
                  '#type' => 'tel',
                  '#size' => '10',
                  '#title' => t('Phone Number'),
                  '#suffix' => '<span id="phone-type'.$i.'"></span>',
                  '#title_display' => 'invisible',
              );

              $form['appointments'][$i]['rep'] = array(
                  '#type' => 'select',
                  '#title' => t('Rep'),
                  '#options' => $rep_array,
                  '#title_display' => 'invisible',
            //      '#disabled' => $future_date,
              );

              $form['appointments'][$i]['bb'] = array(
                  '#type' => 'radios',
                  //'#size' => '30',
                  '#title' => t('Appt Type'),
                  '#options' => array('first' => 'First', 'bb' => 'BB', 'follow' => 'Follow-up'),
                  '#title_display' => 'invisible',
           //       '#disabled' => $future_date,
              );
              if ($future_date == TRUE) {
           //       $form['appointments'][$i]['phone']['#attributes']['readonly'] = 'readonly';
           //       $form['appointments'][$i]['phone']['#attributes']['class'] = 'disabled';
                  $form['appointments'][$i]['status'] = array(
                      '#type' => 'textfield',
                      '#size' => '25',
                      '#title' => t('Status'),
                      '#title_display' => 'invisible',
           //           '#disabled' => TRUE,
                  );
                  $form['appointments'][$i]['outcome'] = array(
                      '#type' => 'select',
                      '#size' => '1',
                      '#title' => t('Outcome'),
                      '#options' => $outcome_array,
                      '#title_display' => 'invisible',
                  );
              }
          }
    //      if ($future_date == FALSE) {
              $form['actions'] = [
                  '#type' => 'actions',
              ];
              $form['appointments']['actions']['add_name'] = [
                  '#type' => 'submit',
                  '#limit_validation_errors' => array(),
                  '#value' => t('Add appointment'),
                  '#submit' => array('::addOne'),
                  '#ajax' => [
                      'callback' => '::addmoreCallback',
                      'wrapper' => 'appointments-wrapper',
                  ],
              ];
              //        if ($name_field > 1) {
              if ($value > 1)  {
                  $form['appointments']['actions']['remove_name'] = [
                      '#type' => 'submit',
                      '#limit_validation_errors' => array(),
                      '#value' => t('Remove previous'),
                      '#submit' => array('::removeCallback'),
                      '#ajax' => [
                          'callback' => '::addmoreCallback',
                          'wrapper' => 'appointments-wrapper',
                      ]
                  ];
              }
   //       }

          $form_state->setCached(FALSE);
          $form['actions']['submit'] = [
              '#type' => 'submit',
              '#value' => $this->t('Update Appointments'),
          ];
          if ($future_date == FALSE) {
              $form['actions']['submit']['#value'] = $this->t('Save Appointments');
              $form['actions']['reminders'] = [
                  '#type' => 'submit',
                  '#value' => $this->t('Send Reminders'),
                  '#submit' => array('::submitForm','::sendReminders'),
              ];

          //    $form['actions']['reminders']['#submit'][] = '::sendReminders';
          }
          $form['actions']['email'] = [
              '#type' => 'submit',
              '#value' => $this->t('Email Report'),
              '#submit' => array('::emailReport'),
          ];
          return $form;
      }

      public function getFormId() {
          return 'fapi_example_ajax_addmore';
      }

      public function addOne(array &$form, FormStateInterface $form_state) {
          $name_field = $form_state->get('num_names');
          $add_button = $name_field + 1;
          $form_state->set('num_names', $add_button);
          $form_state->setRebuild();
      }

      public function addmoreCallback(array &$form, FormStateInterface $form_state) {
          $name_field = $form_state->get('num_names');
          return $form['appointments'];
      }

      public function removeCallback(array &$form, FormStateInterface $form_state) {
          $name_field = $form_state->get('num_names');
          if ($name_field > 1) {
              $remove_button = $name_field - 1;
              $form_state->set('num_names', $remove_button);
          }
          $form_state->setRebuild();
      }

      public function sendReminders(array &$form, FormStateInterface $form_state)
      {
      //    $form['actions'][$action]['#submit'][] = 'mymodule_form_submit';
          $form_state->setRedirect('srg_dataentry.sendmessages', ['date' => $form_state->getValue('appt_date')]);
          return;
      }

      public function emailReport(array &$form, FormStateInterface $form_state) {
          $form_state->setRedirect('srg_dataentry.emailreport', ['date' => $form_state->getValue('appt_date')]);
          return;
      }

      public function initialAdd(array &$form, FormStateInterface $form_state) {
          $appointments = crudAppointments:: getAppointments($form_state->getValue('appt_date'));
      //    $appointments = unserialize($this::getAppointments($form_state->getValue('appt_date')));
          $name_field = $form_state->get('num_names');
          $add_button = $name_field;
          foreach ($appointments as $key => $appointment) {
              if ($key != 'actions') {
                  $form['appointment']['1']['time']['#value'] = $appointment['time'];
                  $form['appointment']['1']['student_name']['#default_value'] = $appointment['student_name'];
                  $form['appointment']['1']['phone']['#default_value'] = $appointment['phone'];
                  $add_button = $name_field + 1;
              }
          }

          $form_state->set('num_names', $add_button);
          $form_state->setRebuild();
      }
      public function populateForm(array &$form, FormStateInterface $form_state) {
          return $form['appointments'];
      }

      public function checkNbrCallback(array &$form, FormStateInterface $form_state) {
          // Instantiate an AjaxResponse Object to return.
          $trigger = $form_state->getTriggeringElement();  //['#array_parents']);
          $trigger_name = $trigger['#name'];
          $str=$trigger_name;
          foreach ($trigger as $key => $value) {
         //  $str .= ','.$key.':'.$value;
          }
          $index = substr($trigger['#id'], 13, 1);  // needs to improved
          $ajax_response = new AjaxResponse();
          $phone = $form_state->getValue(array('appointments',$index,'phone'));
  //        $number_info = TwilioCheckNumber::checkNumber($phone);
          $number_info['carrier_type'] = $str.$phone;
          // Add a command to execute on form, jQuery .html() replaces content between tags.
          // In this case, we replace the desription with wheter the username was found or not.
          //$ajax_response->addCommand(new HtmlCommand('#edit-student-name--description', 'dddd'));
          $target_name = str_replace('phone','type', $trigger_name); //#edit-appointments-'.$index.'-type';
       //   $ajax_response->addCommand(new InvokeCommand('name="'.$target_name.'"', 'val' , [$number_info['carrier_type']]));
     //     $form['appointments'][$index]['type']['default_value'] = $number_info['carrier_type'];
     //     $form['appointments'][$index]['type']['value'] = $number_info['carrier_type'];
     //     $ajax_response->addCommand(new HtmlCommand('#edit-appointments-2-type', 'fred'));
          //  $content = [ '#markup' => ' YES ', ];
          //  $ajax_response->addCommand(new HtmlCommand('#edit-phone', $content));
    //**      $ajax_response->addCommand(new HtmlCommand('#phone-type'.$index, $number_info['carrier_type']));

          // CssCommand did not work.
          //$ajax_response->addCommand(new CssCommand('#edit-user-name--description', array('color', $color)));

          // Add a command, InvokeCommand, which allows for custom jQuery commands.
          // In this case, we alter the color of the description.
          // $ajax_response->addCommand(new InvokeCommand('#edit-student-name--description', 'css', array('color', $color)));

          // Return the AjaxResponse Object.
          return $ajax_response;
      }
      public function validateForm(array &$form, FormStateInterface $form_state) {
          $future_date = $form_state->getValue('future_date');
          $appointments = $form_state->getValue('appointments');
          $index = 0;
          if (!$future_date) {
              foreach ($appointments as $key => $appointment) {
                  if ($key !== 'actions') {
                      if ($appointment['time'] == '') {
                          $form_state->setErrorByName('appointments][' . $index . '][time', ' Appt Time must be selected');
                      }
                      if ($appointment['rep'] == '') {
                          $form_state->setErrorByName('appointments][' . $index . '][rep', ' Rep must be selected');
                      }
                      if ($appointment['student_name'] == '') {
                          $form_state->setErrorByName('appointments][' . $index . '][student_name', ' Name cannot be empty');
                      }
                      if ($this::validatePhone($appointment['phone']) === 0) {
                          $form_state->setErrorByName('appointments][' . $index . '][phone', 'Not a valid phone number');
                      }
                      if ($appointment['bb'] == '') {
                          $form_state->setErrorByName('appointments][' . $index . '][bb', 'First or BB must be selected');
                      }
                  }
                  $index++;
              }
          }
      }
      public function submitForm(array &$form, FormStateInterface $form_state) {
          $appointment_date = $form_state->getValue('appt_date');
          $appointments = $form_state->getValue('appointments');
          unset($appointments['actions']);
   //       if ($form_state->getValue('future_date')) {
   //           $retcode = crudAppointments::updateOutcome($appointment_date, $appointments);
   //       } else {
              $retcode = crudAppointments::writeApptRec($appointment_date, $appointments);
   //       }

          $output = t('The appointments for @date have been saved', array('@date' => $appointment_date,));
          drupal_set_message($output);
      }

      public static function validatePhone($phone) {
          $status = 0;
          $result = preg_replace("/[^0-9]/", '', $phone);
          if (filter_var($result, FILTER_VALIDATE_INT) && (strlen($result) == 10)) {
              $status = 1;
          }
          return $status;
      }

  }

