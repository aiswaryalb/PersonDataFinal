<?php

namespace Drupal\person_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying person and salary data.
 */
class PersonDataController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Constructs a PersonDataController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Display person and salary data.
   */
  public function content() {
    $query = $this->database->select('person', 'p');
    $query->fields('p', ['id', 'name']);

    $query->join('person_salary', 'ps', 'p.id = ps.id');
    $query->fields('ps', ['month', 'year', 'salary']);
    
    $result = $query->orderBy('p.id')->execute()->fetchAllAssoc('id');

    // Build a render array with the data.
    $rows = [];
    foreach ($result as $row) {
      $rows[] = [
        'id' => $row->id,
        'name' => $row->name,
        'month' => $row->month,
        'year' => $row->year,
        'salary' => $row->salary,
      ];
    }

    // Create a table to display the data.
    $header = [
      'id' => $this->t('ID'),
      'name' => $this->t('Name'),
      'month' => $this->t('Month'),
      'year' => $this->t('Year'),
      'salary' => $this->t('Salary'),
    ];

    $table = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No data available'),
    ];

    return $table;
  }
}
