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
      'max_length' => 120,
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

    $properties['beg_indexed_value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['end_indexed_value'] = DataDefinition::create('string')
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
        'beg_indexed_value' => [
          'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'binary' => $field_definition->getSetting('case_sensitive'),
        ],
        'end_indexed_value' => [
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
    $this->set('beg_indexed_value', $calculated_value[0]);
    $this->set('end_indexed_value', $calculated_value[1]);

  }

  /**
   * Calculates indexed_value.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   *
   * @return array
   *   The calculated indexed_values.
   */
  private function calculateIndexedValue(string $entered_value) {
    // Convert $entered_value to upper case.
    $calculated_value = array('0000-00-00', '9999-99-99');
    $entered_value = trim(strtoupper($entered_value));
    $full_date_regex = "/(\d{1,2}) ([a-zA-Z]{3}) (\d{4})/";
    $partial_date_regex = "/([a-zA-Z]{3}) (\d{4})/";
    $year_regex = "/(\d{4})/";

    // If date string starts with BET, FROM, AFT.
    $beg_string_value = trim(substr($entered_value, 0, 4));
    if (in_array($beg_string_value, array('BET', 'FROM', 'AFT'))) {
      // Date in the format of dd MMM yyyy.
      $matches = [];
      if (preg_match_all($full_date_regex, $entered_value, $matches)) {
        $date_string = $this->parseFullDate($entered_value, $matches);
        return array($date_string[0], $date_string[1]);
      }
      // Date in the format of MMM yyyy or yyyy.
      $matches = [];
      if (preg_match_all($partial_date_regex, $entered_value, $matches)) {
        $date_string = $this->parsePartialDate($entered_value, $matches);
        return array($date_string[0], $date_string[1]);
      }
    }
    // If date string starts with TO or BEF.
    $beg_string_value = trim(substr($entered_value, 0, 3));
    if (in_array($beg_string_value, array('TO', 'BEF'))) {
      // Date in the format of dd MMM yyyy.
      $matches = [];
      if (preg_match_all($full_date_regex, $entered_value, $matches)) {
        $date_string = $this->parseFullDate($entered_value, $matches);
        return array('0000-00-00', $date_string[0]);
      }
      // Date in the format of MMM yyyy or yyyy.
      $matches = [];
      if (preg_match_all($partial_date_regex, $entered_value, $matches)) {
        $date_string = $this->parsePartialDate($entered_value, $matches);
        return array('0000-00-00', $date_string[0]);
      }

    }

    // If date string starts with ABT or EST.
    if (in_array($beg_string_value, array('ABT', 'EST'))) {
      // Date in the format of dd MMM yyyy.
      $matches = [];
      if (preg_match_all($full_date_regex, $entered_value, $matches)) {
        $date_string = $this->parseEstFullDate($entered_value, $matches);
        return array($date_string[0], $date_string[1]);
      }
      // Date in the format of MMM yyyy or yyyy.
      $matches = [];
      if (preg_match_all($partial_date_regex, $entered_value, $matches)) {
        $date_string = $this->parseEstPartialDate($entered_value, $matches);
        return array($date_string[0], $date_string[1]);
      }

    }

    // If date is valid full date string.
    $matches = [];
    if (preg_match_all($full_date_regex, $entered_value, $matches)) {
      $date_string = $this->parseFullDate($entered_value, $matches);
      return array($date_string[0], $date_string[0]);
    }

    // If date is a valid partial date string.
    $matches = [];
    if (preg_match_all($partial_date_regex, $entered_value, $matches)) {
      $date_string = $this->parsePartialDate($entered_value, $matches);
      return array($date_string[0], $date_string[0]);
    }

    //Other dates.
    $date_string = $calculated_value;
    return $date_string;
  }

  /**
   * Returns a calculated date value from a full date string.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   * @param array $matches
   *   The captured groups from the regex string match.
   *
   * @return array
   *   The calculated indexed_values.
   */
  private function parseFullDate($entered_value, array $matches) {
    $date_string_1 = '0000-00-00';
    $date_string_2 = '9999-99-99';
    $monthlist = [
      'JAN' => '01',
      'FEB' => '02',
      'MAR' => '03',
      'APR' => '04',
      'MAY' => '05',
      'JUN' => '06',
      'JUL' => '07',
      'AUG' => '08',
      'SEP' => '09',
      'OCT' => '10',
      'NOV' => '11',
      'DEC' => '12',
    ];
    // Get first date value.
    if (in_array($matches[2][0], array_keys($monthlist))) {
      $month = $monthlist[$matches[2][0]];
      $year = $matches[3][0];
      $day = substr('00' . $matches[1][0], -2);
      $date_string_1 = $year . "-" . $month . "-" . $day;
    }
    // Get second date value.
    if (in_array($matches[2][1], array_keys($monthlist))) {
      $month = $monthlist[$matches[2][1]];
      $year = $matches[3][1];
      $day = substr('00' . $matches[1][1], -2);
      $date_string_2 = $year . "-" . $month . "-" . $day;
    }
    return array($date_string_1, $date_string_2);
  }

  /**
   * Returns an estimated calculated date value from a full date string.
   *
   * Estimated date values are +- 3 years from date entered.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   * @param array $matches
   *   The captured groups from the regex string match.
   *
   * @return array
   *   The calculated indexed_values.
   */
  private function parseEstFullDate($entered_value, array $matches) {
    $datevalues = $this->parseFullDate($entered_value, $matches);
    $year1 = (string) ((int) substr($datevalues[0], 0, 4) - 3);
    $year2 = (string) ((int) substr($datevalues[0], 0, 4) + 3);
    $datevalues[0] = $year1 . substr($datevalues[0], 4, strlen($datevalues[0]) - 4);
    $datevalues[1] = $year2 . substr($datevalues[1], 4, strlen($datevalues[1]) - 4);
    return $datevalues;
  }

  /**
   * Returns a calculated date value from a partial date string.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   * @param array $matches
   *   The captured groups from the regex string match.
   *
   * @return array
   *   The calculated indexed_values.
   */
  private function parsePartialDate($entered_value, array $matches) {
    $date_string_1 = '0000-00-00';
    $date_string_2 = '9999-99-99';
    $monthlist = [
      'JAN' => '01',
      'FEB' => '02',
      'MAR' => '03',
      'APR' => '04',
      'MAY' => '05',
      'JUN' => '06',
      'JUL' => '07',
      'AUG' => '08',
      'SEP' => '09',
      'OCT' => '10',
      'NOV' => '11',
      'DEC' => '12',
    ];
    // Get first date value.
    if (in_array($matches[1][0], array_keys($monthlist))) {
      $month = $monthlist[$matches[1][0]];
    }
    else {
      $month = '00';
    }
    $year = $matches[2][0];
    $day = '00';
    $date_string_1 = $year . "-" . $month . "-" . $day;
    // Get second date value.
    if (in_array($matches[1][1], array_keys($monthlist))) {
      $month = $monthlist[$matches[1][1]];
    }
    else {
      if (in_array($matches[1][0], array_keys($monthlist))) {
        $month = $monthlist[$matches[1][0]];
      }
      else {
        $month = '99';
      }
    }
    if (isset($matches[2][1])) {
     $year = $matches[2][1];
    }
    else {
      $year = '9999';
    }
    $day = '99';
    $date_string_2 = $year . "-" . $month . "-" . $day;
    return array($date_string_1, $date_string_2);
  }

  /**
   * Returns an estimated calculated date value from a partial date string.
   *
   * Estimated date values are +- 3 years from date entered.
   *
   * @param string $entered_value
   *   The value entered by the end user for the date.
   * @param array $matches
   *   The captured groups from the regex string match.
   *
   * @return array
   *   The calculated indexed_values.
   */
  private function parseEstPartialDate($entered_value, array $matches) {
    $datevalues = $this->parsePartialDate($entered_value, $matches);
    $year1 = (string) ((int) substr($datevalues[0], 0, 4) - 3);
    $year2 = (string) ((int) substr($datevalues[0], 0, 4) + 3);
    $datevalues[0] = $year1 . substr($datevalues[0], 4, strlen($datevalues[0]) - 4);
    $datevalues[1] = $year2 . substr($datevalues[1], 4, strlen($datevalues[1]) - 4);
    return $datevalues;
  }

}
