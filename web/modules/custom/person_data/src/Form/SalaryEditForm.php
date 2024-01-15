<?php

/**
 * @file
 * A form to edit salary details.
 */

namespace Drupal\person_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SalaryEditForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'person_salary_edit_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        
        if (\Drupal::currentUser()->hasPermission('edit salary details')) {

            if ($id === NULL) {
                $id = $this->getCurrentId();
            }

            $entry = $this->loadSalaryDetails($id);

            if (!$entry) {
                $this->messenger()->addError($this->t('Salary details not found.'));
                return $form;
            }

            $form['id'] = [
                '#type' => 'number',
                '#title' => $this->t('ID'),
                '#required' => TRUE,
                '#default_value' => $entry['id'],
            ];
    
            $form['month'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Month'),
                '#required' => TRUE,
                '#default_value' => $entry['month'],

            ];
            $form['year'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Year'),
                '#required' => TRUE,
                '#default_value' => $entry['year'],
            ];
            $form['salary'] = [
                '#type' => 'number',
                '#title' => $this->t('Salary'),
                '#required' => TRUE,
                '#default_value' => $entry['salary'],
            ];
    
            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Update Salary Details'),
            ];    
        } 
        else {
            \Drupal::messenger()->addError($this->t('You do not have permission to add salary details.'));
        }
        return $form;

    }

     /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        //Checking if the year entered is valid
        $year = $values['year'];
        if (!is_numeric($year) || strlen($year) !== 4 || $year < 1900 || $year > date('Y')) {
            $form_state->setErrorByName('year', $this->t('Please enter a valid year.'));
        }
        //Checking if month enetered is valid
        $month = strtolower($form_state->getValue('month'));
        $valid_month_names = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december',
        ];
        if (!in_array($month, $valid_month_names)) {
            $form_state->setErrorByName('month', $this->t('Please enter a valid month name.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form , FormStateInterface $form_state) {
        $id = $this->getCurrentId();
        $this->updateSalaryDetails($id, $form_state->getValues());
        $this->messenger()->addMessage($this->t('Salary Detail updated successfully.'));
    }

    // Defining additional functions
    private function getCurrentId() {
        $id = \Drupal::routeMatch()->getParameter('id');
        return $id;
    }

    private function loadSalaryDetails($id) {
        try {
            $connection = \Drupal::database();
            $query = $connection->select('person_salary', 'ae')
            ->fields('ae')
            ->condition('id', $id)
            ->execute();
            return $query->fetchAssoc();
        }
        catch (\Exception $e) {
            \Drupal::messenger()->addError(t("Unable to load salary details this time."));

        }
    }
    private function updateSalaryDetails($id, $values) {
        try {
            $connection = \Drupal::database();
            $connection->update('person_salary')
            ->fields([
                'id' => $values['id'],
                'month' => $values['month'],
                'year' => $values['year'],
                'salary' => $values['salary'],
            ])
            ->condition('id', $id)
            ->execute();
        }
        catch (\Exception $e) {
            \Drupal::messenger()->addError(t("Unable to edit salary this time."));
        }
    }
}