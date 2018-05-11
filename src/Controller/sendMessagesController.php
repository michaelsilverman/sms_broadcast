<?php
/**
 * Created by PhpStorm.
 * User: michaelsilverman
 * Date: 1/24/17
 * Time: 3:37 PM
 */

namespace Drupal\sms_broadcast\Controller;

use Drupal\Core\Controller;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection;
//use Drupal\srg_dataentry\Service\crudAppointments;
use Drupal\srg_twilio\Services\Command;


class sendMessagesController extends ControllerBase implements ContainerInjectionInterface, ContainerAwareInterface {

    protected $twig ;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig ;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        // TODO: Implement setContainer() method.
    }

    public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container)

    {
        return new static(
            $container->get('twig')
        );
    }

    public function sendMessages($dist_listID=1, $messageID=2) {
        $client_name = 'Aluminum Company';
        $client_info = new Command($client_name);
        dpm($client_info->getToken(), 't');
        dpm($client_info->getSID(), 's');
        // sami http://aluminumcompany.com/sites/all/themes/aluminum_company/images/meet_jeff_monsein.jpg
        $message = ['text'=> 'Sami will be coming at 9:30 tomorrow',
            'image_url'=>'http://aluminumcompany.com/sites/all/themes/aluminum_company/images/meet_sami_hanna.jpg'];
        $distribution_list[] = ['number'=> '6308999711', 'name'=> 'Michael'];
        dpm($distribution_list, 'dist');
        dpm($message, 'message');
        foreach ($distribution_list as $distribution_item) {
            $client_info->messageSend($distribution_item['number'], $message['text'], $message['image_url']);
        }

        $markup = [
            '#markup' => 'Text send to: ',
        ];
        return $markup;

 /*       $today = date('Y-m-d',strtotime('now'));
        // set to date of text massages being sent
        \Drupal::state()->set('coyne_appt_call_date', $date);
        $appointments = crudAppointments:: getAppointments($date);
        unset($appointments['actions']);
        $time = strtotime($date);
        $newformat = date('l F jS',$time);
        foreach ($appointments as $key => $appointment) {
            if ($key !== 'actions') {
          //    $text = 'This is Coyne College reminding '.$appointment['student_name']. ' of their appointment '.$newformat.
          //        ' at '.$appointment['time'].'. Press Y to confirm or call 800-707-1922 to reschedule';
                $text = 'This is Coyne College reminding '.$appointment->student_name. ' of their appointment '.$newformat.
                    ' at '.$appointment->appt_time.'. Press C to confirm, X to cancel or R to reschedule.';
              $to = $appointment->phone;
                $from = $this->config('srg_dataentry.settings')->get('phone_number');
              $message = new SMSMessage();
              $carrier_type = $message->sendMessage('twilio', $from, $to, $text);
              $status = 'waiting response';
              if ($carrier_type == 'landline') {
                  $status = 'Landline cannot send text';
              }
              if ($carrier_type == 'Invalid phone number') {
                  $status = $carrier_type;
              }
              crudAppointments::updateStatus($date, $appointment->phone, $status);
            }
        }
        $twigFilePath = drupal_get_path('module', 'srg_dataentry') . '/templates/sendmessages.html.twig';
        $template = $this->twig->loadTemplate($twigFilePath);
 */
        $markup = [
         //   '#markup' => $template->render( ['appointments' => $appointments ]),
            '#markup' => 'sent',
         //   '#attached' => ['library' => ['client/index.custom']] ,
        ];
        return $markup;
    }
//?????????????????
    public function showStatus($date)
    {
        $appointments = crudAppointments::getStatus($date);
        $twigFilePath = drupal_get_path('module', 'srg_dataentry') . '/templates/showstatus.html.twig';
        $template = $this->twig->loadTemplate($twigFilePath);
        $markup = [
            '#markup' => $template->render( ['date'=>$date, 'appointments' => $appointments ]),
            //   '#attached' => ['library' => ['client/index.custom']] ,
        ];
        return $markup;
    }





}