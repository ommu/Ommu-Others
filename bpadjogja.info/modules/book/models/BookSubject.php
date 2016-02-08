<?php
/**
 * BookSubject * @author Putra Sudaryanto <putra.sudaryanto@gmail.com>
 * @copyright Copyright (c) 2014 Ommu Platform (ommu.co)
 * @link http://company.ommu.co
 * @contact (+62)856-299-4114
 *
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 *
 * --------------------------------------------------------------------------------------
 *
 * This is the model class for table "ommu_book_subject".
 *
 * The followings are the available columns in table 'ommu_book_subject':
 * @property string $id
 * @property string $book_id
 * @property string $subject_id
 * @property string $creation_date
 * @property string $creation_id
 *
 * The followings are the available model relations:
 * @property OmmuBookMasterSubjects $subject
 * @property OmmuBookMasters $book
 */
class BookSubject extends CActiveRecord
{
	public $defaultColumns = array();
	public $subject_input;
	
	// Variable Search
	public $book_search;
	public $subject_search;
	public $creation_search;	

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BookSubject the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ommu_book_subject';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id, subject_id', 'required'),
			array('book_id, subject_id, creation_id', 'length', 'max'=>11),
			array('
				subject_input', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, book_id, subject_id, creation_date, creation_id,
				book_search, subject_search, creation_search', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'book_relation' => array(self::BELONGS_TO, 'BookMasters', 'book_id'),
			'subject_relation' => array(self::BELONGS_TO, 'BookMasterSubjects', 'subject_id'),
			'creation_relation' => array(self::BELONGS_TO, 'Users', 'creation_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'book_id' => 'Book',
			'subject_id' => 'Subject',
			'creation_date' => 'Creation Date',
			'creation_id' => 'Creation',
			'subject_input' => 'Subject',
			'book_search' => 'Book',
			'subject_search' => 'Subject',
			'creation_search' => 'Creation',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id,true);
		if(isset($_GET['book'])) {
			$criteria->compare('t.book_id',$_GET['book']);
		} else {
			$criteria->compare('t.book_id',$this->book_id);
		}
		if(isset($_GET['subject'])) {
			$criteria->compare('t.subject_id',$_GET['subject']);
		} else {
			$criteria->compare('t.subject_id',$this->subject_id);
		}
		if($this->creation_date != null && !in_array($this->creation_date, array('0000-00-00 00:00:00', '0000-00-00')))
			$criteria->compare('date(t.creation_date)',date('Y-m-d', strtotime($this->creation_date)));
		$criteria->compare('t.creation_id',$this->creation_id,true);
		
		// Custom Search
		$criteria->with = array(
			'book_relation' => array(
				'alias'=>'book_relation',
				'select'=>'title'
			),
			'subject_relation' => array(
				'alias'=>'subject_relation',
				'select'=>'subject'
			),
			'creation_relation' => array(
				'alias'=>'creation_relation',
				'select'=>'displayname'
			),
		);
		$criteria->compare('book_relation.title',strtolower($this->book_search), true);
		$criteria->compare('subject_relation.subject',strtolower($this->subject_search), true);
		$criteria->compare('creation_relation.displayname',strtolower($this->creation_search), true);

		if(!isset($_GET['BookSubject_sort']))
			$criteria->order = 'id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>30,
			),
		));
	}


	/**
	 * Get column for CGrid View
	 */
	public function getGridColumn($columns=null) {
		if($columns !== null) {
			foreach($columns as $val) {
				/*
				if(trim($val) == 'enabled') {
					$this->defaultColumns[] = array(
						'name'  => 'enabled',
						'value' => '$data->enabled == 1? "Ya": "Tidak"',
					);
				}
				*/
				$this->defaultColumns[] = $val;
			}
		} else {
			//$this->defaultColumns[] = 'id';
			$this->defaultColumns[] = 'book_id';
			$this->defaultColumns[] = 'subject_id';
			$this->defaultColumns[] = 'creation_date';
			$this->defaultColumns[] = 'creation_id';
		}

		return $this->defaultColumns;
	}

	/**
	 * Set default columns to display
	 */
	protected function afterConstruct() {
		if(count($this->defaultColumns) == 0) {
			$this->defaultColumns[] = array(
				'header' => 'No',
				'value' => '$this->grid->dataProvider->pagination->currentPage*$this->grid->dataProvider->pagination->pageSize + $row+1'
			);
			$this->defaultColumns[] = array(
				'name' => 'book_search',
				'value' => '$data->book_relation->title',
			);
			$this->defaultColumns[] = array(
				'name' => 'subject_search',
				'value' => '$data->subject_relation->subject',
			);
			$this->defaultColumns[] = array(
				'name' => 'creation_search',
				'value' => '$data->creation_relation->displayname',
			);
			$this->defaultColumns[] = array(
				'name' => 'creation_date',
				'value' => 'Utility::dateFormat($data->creation_date)',
				'htmlOptions' => array(
					'class' => 'center',
				),
				'filter' => Yii::app()->controller->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model'=>$this,
					'attribute'=>'creation_date',
					'language' => 'ja',
					'i18nScriptFile' => 'jquery.ui.datepicker-en.js',
					//'mode'=>'datetime',
					'htmlOptions' => array(
						'id' => 'creation_date_filter',
					),
					'options'=>array(
						'showOn' => 'focus',
						'dateFormat' => 'dd-mm-yy',
						'showOtherMonths' => true,
						'selectOtherMonths' => true,
						'changeMonth' => true,
						'changeYear' => true,
						'showButtonPanel' => true,
					),
				), true),
			);
		}
		parent::afterConstruct();
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
		if($column != null) {
			$model = self::model()->findByPk($id,array(
				'select' => $column
			));
			return $model->$column;
			
		} else {
			$model = self::model()->findByPk($id);
			return $model;			
		}
	}

	/**
	 * get book subject
	 */
	public static function getSubject($id) {
		$model = self::model()->findAll(array(
			'condition' => 'book_id = :id',
			'params' => array(
				':id' => $id,
			),
			'order' => 'id ASC',
		));
		
		$subject = '';
		if($model != null) {
			foreach($model as $val) {
				$subject .= ','.$val->subject_relation->subject;
			}
		}
		
		return $subject;
	}

	/**
	 * before validate attributes
	 */
	protected function beforeValidate() {
		if(parent::beforeValidate()) {
			if($this->isNewRecord) {
				$this->creation_id = Yii::app()->user->id;			
			}
		}
		return true;
	}
	
	/**
	 * before save attributes
	 */
	protected function beforeSave() {
		if(parent::beforeSave()) {
			if($this->isNewRecord) {
				if($this->subject_id == 0) {
					$subject = BookMasterSubjects::model()->find(array(
						'select' => 'subject_id, subject',
						'condition' => 'subject = :subject',
						'params' => array(
							':subject' => strtolower($this->subject_input),
						),
					));
					if($subject != null) {
						$this->subject_id = $subject->subject_id;
					} else {
						$data = new BookMasterSubjects;
						$data->subject = $this->subject_input;
						if($data->save()) {
							$this->subject_id = $data->subject_id;
						}
					}					
				}
			}
		}
		return true;
	}

}