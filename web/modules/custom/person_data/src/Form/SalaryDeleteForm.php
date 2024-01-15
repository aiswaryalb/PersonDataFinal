<?php

/**
 * @file
 * A form to delete salary details.
 */

namespace Drupal\person_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SalaryDeleteForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'person_salary_delete_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        if (\Drupal::currentUser()->hasPermission('delete salary details')) {
            $form['delete'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Delete by ID'),
                '#required' => TRUE,
              ];

            $form['submit'] = [
                '#type' => 'submit',
                '#value' => $this->t('Delete Salary Details'),
            ];
        } else {
            \Drupal::messenger()->addError($this->t('You do not have permission to delete salary details.'));
        }

        return $form;
    }


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form , FormStateInterface $form_state) {
        try {
            $id = $form_state->getValue('delete');
            $connection = \Drupal::database();
            $connection->delete('person_salary')
                ->condition('id', $id)
                ->execute();
            \Drupal::messenger()->addMessage(t("Salary details with ID @id deleted successfully.", ['@id' => $id]));
        } catch (\Exception $e) {
            \Drupal::messenger()->addError(t("Unable to delete salary details this time."));
        }
    }
}
