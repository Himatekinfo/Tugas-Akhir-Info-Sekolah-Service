<?php

/**
 * This is the model class for table "t_school_cost_details".
 *
 * The followings are the available columns in table 't_school_cost_details':
 * @property string $Id
 * @property string $SchoolId
 * @property string $Description
 * @property string $Price
 *
 * The followings are the available model relations:
 * @property School $school
 */
class SchoolCostDetails extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SchoolCostDetails the static model class
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
		return 't_school_cost_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SchoolId, Description', 'required'),
			array('SchoolId, Price', 'length', 'max'=>20),
			array('Description', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, SchoolId, Description, Price', 'safe', 'on'=>'search'),
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
			'school' => array(self::BELONGS_TO, 'School', 'SchoolId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'SchoolId' => 'School',
			'Description' => 'Description',
			'Price' => 'Price',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id,true);
		$criteria->compare('SchoolId',$this->SchoolId,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('Price',$this->Price,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}