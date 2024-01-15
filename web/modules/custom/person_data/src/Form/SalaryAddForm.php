<?php

/**
 * @file
 * A form to add salary details.
 */

namespace Drupal\person_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SalaryAddForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'person_salary_add_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state) {
        
        if (\Drupal::currentUser()->hasPermission('add salary details')) {
            $form['id'] = [
                '#type' => 'number',
                '#title' => $this->t('ID'),
                '#required' => TRUE,
            ];
    
            $form['month'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Month'),
                '#required' => TRUE,
            ];
            $form['year'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Year'),
                '#required' => TRUE,
            ];
            $form['salary'] = [
                '#type' => 'number',
                '#title' => $this->t('Salary'),
                '#required' => TRUE,
            ];
    
            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Add Salary Details'),
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
        try {
            $values = $form_state->getValues();

            $connection = \Drupal::database();
            $connection->insert('person_salary')
            ->fields([
                'id' => $values['id'],
                'month' => $values['month'],
                'year' => $values['year'],
                'salary' => $values['salary'],
            ])->execute();
            \Drupal::messenger()->addMessage(t("Salary details added successfully"));
        }
        catch (\Exception $e) {
            \Drupal::messenger()->addError(t("Unable to save salary details this time."));

        }
    }
}