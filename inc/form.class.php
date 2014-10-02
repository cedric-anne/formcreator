<?php
class PluginFormcreatorForm extends CommonDBTM
{
   public $dohistory       = true;

   const ACCESS_PUBLIC     = 0;
   const ACCESS_PRIVATE    = 1;
   const ACCESS_RESTRICTED = 2;

   /**
    * Check if current user have the right to create and modify requests
    *
    * @return boolean True if he can create and modify requests
    */
   public static function canCreate()
   {
      return true;
   }

   /**
    * Check if current user have the right to read requests
    *
    * @return boolean True if he can read requests
    */
   public static function canView()
   {
      return true;
   }

   /**
    * Returns the type name with consideration of plural
    *
    * @param number $nb Number of item(s)
    * @return string Itemtype name
    */
   public static function getTypeName($nb = 0)
   {
      return _n('Form', 'Forms', $nb, 'formcreator');
   }

   /**
    * Define search options for forms
    *
    * @return Array Array of fields to show in search engine and options for each fields
    */
   public function getSearchOptions()
   {
      $tab = array(
         '2' => array(
            'table'         => $this->getTable(),
            'field'         => 'id',
            'name'          => __('ID'),
            'searchtype'    => 'contains',
            'massiveaction' => false,
         ),
         '1' => array(
            'table'         => $this->getTable(),
            'field'         => 'name',
            'name'          => __('Name'),
            'datatype'      => 'itemlink',
            'massiveaction' => false,
         ),
         '4' => array(
            'table'         => $this->getTable(),
            'field'         => 'description',
            'name'          => __('Description', 'formcreator'),
            'massiveaction' => false,
         ),
         '5' => array(
            'table'         => 'glpi_entities',
            'field'         => 'completename',
            'name'          => _n('Entity', 'Entities', 1),
            'datatype'      => 'dropdown',
            'massiveaction' => false,
         ),
         '6' => array(
            'table'         => $this->getTable(),
            'field'         => 'is_recursive',
            'name'          => __('Recursive'),
            'datatype'      => 'bool',
            'massiveaction' => false,
         ),
         '7' => array(
            'table'         => $this->getTable(),
            'field'         => 'language',
            'name'          => __('Language'),
            'datatype'      => 'specific',
            'searchtype'    => array('equals'),
            'massiveaction' => false,
         ),
         '8' => array(
            'table'         => $this->getTable(),
            'field'         => 'helpdesk_home',
            'name'          => __('Homepage', 'formcreator'),
            'datatype'      => 'bool',
            'searchtype'    => array('equals', 'notequals'),
            'massiveaction' => true,
         ),
         '9' => array(
            'table'         => $this->getTable(),
            'field'         => 'access_rights',
            'name'          => __('Access', 'formcreator'),
            'datatype'      => 'specific',
            'searchtype'    => array('equals', 'notequals'),
            'massiveaction' => true,
         ),
         '10' => array(
            'table'         => getTableForItemType('PluginFormcreatorCategory'),
            'field'         => 'name',
            'name'          => PluginFormcreatorCategory::getTypeName(1),
            'datatype'      => 'dropdown',
            'massiveaction' => true,

         ),
         '30' => array(
            'table'         => $this->getTable(),
            'field'         => 'is_active',
            'name'          => __('Active'),
            'datatype'      => 'specific',
            'searchtype'    => array('equals', 'notequals'),
            'massiveaction' => true,
         ),
      );
      return $tab;
   }

   // /**
   //  * Define default search request
   //  *
   //  * @return Array Array of search options : [field, searchtype, contains, sort, order]
   //  */
   // public static function getDefaultSearchRequest()
   // {
   //    $search = array('field'      => array(0 => 30),
   //                    'searchtype' => array(0 => 'equals'),
   //                    'contains'   => array(0 => 30),
   //                    'sort'       => 2,
   //                    'order'      => 'ASC');
   //    return $search;
   // }

   /**
    * Define how to display search field for a specific type
    *
    * @since version 0.84
    *
    * @param String $field           Name of the field as define in $this->getSearchOptions()
    * @param String $name            Name attribute for the field to be posted (default '')
    * @param Array  $values          Array of all values to display in search engine (default '')
    * @param Array  $options         Options (optional)
    *
    * @return String                 Html string to be displayed for the form field
    **/
   public static function getSpecificValueToSelect($field, $name='', $values='', array $options=array())
   {

      if (!is_array($values)) {
         $values = array($field => $values);
      }
      $options['display'] = false;

      switch ($field) {
         case 'is_active' :
            $output  = "<select name='".$name."'>";
            $output .=  "<option value='0' ".(($values[$field] == 0)?" selected ":"").">"
                        . __('Inactive')
                        . "</option>";
            $output .=  "<option value='1' ".(($values[$field] == 1)?" selected ":"").">"
                        . __('Active')
                        . "</option>";
            $output .=  "</select>";

            return $output;
            break;
         case 'access_rights' :
            $output  = '<select name="' . $name . '">';
            $output .=  '<option value="' . self::ACCESS_PUBLIC . '" '
                           . (($values[$field] == 0) ? ' selected ' : '') . '>'
                        . __('Public access', 'formcreator')
                        . '</option>';
            $output .=  '<option value="' . self::ACCESS_PRIVATE . '" '
                           . (($values[$field] == 1) ? ' selected ' : '') . '>'
                        . __('Private access', 'formcreator')
                        . '</option>';
            $output .=  '<option value="' . self::ACCESS_RESTRICTED . '" '
                           . (($values[$field] == 1) ? ' selected ' : '') . '>'
                        . __('Restricted access', 'formcreator')
                        . '</option>';
            $output .=  '</select>';

            return $output;
            break;
         case 'language' :
            return Dropdown::showLanguages('language', array(
               'value'               => $values[$field],
               'display_emptychoice' => true,
               'emptylabel'          => '--- ' . __('All langages', 'formcreator') . ' ---',
               'display'             => false
            ));
            break;
      }
      return parent::getSpecificValueToSelect($field, $name, $values, $options);
   }


   /**
    * Define how to display a specific value in search result table
    *
    * @param  String $field   Name of the field as define in $this->getSearchOptions()
    * @param  Mixed  $values  The value as it is stored in DB
    * @param  Array  $options Options (optional)
    * @return Mixed           Value to be displayed
    */
   public static function getSpecificValueToDisplay($field, $values, array $options=array())
   {
      if (!is_array($values)) {
         $values = array($field => $values);
      }
      switch ($field) {
         case 'is_active':
            if($values[$field] == 0) {
               $output = '<div style="text-align: center"><img src="' . $GLOBALS['CFG_GLPI']['root_doc'] . '/plugins/formcreator/pics/inactive.png"
                           height="16" width="16"
                           alt="' . __('Inactive') . '"
                           title="' . __('Inactive') . '" /></div>';
            } else {
               $output = '<div style="text-align: center"><img src="' . $GLOBALS['CFG_GLPI']['root_doc'] . '/plugins/formcreator/pics/active.png"
                           height="16" width="16"
                           alt="' . __('Active') . '"
                           title="' . __('Active') . '" /></div>';
            }
            return $output;
            break;
         case 'access_rights':
            switch ($values[$field]) {
               case self::ACCESS_PUBLIC :
                  return __('Public access', 'formcreator');
                  break;
               case self::ACCESS_PRIVATE :
                  return __('Private access', 'formcreator');
                  break;
               case self::ACCESS_RESTRICTED :
                  return __('Restricted access', 'formcreator');
                  break;
            }
            return '';
            break;
         case 'language' :
            if (empty($values[$field])) {
               return __('All langages', 'formcreator');
            } else {
               return Dropdown::getLanguageName($values[$field]);
            }
            break;
      }
      return parent::getSpecificValueToDisplay($field, $values, $options);
   }

   /**
    * Show the Form edit form the the adminsitrator in the config page
    *
    * @param  Array  $options Optional options
    *
    * @return NULL         Nothing, just display the form
    */
   public function showForm($ID, $options=array())
   {
      $this->initForm($ID, $options);
      $this->showTabs($options);
      $this->showFormHeader($options);

      echo '<tr class="tab_bg_1">';
      echo '<td><strong>' . __('Name') . ' <span class="red">*</span></strong></td>';
      echo '<td><input type="text" name="name" value="' . $this->fields["name"] . '" size="54"/></td>';
      echo '<td><strong>' . __('Active') . ' <span class="red">*</span></strong></td>';
      echo '<td>';
      Dropdown::showYesNo("is_active", $this->fields["is_active"]);
      echo '</td>';
      echo '</tr>';

      echo '<tr class="tab_bg_2">';
      echo '<td><strong>' . __('Category') . ' <span class="red">*</span></strong></td>';
      echo '<td>';
      PluginFormcreatorCategory::dropdown(array(
         'name'  => 'plugin_formcreator_categories_id',
         'value' => ($ID != 0) ? $this->fields["plugin_formcreator_categories_id"] : 1,
      ));
      echo '</td>';
      echo '<td>' . __('Direct access on homepage', 'formcreator') . '</td>';
      echo '<td>';
      Dropdown::showYesNo("helpdesk_home", $this->fields["helpdesk_home"]);
      echo '</td>';

      echo '</tr>';

      echo '<tr class="tab_bg_1">';
      echo '<td>' . __('Description') . '</td>';
      echo '<td><input type="text" name="description" value="' . $this->fields['description'] . '" size="54" /></td>';
      echo '<td>' . __('Language') . '</td>';
      echo '<td>';
      Dropdown::showLanguages('language', array(
         'value'               => ($ID != 0) ? $this->fields['language'] : $_SESSION['glpilanguage'],
         'display_emptychoice' => true,
         'emptylabel'          => '--- ' . __('All langages', 'formcreator') . ' ---',
      ));
      echo '</td>';
      echo '</tr>';

      echo '<tr class="tab_bg_1">';
      echo '<td>' . _n('Header', 'Headers', 1, 'formcreator') . '</td>';
      echo '<td colspan="3"><textarea name="content" cols="115" rows="10">' . $this->fields["content"] . '</textarea></td>';
      Html::initEditorSystem('content');
      echo '</tr>';

      echo '<tr class="tab_bg_2">';
      echo '<td>' . __('Need to be validate?', 'formcreator') . '</td>';
      echo '<td>';
      Dropdown::showYesNo("validation_required",
         $this->fields["validation_required"],
         -1,
         array('on_change' => 'changeValidators(this.value)'));
      echo '</td>';
      echo '<td><label for="validators" id="label_validators">' . __('Available validators', 'formcreator') . '</label></td>';
      echo '<td>';

      $validators = array();
      $query = "SELECT `users_id`
                FROM `glpi_plugin_formcreator_formvalidators`
                WHERE `forms_id` = '" . $this->getID(). "'";
      $result = $GLOBALS['DB']->query($query);
      while(list($id) = $GLOBALS['DB']->fetch_array($result)) {
         $validators[] = $id;
      }

      $subentities = getSonsOf('glpi_entities', $this->fields["entities_id"]);
      $query = 'SELECT u.`id`, u.`name`, u.`realname`
                FROM `glpi_users` u
                INNER JOIN `glpi_profiles_users` pu ON u.`id` = pu.`users_id`
                INNER JOIN `glpi_profiles` p ON p.`id` = pu.`profiles_id`
                WHERE (p.`validate_request` = 1 OR p.`validate_incident` = 1)
                AND (pu.`entities_id` = ' . $this->fields["entities_id"] . '
                OR (pu.`is_recursive` = 1 AND pu.entities_id IN (' . implode(',', $subentities). ')))
                GROUP BY u.`id`
                ORDER BY u.`name`';
      $result = $GLOBALS['DB']->query($query);
      echo '<select name="_validators[]" size="4" style="width: 100%" multiple id="validators">';
      while($user = $GLOBALS['DB']->fetch_assoc($result)) {
         echo '<option value="' . $user['id'] . '"';
         if (in_array($user['id'], $validators)) echo ' selected="selected"';
         echo '>' . $user['name'] . '</option>';
      }
      echo '</select>';
      echo '<script type="text/javascript">
               function changeValidators(value) {
                  if (value == 1) {
                     document.getElementById("label_validators").style.display = "inline";
                     document.getElementById("validators").style.display       = "inline";
                  } else {
                     document.getElementById("label_validators").style.display = "none";
                     document.getElementById("validators").style.display       = "none";
                  }
               }
               changeValidators(' . $this->fields["validation_required"] . ');
            </script>';
      echo '</td>';
      echo '</tr>';

      echo '</td>';
      echo '</tr>';

      $this->showFormButtons($options);
      $this->addDivForTabs();
   }


   /**
    * Return the name of the tab for item including forms like the config page
    *
    * @param  CommonGLPI $item         Instance of a CommonGLPI Item (The Config Item)
    * @param  integer    $withtemplate
    *
    * @return String                   Name to be displayed
    */
   public function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
   {
      switch ($item->getType()) {
         case "PluginFormcreatorConfig":
            $object  = new self;
            $founded = $object->find();
            $number  = count($founded);
            return self::createTabEntry(self::getTypeName($number), $number);
            break;
         case "PluginFormcreatorForm":
            return __('Preview');
            break;
      }
      return '';
   }

   /**
    * Display a list of all forms on the configuration page
    *
    * @param  CommonGLPI $item         Instance of a CommonGLPI Item (The Config Item)
    * @param  integer    $tabnum       Number of the current tab
    * @param  integer    $withtemplate
    *
    * @see CommonDBTM::displayTabContentForItem
    *
    * @return null                     Nothing, just display the list
    */
   public static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0)
   {
      $uri = strrchr($_SERVER['HTTP_REFERER'], '/');
      if(strpos($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
      $uri = trim($uri, '/');

      switch ($uri) {
         case "form.form.php":
            echo '<div style="text-align: left">';
            $item->displayUserForm($item);
            echo '</div>';
            break;
      }
   }


   public function defineTabs($options=array())
   {
      $ong = array();
      $this->addStandardTab('PluginFormcreatorQuestion', $ong, $options);
      $this->addStandardTab('PluginFormcreatorFormprofiles', $ong, $options);
      $this->addStandardTab('PluginFormcreatorTarget', $ong, $options);
      $this->addStandardTab(__CLASS__, $ong, $options);
      return $ong;
   }

   /**
    * Show the list of forms to be displayed to the end-user
    */
   public function showList()
   {
      echo '<div class="center">';

      // Show header for the current entity or it's first parent header
      $table  = getTableForItemType('PluginFormcreatorHeader');
      $where  = getEntitiesRestrictRequest( "", $table, "", "", true, false);
      $query  = "SELECT $table.`comment`
                 FROM $table
                 WHERE $where";
      $result = $GLOBALS['DB']->query($query);
      if (!empty($result)) {
         list($description) = $GLOBALS['DB']->fetch_array($result);
         if (!empty($description)) {
            echo '<table class="tab_cadre_fixe">';
            echo '<tr><td>' . html_entity_decode($description) . '</td></tr>';
            echo '</table>';
            echo '<br />';
         }
      }

      echo '<div style="width: 950px; margin: 0 auto;">';
      echo '<div style="float:right; width: 375px;">';
         echo '<table class="tab_cadrehov" style="width: 375px">';
            echo '<tr><th colspan="2">' . __('My last forms (requester)', 'formcreator') . '</th></tr>';
            $query = "SELECT fa.`id`, f.`name`, fa.`status`, fa.`request_date`
                      FROM glpi_plugin_formcreator_forms f
                      INNER JOIN glpi_plugin_formcreator_formanswers fa ON f.`id` = fa.`plugin_formcreator_forms_id`
                      WHERE fa.`requester_id` = '" . $_SESSION['glpiID'] . "'
                      ORDER BY fa.`status` ASC, fa.`request_date` DESC
                      LIMIT 0, 5";
            $result = $GLOBALS['DB']->query($query);
            if ($GLOBALS['DB']->numrows($result) == 0) {
               echo '<tr><td colspan="2" class="line1" align="center">' . __('No form posted yet', 'formcreator') . '</td></tr>';
            } else {
               while ($form = $GLOBALS['DB']->fetch_assoc($result)) {
                  $img_dir = $GLOBALS['CFG_GLPI']['root_doc'] . '/plugins/formcreator/pics/';
                  $img = '<img src="' . $img_dir . '' . $form['status'] . '.png" align="absmiddle"
                              alt="' . __($form['status'], 'formcreator') . '"
                              title="' . __($form['status'], 'formcreator') . '" />';
                  echo '<tr>';
                  echo '<td>' . $img . ' <a href="formanswer.form.php?id=' . $form['id'] . '">' . $form['name'] . '</a></td>';
                  echo '<td align="center" width="35%">' . Html::convDateTime($form['request_date']) . '</td>';
                  echo '</tr>';
               }
               echo '<tr>';
               echo '<th colspan="2" align="center">';
               echo '<a href="formanswer.php?field[0]=4&searchtype[0]=equals&contains[0]=2">';
               echo __('All my forms (requester)', 'formcreator');
               echo '</a>';
               echo '</th>';
               echo '</tr>';
            }
         echo '</table>';

         echo '<br />';

         if ((isset($_SESSION['glpiactiveprofile']['validate_request'])
               && ($_SESSION['glpiactiveprofile']['validate_request'] == 1))
            || (isset($_SESSION['glpiactiveprofile']['validate_incident'])
               && ($_SESSION['glpiactiveprofile']['validate_incident'] == 1))) {
            echo '<table class="tab_cadrehov" style="width: 375px">';
            echo '<tr><th colspan="2">' . __('My last forms (validator)', 'formcreator') . '</t></tr>';
            $query = "SELECT fa.`id`, f.`name`, fa.`status`, fa.`request_date`
                      FROM glpi_plugin_formcreator_forms f
                      INNER JOIN glpi_plugin_formcreator_formanswers fa ON f.`id` = fa.`plugin_formcreator_forms_id`
                      WHERE fa.`validator_id` = '" . $_SESSION['glpiID'] . "'
                      ORDER BY fa.`status` ASC, fa.`request_date` DESC
                      LIMIT 0, 5";
            $result = $GLOBALS['DB']->query($query);
            if ($GLOBALS['DB']->numrows($result) == 0) {
               echo '<tr><td colspan="2" class="line1" align="center">' . __('No form waiting for validation', 'formcreator') . '</td></tr>';
            } else {
               while ($form = $GLOBALS['DB']->fetch_assoc($result)) {
                  $img_dir = $GLOBALS['CFG_GLPI']['root_doc'] . '/plugins/formcreator/pics/';
                  $img = '<img src="' . $img_dir . '' . $form['status'] . '.png" align="absmiddle"
                              alt="' . __($form['status'], 'formcreator') . '"
                              title="' . __($form['status'], 'formcreator') . '" />';
                  echo '<tr>';
                  echo '<td>' . $img . ' <a href="formanswer.form.php?id=' . $form['id'] . '">' . $form['name'] . '</a></td>';
                  echo '<td align="center" width="35%">' . Html::convDateTime($form['request_date']) . '</td>';
                  echo '</tr>';
               }
               echo '<tr>';
               echo '<th colspan="2" align="center">';
               echo '<a href="formanswer.php?field[0]=5&searchtype[0]=equals&contains[0]=2">';
               echo __('All my forms (validator)', 'formcreator');
               echo '</a>';
               echo '</th>';
               echo '</tr>';
            }
            echo '</table>';
         }

      echo '</div>';
      echo '<div style="width: 550px;">';

      // Show categories wicth have at least one form user can access
      $cat_table  = getTableForItemType('PluginFormcreatorCategory');
      $form_table = getTableForItemType('PluginFormcreatorForm');
      $table_fp   = getTableForItemType('PluginFormcreatorFormprofiles');
      $where      = getEntitiesRestrictRequest( "", $form_table, "", "", true, false);
      $query  = "SELECT $cat_table.`name`, $cat_table.`id`
                 FROM $cat_table
                 WHERE 0 < (
                     SELECT COUNT($form_table.id)
                     FROM $form_table
                     WHERE $form_table.`plugin_formcreator_categories_id` = $cat_table.`id`
                     AND $form_table.`is_active` = 1
                     AND ($form_table.`language` = '{$_SESSION['glpilanguage']}' OR $form_table.`language` = '')
                     AND $where
                     AND ($form_table.`access_rights` != " . self::ACCESS_RESTRICTED . " OR $form_table.`id` IN (
                        SELECT plugin_formcreator_forms_id
                        FROM $table_fp
                        WHERE plugin_formcreator_profiles_id = " . (int) $_SESSION['glpiactiveprofile']['id'] . "))
                  )
                 ORDER BY $cat_table.`name` ASC";
      $result = $GLOBALS['DB']->query($query);
      if (!empty($result)) {

         // For each categories, show the list of forms the user can fill
         while ($category = $GLOBALS['DB']->fetch_array($result)) {
         echo '<table class="tab_cadrehov" style="width: 550px">';
            echo '<tr><th>' . $category['name'] . '</th></tr>';

            $where       = getEntitiesRestrictRequest( "", $form_table, "", "", true, false);
            $table_fp    = getTableForItemType('PluginFormcreatorFormprofiles');
            $query_forms = "SELECT $form_table.id, $form_table.name, $form_table.description
                            FROM $form_table
                            WHERE $form_table.`plugin_formcreator_categories_id` = {$category['id']}
                            AND $form_table.`is_active` = 1
                            AND ($form_table.`language` = '{$_SESSION['glpilanguage']}' OR $form_table.`language` = '')
                            AND $where
                            AND (`access_rights` != " . self::ACCESS_RESTRICTED . " OR $form_table.`id` IN (
                               SELECT plugin_formcreator_forms_id
                               FROM $table_fp
                               WHERE plugin_formcreator_profiles_id = " . (int) $_SESSION['glpiactiveprofile']['id'] . "))
                            ORDER BY $form_table.name ASC";
            $result_forms = $GLOBALS['DB']->query($query_forms);
            $i = 0;
            while ($form = $GLOBALS['DB']->fetch_array($result_forms)) {
               $i++;
               echo '<tr class="line' . ($i % 2) . '">';
               echo '<td>';
               echo '<img src="' . $GLOBALS['CFG_GLPI']['root_doc'] . '/pics/plus.png" alt="+" title=""
                         onclick="showDescription(' . $form['id'] . ', this)" align="absmiddle" style="cursor: pointer">';
               echo '&nbsp;';
               echo '<a href="' . $GLOBALS['CFG_GLPI']['root_doc']
                        . '/plugins/formcreator/front/showform.php?id=' . $form['id'] . '"
                        title="' . htmlentities(strip_tags($form['description'])) . '">'
                        . $form['name']
                        . '</a></td>';
               echo '</tr>';
               echo '<tr id="desc' . $form['id'] . '" class="line' . ($i % 2) . ' form_description">';
               echo '<td><div>' . $form['description'] . '&nbsp;</div></td>';
               echo '</tr>';
            }

         echo '</table>';
         echo '<br />';
         }
      }
      echo '</div>';

      echo '</div>';
      echo '</div>';
      echo '<hr style="clear:both; height:0; background: transparent; border:none" />';
      echo '<script type="text/javascript">
               function showDescription(id, img){
                  if(img.alt == "+") {
                    img.alt = "-";
                    img.src = "' . $GLOBALS['CFG_GLPI']['root_doc'] . '/pics/moins.png";
                    document.getElementById("desc" + id).style.display = "table-row";
                  } else {
                    img.alt = "+";
                    img.src = "' . $GLOBALS['CFG_GLPI']['root_doc'] . '/pics/plus.png";
                    document.getElementById("desc" + id).style.display = "none";
                  }
               }
            </script>';
   }

   /**
    * Display the Form end-user form to be filled
    *
    * @param  CommonGLPI   $item       Instance of the Form to be displayed
    *
    * @return Null                     Nothing, just display the form
    */
   public function displayUserForm(CommonGLPI $item)
   {
      if(isset($_SESSION['formcreator']['datas'])) {
         $datas = $_SESSION['formcreator']['datas'];
         unset($_SESSION['formcreator']['datas']);
      } else {
         $datas = null;
      }

      echo '<form name="formcreator_form' . $item->getID() . '" method="post" role="form" enctype="multipart/form-data"
               action="' . $GLOBALS['CFG_GLPI']['root_doc'] . '/plugins/formcreator/front/form.form.php" class="formcreator_form form_horizontal">';
      echo '<h1 class="form-title">' . $item->fields['name'] . '</h1>';

      // Form Header
      if (!empty($item->fields['content'])) {
         echo '<div class="form_header">';
         echo html_entity_decode($item->fields['content']);
         echo '</div>';
      }
      // Get and display sections of the form
      $question      = new PluginFormcreatorQuestion();

      $section_class = new PluginFormcreatorSection();
      $find_sections = $section_class->find('plugin_formcreator_forms_id = ' . $item->getID(), '`order` ASC');
      echo '<div class="form_section">';
      foreach ($find_sections as $section_line) {
         echo '<h2>' . $section_line['name'] . '</h2>';

         // Display all fields of the section
         $questions = $question->find('plugin_formcreator_sections_id = ' . $section_line['id'], '`order` ASC');
         foreach ($questions as $question_line) {
            PluginFormcreatorFields::showField($question_line, $datas);
         }

      }

      // Show validator selector
      if ($item->fields['validation_required']) {
         $query = "SELECT u.`id`, u.`name`, u.`realname`, u.`firstname`
                   FROM `glpi_users` u
                   LEFT JOIN `glpi_plugin_formcreator_formvalidators` fv ON fv.`users_id` = u.`id`
                   WHERE fv.`forms_id` = '" . $this->getID(). "'";
         $result = $GLOBALS['DB']->query($query);
         if ($GLOBALS['DB']->numrows($result) > 0) {
            while($user = $GLOBALS['DB']->fetch_array($result)) {
               $firstname = !empty($user['firstname']) ? $user['firstname'] : '';
               $realname  = !empty($user['realname'])  ? $user['realname']  : '';
               if (!empty($firstname) || !empty($realname)) {
                  $validators[$user['id']] = trim($firstname . ' ' . $realname);
               } else {
                  $validators[$user['id']] = $user['name'];
               }
            }
         } else {
            $validators  = array();
            $subentities = getSonsOf('glpi_entities', $this->fields["entities_id"]);
            $query = 'SELECT u.`id`, u.`name`, u.`realname`, u.`firstname`
                      FROM `glpi_users` u
                      INNER JOIN `glpi_profiles_users` pu ON u.`id` = pu.`users_id`
                      INNER JOIN `glpi_profiles` p ON p.`id` = pu.`profiles_id`
                      WHERE (p.`validate_request` = 1 OR p.`validate_incident` = 1)
                      AND (pu.`entities_id` = ' . $this->fields["entities_id"] . '
                      OR (pu.`is_recursive` = 1 AND pu.entities_id IN (' . implode(',', $subentities). ')))
                      GROUP BY u.`id`
                      ORDER BY u.`name`';
            $result = $GLOBALS['DB']->query($query);
            while($user = $GLOBALS['DB']->fetch_assoc($result)) {
               $firstname = !empty($user['firstname']) ? $user['firstname'] : '';
               $realname  = !empty($user['realname'])  ? $user['realname']  : '';
               if (!empty($firstname) || !empty($realname)) {
                  $validators[$user['id']] = trim($firstname . ' ' . $realname);
               } else {
                  $validators[$user['id']] = $user['name'];
               }
            }
         }

         echo '<div class="form-group required" id="form-validator">';
         echo '<label>' . __('Choose a validator', 'formcreator') . ' <span class="red">*</span></label>';
         Dropdown::showFromArray('formcreator_validator', $validators);
         echo '</div>';
      }

      echo '</div>';

      // Display submit button
      echo '<div class="center">';
      echo '<input type="submit" name="submit_formcreator" class="submit_button" value="' . __('Send') . '" />';
      echo '</div>';

      echo '<input type="hidden" name="formcreator_form" value="' . $item->getID() . '">';
      echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';
      echo '</form>';
   }

   /**
    * Prepare input datas for adding the form
    *
    * @param $input datas used to add the item
    *
    * @return the modified $input array
   **/
   public function prepareInputForAdd($input)
   {
      // Control fields values :
      // - name is required
      if(empty($input['name'])) {
         Session::addMessageAfterRedirect(__('The name cannot be empty!', 'formcreator'), false, ERROR);
         return array();
      }

      // - Category is required
      if(empty($input['plugin_formcreator_categories_id'])) {
         Session::addMessageAfterRedirect(__('The form category cannot be empty!', 'formcreator'), false, ERROR);
         return array();
      }

      // Save form validators
      $query = 'DELETE FROM `glpi_plugin_formcreator_formvalidators` WHERE `forms_id` = "' . $this->getID() . '"';
      $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
      if(($input['validation_required'] == '1') && (!empty($input['_validators']))) {
         foreach ($input['_validators'] as $user) {
            $query = 'INSERT INTO `glpi_plugin_formcreator_formvalidators` SET
                      `forms_id` = "' . $this->getID() . '",
                      `users_id` = "' . $user . '"';
            $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
         }
      }

      return $input;
   }

   /**
    * Prepare input datas for updating the form
    *
    * @param $input datas used to add the item
    *
    * @return the modified $input array
   **/
   public function prepareInputForUpdate($input)
   {
      if (isset($input['access_rights']) || isset($_POST['massiveaction'])) {
         return $input;
      } else {
         return $this->prepareInputForAdd($input);
      }
   }

   public function saveForm($datas)
   {
      $valid = true;

      // Validate form fields
      foreach ($_POST as $key => $value) {
         if (substr($key, 0, 18) == 'formcreator_field_') {
            $question_id = (int) substr($key, 18);
            $question    = new PluginFormcreatorQuestion();
            $question->getFromDB($question_id);
            $className   = $question->fields['fieldtype'] . 'Field';
            $filePath    = dirname(__FILE__) . '/fields/' . $question->fields['fieldtype'] . '-field.class.php';

            if ($question->fields['fieldtype'] == 'float') {
               $value = str_replace(',', '.', $datas['formcreator_field_' . $question_id]);
               $datas['formcreator_field_' . $question_id] = $value;
            }

            if(is_file($filePath)) {
               include_once ($filePath);
               if (class_exists($className)) {
                  if (!$className::isValid($question->fields, $value, $datas)) {
                     $valid = false;
                  }
               }
            } else {
               $valid = false;
            }
         }
      }

      // If not valid back to form
      if (!$valid) {
         $_SESSION['formcreator']['datas'] = $datas;
         Html::back();

      // Save form
      } else {
         $formanswer = new PluginFormcreatorFormanswer();
         $formanswer->saveAnswers($datas);
      }
   }

   /**
    * Database table installation for the item type
    *
    * @param Migration $migration
    * @return boolean True on success
    */
   public static function install(Migration $migration)
   {
      $obj   = new self();
      $table = $obj->getTable();

      // Create default request type
      $query  = "SELECT id FROM `glpi_requesttypes` WHERE `name` LIKE 'Formcreator';";
      $result = $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
      if ($GLOBALS['DB']->numrows($result) > 0) {
         list($requesttype) = $GLOBALS['DB']->fetch_array($result);
      } else {
         $query = "INSERT INTO `glpi_requesttypes` SET `name` = 'Formcreator';";
         $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
         $requesttype = $GLOBALS['DB']->insert_id();
      }

      if (!TableExists($table)) {
         $migration->displayMessage("Installing $table");

         // Create Forms table
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
                     `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                     `entities_id` int(11) NOT NULL DEFAULT '0',
                     `is_recursive` tinyint(1) NOT NULL DEFAULT '0',
                     `access_rights` tinyint(1) NOT NULL DEFAULT '1',
                     `requesttype` int(11) NOT NULL DEFAULT '$requesttype',
                     `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                     `description` varchar(255) COLLATE utf8_unicode_ci,
                     `content` longtext COLLATE utf8_unicode_ci,
                     `plugin_formcreator_categories_id` tinyint(3) UNSIGNED NOT NULL,
                     `is_active` tinyint(1) NOT NULL DEFAULT '0',
                     `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
                     `helpdesk_home` tinyint(1) NOT NULL DEFAULT '0',
                     `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
                     `validation_required` tinyint(1) NOT NULL DEFAULT '0'
                  )
                  ENGINE = MyISAM
                  DEFAULT CHARACTER SET = utf8
                  COLLATE = utf8_unicode_ci;";
         $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
      } else {
         // Migration from previous version
         if (FieldExists($table, 'cat', false)) {
            $query = "ALTER TABLE `$table`
                      CHANGE `cat` `plugin_formcreator_categories_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0';";
            $GLOBALS['DB']->query($query);
         }

         // Migration from previous version
         if (FieldExists($table, 'validation_required', false)) {
            $query = "ALTER TABLE `$table`
                      ADD `validation_required` tinyint(1) NOT NULL DEFAULT '0';";
            $GLOBALS['DB']->query($query);
         }

         // Migration from previous version
         if (!FieldExists($table, 'plugin_formcreator_categories_id', false)) {
            $query = "ALTER TABLE `$table`
                      ADD `plugin_formcreator_categories_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '1';";
            $GLOBALS['DB']->query($query);
         }

         // Migration from previous version
         if (!FieldExists($table, 'requesttype', false)) {
            $query = "ALTER TABLE `$table`
                      ADD `access_rights` tinyint(1) NOT NULL DEFAULT '1',
                      ADD `requesttype` int(11) NOT NULL DEFAULT '$requesttype',
                      ADD `description` varchar(255) COLLATE utf8_unicode_ci,
                      ADD `helpdesk_home` tinyint(1) NOT NULL DEFAULT '0',
                      ADD `is_deleted` tinyint(1) NOT NULL DEFAULT '0';";
            $GLOBALS['DB']->query($query);
         }
      }

      if (!TableExists('glpi_plugin_formcreator_formvalidators')) {
         $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_formcreator_formvalidators` (
                     `forms_id` int(11) NOT NULL,
                     `users_id` int(11) NOT NULL,
                     PRIMARY KEY (`forms_id`, `users_id`)
                  )
                  ENGINE = MyISAM
                  DEFAULT CHARACTER SET = utf8
                  COLLATE = utf8_unicode_ci;";
         $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());
      }

      // Create standard search options
      $query = 'DELETE FROM `glpi_displaypreferences` WHERE `itemtype` = "PluginFormcreatorForm"';
      $GLOBALS['DB']->query($query) or die("error deleting glpi_displaypreferences ". $GLOBALS['DB']->error());

      $query = "INSERT IGNORE INTO `glpi_displaypreferences` (`id`, `itemtype`, `num`, `rank`, `users_id`) VALUES
               (NULL, '" . __CLASS__ . "', 30, 1, 0),
               (NULL, '" . __CLASS__ . "', 3, 2, 0),
               (NULL, '" . __CLASS__ . "', 10, 3, 0),
               (NULL, '" . __CLASS__ . "', 7, 4, 0),
               (NULL, '" . __CLASS__ . "', 8, 5, 0),
               (NULL, '" . __CLASS__ . "', 9, 6, 0);";
      $GLOBALS['DB']->query($query) or die ($GLOBALS['DB']->error());

      return true;
   }

   /**
    * Database table uninstallation for the item type
    *
    * @return boolean True on success
    */
   public static function uninstall()
   {
      $obj = new self();
      $GLOBALS['DB']->query('DROP TABLE IF EXISTS `'.$obj->getTable().'`');

      // Delete logs of the plugin
      $GLOBALS['DB']->query('DELETE FROM `glpi_logs` WHERE itemtype = "' . __CLASS__ . '"');

      return true;
   }
}
