<?php

namespace Drupal\events\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;



class EventEntityInscription extends FormBase {

  public function getFormId(){

    return 'participation_form';

  }
  public function buildForm(array $form, FormStateInterface $form_state){

    $participant = \Drupal::currentUser()->getAccountName();


    //build our form
    $form['name']= [
            '#title'=> 'Nom',
            '#type'=>'textfield',
            '#lenght' => 60,
            '#value'=>$participant,
            '#attributes'=> ['readonly'=>'readonly'],
        ];

        $form ['mail'] = [
            '#title' => 'mail',
            '#type'=> 'email',
            '#required' => TRUE,
        ];
        $form ['mobile_phone'] = [
            '#title' => $this->t('mobile phone'),
            '#type'=> 'tel',
            '#required' => TRUE,

        ];

        $form['submit']=[
            '#type'=> 'submit',
            '#value' => 'participate to the event',
            '#attributes' => ["onclick" => "
              jQuery(this).attr('disabled', true);
              jQuery(this).parents('form').submit(); "]

        ];

        return $form;


  }
  public function validateForm(array &$form, FormStateInterface $form_state){
    $mobile = $form_state ->getValue('mobile_phone');
    $pattern = "#^[34][-. ]?[1-9]{2}([-. ]?[0-9]{2}){2}$#";
    if(preg_match($pattern, $mobile) == FALSE){

      $form_state->setErrorByName('mobile_phone', $this->t('please set a good phone number!'));

    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state){

    $name = $form_state ->getValue('name');
    $mail = $form_state ->getValue('mail');
    $mobile = $form_state ->getValue('mobile_phone');
    //Get the current entity
    $eid = \Drupal::routeMatch()->getParameter('event_entity')->id();
    $uid = \Drupal::currentUser()->id();

    $insert = \Drupal::Database()->insert('participants_list')
              ->fields([
                'uid'=>$uid,
                'eid'=>$eid,
                'name'=>$name,
                'mail'=> $mail,
                'mobile'=>$mobile,
              ])
              ->execute();

    \Drupal::messenger()
            ->addMessage(t('you are successfully registred. Thanks.'));

    //$form_state->setRedirect('entity.event_entity.canonical',['evenements' => $entity_id,'submitted'=>'1']);

    //Sending confirmation mail
    $sitename = \Drupal::config('system.site')->get('name');
    $langcode = \Drupal::languageManager()->getCurrentLanguage();
    $module = 'events';
    $key = 'events_registration';
    $to = $mail;
    $reply = NULL;
    $send = TRUE;

    $params['message'] = t('A new events has created and you can registrer now from our website @sitename', array('@sitename' => $sitename));
    $params['subject'] = t('Event!!!');
    //$params['options']['username'] = $account->getUsername();
    //$params['options']['title'] = t('Your wonderful title');
    //$params['options']['footer'] = t('Your wonderful footer');

    $mailManager = \Drupal::service('plugin.manager.mail');
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $reply, $send);
    //
    if ($result['result'] !== true) {
      drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
     }
     else {
       drupal_set_message(t('Your message has been sent.'));
     }

  }



}
