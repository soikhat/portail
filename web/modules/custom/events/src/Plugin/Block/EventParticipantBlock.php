<?php

namespace Drupal\events\Plugin\Block;

Use Drupal\Core\Block\Blockbase;
Use Drupal\Core\Cache\Cache;

/**
 *provides
 * @Block(
 *   id= "events_block",
 *   admin_label= @translation("Events_Participant")
 * )
 * @package Drupal\events\Plugin\Block
 *
 */
class EventParticipantBlock extends Blockbase{

//build the block
  public function build(){

    //check that we are on event page

    if('entity.event_entity.canonical'==  \Drupal::routeMatch()->getRouteName()){

      $currentEntity = \Drupal::routeMatch()->getParameter('event_entity')->id();

      if ($currentEntity) {

        $nombre_participant = \Drupal::database()
            ->select('participants_list', 'P')
            ->condition('eid', $currentEntity, '=')
            ->countQuery()
            ->execute()->fetchField();

        $participants_list = \Drupal::database()
            ->select('participants_list', 'P')
            ->condition('eid', $currentEntity, '=')
            ->fields('P', ['name'])
            ->execute();
        $list = [];
        foreach ($participants_list as $participant) {

            $list[] = $participant->name;

        }
        /*$list_participants = [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#title' => 'We registred ' . $nombre_participant . ' Participants',
            '#items' => $list,
            '#cache' => [
                'keys' => ['participant'],
                'max-age' => '10',
            ]

        ];*/
        if($nombre_participant > 6){

          return ['#markup'=>t('Registred Participants :').' '. $nombre_participant. '',
                '#cache' => [
                    'keys' => ['participant'],
                    'max-age' => '10',
                ]
              ];
        }
        else{
          return ['#markup'=>t('Dont forget to register. Places are limited !!!')];
        }
      }
    }

  }

  public function getCacheContexts() {
        //if you depends on \Drupal::routeMatch()
        //you must set context of this block with 'route' context tag.
        //Every new route this block will rebuild
      return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}
