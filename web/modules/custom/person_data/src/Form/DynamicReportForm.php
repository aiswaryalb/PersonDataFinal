<?php

namespace Drupal\person_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;

class DynamicReportForm extends FormBase {

  protected $database;

  /**
   * DynamicReportForm constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dynamic_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['report_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Report Type'),
      '#options' => [
        'age' => $this->t('Age'),
        'location' => $this->t('Location'),
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate Report'),
    ];

    // Check if the form has been submitted and the table is set in form state.
    if ($form_state->isSubmitted() && $form_state->hasValue('table')) {
      $table = $form_state->getValue('table');

      // Add the table to the form.
      $form['table'] = $table;
    }
   
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $report_type = $form_state->getValue('report_type');

    switch ($report_type) {
      case 'age':
        // Implement logic for age-wise report.
        $query = $this->database->select('person', 'p');
        $query->fields('p', ['age'])
          ->addExpression('COUNT(*)', 'count');
        $results = $query->groupBy('age')->execute()->fetchAll();

        // Build a render array for the age-wise table.
        $table = [
          '#theme' => 'table',
          '#header' => [
            'Age',
            'Number of Persons',
          ],
          '#rows' => [],
        ];

        foreach ($results as $row) {
          $table['#rows'][] = [
            'age' => $row->age,
            'count' => $row->count,
          ];
        }
        
        $form_state->setValue('table', $table);
        break;

      case 'location':
        // Implement logic for location-wise report.
        $query = $this->database->select('person', 'p');
        $query->fields('p', ['location'])
          ->addExpression('COUNT(*)', 'count');
        $results = $query->groupBy('location')->execute()->fetchAll();

        // Build a render array for the location-wise table.
        $table = [
          '#theme' => 'table',
          '#header' => [
            'Location',
            'Number of Persons',
          ],
          '#rows' => [],
        ];

        foreach ($results as $row) {
          $table['#rows'][] = [
            'location' => $row->location,
            'count' => $row->count,
          ];
        }

       

        $form_state->setValue('table', $table);
        break;
    }
    
    $form_state->setRebuild();
  }
}

