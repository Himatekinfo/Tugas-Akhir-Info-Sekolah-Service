<?php

/**
 * This is the model class for table "t_node".
 *
 * The followings are the available columns in table 't_node':
 * @property string $Id
 * @property double $Latitude
 * @property double $Longitude
 * @property string $Description
 *
 * The followings are the available model relations:
 * @property NeighboringNode[] $neighboringNodes
 * @property NeighboringNode[] $neighboringNodes1
 * @property School[] $schools
 */
class Node extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Node the static model class
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
		return 't_node';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Latitude, Longitude', 'required'),
			array('Latitude, Longitude', 'numerical'),
			array('Description', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, Latitude, Longitude, Description', 'safe', 'on'=>'search'),
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
			'neighboringNodes' => array(self::HAS_MANY, 'NeighboringNode', 'NodeId'),
			'neighboringNodes1' => array(self::HAS_MANY, 'NeighboringNode', 'NeighboringNodeId'),
			'schools' => array(self::HAS_MANY, 'School', 'NodeId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'Latitude' => 'Latitude',
			'Longitude' => 'Longitude',
			'Description' => 'Description',
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
		$criteria->compare('Latitude',$this->Latitude);
		$criteria->compare('Longitude',$this->Longitude);
		$criteria->compare('Description',$this->Description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}