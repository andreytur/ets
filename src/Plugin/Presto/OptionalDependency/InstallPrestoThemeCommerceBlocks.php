<?php

namespace Drupal\presto\Plugin\Presto\OptionalDependency;

use Drupal\block\Entity\Block;
use Drupal\Core\Form\FormStateInterface;
use Drupal\presto\Mixins\DrupalConfigReaderTrait;

/**
 * Installs Presto Theme Commerce Blocks if possible.
 *
 * @PrestoOptionalDependency(
 *     id = "install_presto_theme_commerce_blocks",
 *     label = @Translation("Install Presto Theme Commerce Blocks"),
 *     weight = 1
 * )
 */
class InstallPrestoThemeCommerceBlocks extends AbstractOptionalDependency {

  use DrupalConfigReaderTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldInstall(array $installState) {
    $installTheme = $installState['optional_dependencies']['install_presto_theme']['presto_theme'];
    $installCommerce = !empty($installState['presto_ecommerce_enabled']);
    return $installTheme && $installCommerce;
  }

  /**
   * Get any required Batch API install operations for this dependency.
   *
   * @return array
   *   Batch operation definitions.
   */
  public function getInstallOperations() {
    return [
      [
        [static::class, 'createBlocks'],
        [],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(
    array $form,
    FormStateInterface $form_state
  ) {
    // No configuration required for this dependency.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(
    array &$form,
    FormStateInterface $form_state
  ) {
    // Nothing to do, we don't have a form.
  }

  /**
   * Create block.
   *
   * We do a manual entity create as for some reason it won't import via config.
   *
   * @param array $context
   *   Batch API context.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function createBlocks(array &$context) {
    $block = Block::create([
      'id' => 'presto_theme_views_block__presto_product_listing_listing_block',
      'status' => TRUE,
      'plugin' => 'views_block:presto_product_listing-listing_block',
      'weight' => 10,
      'theme' => 'presto_theme',
      'region' => 'content',
      'visibility' => [
        'request_path' => [
          'id' => 'request_path',
          'pages' => '/products',
          'negate' => FALSE,
          'context_mapping' => [],
        ],
      ],
    ]);
    $block->save();
  }

}
