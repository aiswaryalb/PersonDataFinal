<?php

namespace Drupal\person_data;

use Drupal\node\Entity\Node;
use Drupal\person_data\Entity\Person;

/**
 * addImportContent class.
 */
class addImportContent {

  /**
   * Batch operation to create a Person entity from CSV data.
   */
  public static function addImportContentItem($item, &$context) {
    $context['sandbox']['current_item'] = $item;
    $message = 'Creating ' . $item['name']; // Adjust the key according to your CSV structure.
    $results = [];

    // Creating a Person entity.
    create_person($item);

    $context['message'] = $message;
    $context['results'][] = $item;
  }

  /**
   * Batch finished callback.
   */
  public static function addImportContentItemCallback($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One item processed.', '@count items processed.'
      );
      \Drupal::messenger()->addMessage($message);
    } else {
      $message = t('Finished with an error.');
      \Drupal::messenger()->addError($message);
    }
  }
}

/**
 * Function to create a Person entity.
 */
function create_person($item) {
  try {
    // Creating a new Person entity.
    $person = Person::create([
      'name' => $item['name'],
      'id' => $item['id'],
      'location' => $item['location'],
      'age' => $item['age'],
      // Add other fields as needed.
    ]);

    // Save the Person entity.
    $person->save();
  } catch (\Exception $e) {
    // Log any exceptions.
    \Drupal::logger('person_data')->error('Error creating Person entity: @error', ['@error' => $e->getMessage()]);
  }
}
