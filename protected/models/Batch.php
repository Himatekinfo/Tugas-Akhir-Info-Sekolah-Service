<?php

/**
 * This is the model class for table "t_batch".
 *
 * The followings are the available columns in table 't_batch':
 * @property string $Id
 * @property string $SchoolId
 * @property string $LastModified
 *
 * The followings are the available model relations:
 * @property School $school
 */
class Batch extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Batch the static model class
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
		return 't_batch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SchoolId', 'required'),
			array('SchoolId', 'length', 'max'=>20),
			array('LastModified', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, SchoolId, LastModified', 'safe', 'on'=>'search'),
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
			'LastModified' => 'Last Modified',
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
		$criteria->compare('LastModified',$this->LastModified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}