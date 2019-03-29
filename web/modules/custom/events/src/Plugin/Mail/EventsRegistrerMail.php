<?php
/**
 * @file
 * Contains \Drupal\my_module\Plugin\Mail\MyModuleMail.
 */
namespace Drupal\events\Plugin\Mail;

use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\Plugin\Mail\PhpMail;
use Drupal\Core\Render\Markup;
use Drupal\Core\Site\Settings;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Renderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Defines the plugin Mail.
 *
 * @Mail(
 *   id = "events_mail",
 *   label = @Translation("My Events registrer confirmation HTML mailer"),
 *   description = @Translation("Sends confirmation email")
 * )
 */
class EventsRegistrerMail extends PHPMail implements MailInterface, ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Render\Renderer;
   */
  protected $renderer;

  /**
   * EventsRegistrerMail constructor.
   *
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The service renderer.
   */
  function __construct(Renderer $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('renderer')
    );
  }
