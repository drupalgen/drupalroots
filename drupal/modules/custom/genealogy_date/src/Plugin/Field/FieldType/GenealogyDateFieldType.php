<?php

namespace Drupal\genealogy_date\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'genealogy_date_field_type' field type.
 *
 * @FieldType(
 *   id = "genealogy_date_field_type",
 *   label = @Translation("Genealogy Date"),
 *   description = @Translation("Genealogy Date, in the format of (Bef|Abt|Aft) YYYY Mon DD"),
 *   default_widget = "genealogy_date_widget_type",
 *   default_formatter = "genealogy_date_formatter_type"
 * )
 */
class GenealogyDateFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 20,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    $properties['indexed_value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'binary' => $field_definition->getSetting('case_sensitive'),
        ],
        'indexed_value' => [
          'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'binary' => $field_definition->getSetting('case_sensitive'),
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    if ($max_length = $this->getSetting('max_length')) {
      $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
      $constraints[] = $constraint_manager->create('ComplexData', [
        'value' => [
          'Length' => [
            'max' => $max_length,
            'maxMessage' => t('%name: may not be longer than @max characters.', [
              '%name' => $this->getFieldDefinition()->getLabel(),
              '@max' => $max_length,
            ]),
          ],
        ],
      ]);
    }

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    $elements['max_length'] = [
      '#type' => 'number',
      '#title' => t('Maximum length'),
      '#default_value' => $this->getSetting('max_length'),
      '#required' => TRUE,
      '#description' => t('The maximum length of the field in characters.'),
      '#min' => 1,
      '#disabled' => $has_data,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function presave() {
    $entered_value = $this->get('value')->getValue();
    $calculated_value = NULL;
    if (strlen($entered_value) < 1) {
      $calculated_value = '0000-00-00';
    }
    else {
      $calculated_value = $this->calculateIndexedValue($entered_value);
    }

    // Sets the calculated value of the indexed_value component of the field.
    $this->set('indexed_value', $calculated_value);

  }

  /**
   * Calculates indexed_value.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   *
   * @return string
   *   The calculated indexed_value string.
   */
  private function calculateIndexedValue(string $entered_value) {
    // Convert $entered_value to upper case.
    $calculated_value = '0000-00-00';
    $entered_value = trim(strtoupper($entered_value));

    // Flag values.
    $before_flag = FALSE;
    $after_flag = FALSE;
    $about_flag = FALSE;

    $year_number = NULL;
    $month_number = NULL;
    $day_number = NULL;

    // Array conversion of months to numeric values.
    $months = [
      "JAN" => '01',
      "FEB" => '02',
      "MAR" => '03',
      "APR" => '04',
      "MAY" => '05',
      "JUN" => '06',
      "JUL" => '07',
      "AUG" => '08',
      "SEP" => '09',
      "OCT" => '10',
      "NOV" => '11',
      "DEC" => '12',
    ];

    // Get prefixed date value, if it exists.
    if (strpos($entered_value, 'BEF') !== FALSE) {
      $before_flag = TRUE;
      $entered_value = trim(str_replace('BEF', '', $entered_value));
    }
    elseif (strpos($entered_value, 'AFT') !== FALSE) {
      $after_flag = TRUE;
      $entered_value = trim(str_replace('AFT', '', $entered_value));
    }
    elseif (strpos($entered_value, 'ABT') !== FALSE) {
      $about_flag = TRUE;
      $entered_value = trim(str_replace('ABT', '', $entered_value));
    }

    // Get year.
    $year_value = substr($entered_value, 0, 4);
    if (!$year_value) {
      return $calculated_value;
    }
    else {
      $year_number = $year_value;
      $entered_value = trim(str_replace($year_value, '', $entered_value));
    }

    // Get month.
    $month_value = substr($entered_value, 0, 3);
    if ($month_value) {
      $month_number = $months[$month_value];
      if (strlen($month_number) < 1) {
        $month_number = '00';
      }
      $entered_value = trim(str_replace($month_value, '', $entered_value));
    }
    else {
      $month_number = '00';
    }

    // Get day.
    if (strlen($entered_value) > 0) {
      if (strlen($entered_value) == 1) {
        $day_number = '0' . $entered_value;
      }
      else {
        $day_number = $entered_value;
      }
    }
    else {
      $day_number = '00';
    }

    $date_string = $year_number . '-' . $month_number . '-' . $day_number;

    return $date_string;
  }

}
