<?php

class PluginFormcreatorNotificationTargetForm_answer extends NotificationTarget
{
   const AUTHOR   = 101;
   const APPROVER = 102;


   public function getEvents() {
      $events = array (
         'plugin_formcreator_form_created'    => __('The form as been saved', 'formcreator'),
         'plugin_formcreator_need_validation' => __('A form need to be validate', 'formcreator'),
         'plugin_formcreator_refused'         => __('The form is refused', 'formcreator'),
         'plugin_formcreator_accepted'        => __('The form is accepted', 'formcreator'),
         'plugin_formcreator_deleted'         => __('The form is deleted', 'formcreator'),
      );
      return $events;
   }

   public function getDatasForTemplate($event, $options = array()) {
      global $CFG_GLPI;

      $form = new PluginFormcreatorForm();
      $form->getFromDB($this->obj->fields['plugin_formcreator_forms_id']);
      $link = $CFG_GLPI['url_base'];
      $link .= '/plugins/formcreator/front/form_answer.form.php?id=' . $this->obj->getID();

      $requester = new User();
      $requester->getFromDB($this->obj->fields['requester_id']);
      $validator = new User();
      $validator->getFromDB($this->obj->fields['validator_id']);

      $this->datas['##formcreator.form_id##']            = $form->getID();
      $this->datas['##formcreator.form_name##']          = $form->fields['name'];
      $this->datas['##formcreator.form_requester##']     = $requester->getName();
      $this->datas['##formcreator.form_validator##']     = $validator->getName();
      $this->datas['##formcreator.form_creation_date##'] = Html::convDateTime($this->obj->fields['request_date']);
      $this->datas['##formcreator.form_full_answers##']  = $this->obj->getFullForm();
      $this->datas['##formcreator.validation_comment##'] = $this->obj->fields['comment'];
      $this->datas['##formcreator.validation_link##']    = $link;
      $this->datas['##formcreator.request_id##']         = $this->obj->fields['id'];
   }

   public function getTags() {
      $tags = array(
         'formcreator.form_id'            => __('Form #', 'formcreator'),
         'formcreator.form_name'          => __('Form name', 'formcreator'),
         'formcreator.form_requester'     => __('Requester', 'formcreator'),
         'formcreator.form_validator'     => __('Validator', 'formcreator'),
         'formcreator.form_creation_date' => __('Creation date'),
         'formcreator.form_full_answers'  => __('Full form answers', 'formcreator'),
         'formcreator.validation_comment' => __('Refused comment', 'formcreator'),
         'formcreator.validation_link'    => __('Validation link', 'formcreator'),
         'formcreator.request_id'         => __('Request #', 'formcreator'),
      );

      foreach ($tags as $tag => $label) {
         $this->addTagToList(array('tag'    => $tag,
               'label'  => $label,
               'value'  => true,
               'events' => NotificationTarget::TAG_FOR_ALL_EVENTS));
      }
   }

   public function getAdditionalTargets($event='') {
      $this->addTarget(self::AUTHOR, __('Author'));
      $this->addTarget(self::APPROVER, __('Approver'));
   }

   public function getSpecificTargets($data, $options) {
      switch ($data['items_id']) {
         case self::AUTHOR :
            $this->getUserByField('requester_id', true);
            break;
         case self::APPROVER :
            $form = new PluginFormcreatorForm();
            $form->getFromDB($this->obj->fields['plugin_formcreator_forms_id']);
            if ($form->fields['validation_required'] == 1) {
               $this->getUserByField('validator_id', true);
            } else if ($form->fields['validation_required'] == 2) {
               $this->getAddressesByGroup(0, $this->obj->fields['validator_id']);
            }
            break;
      }
   }
}
