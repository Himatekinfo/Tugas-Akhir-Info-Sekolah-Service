<?php

/**
 * This is the model class for table "t_distance_cache".
 *
 * The followings are the available columns in table 't_distance_cache':
 * @property string $Id
 * @property string $StartNodeId
 * @property string $EndNodeId
 * @property double $Distance
 * @property string $EncodedPolyline
 */
class DistanceCache extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DistanceCache the static model class
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
		return 't_distance_cache';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('StartNodeId, EndNodeId, Distance, EncodedPolyline', 'required'),
			array('Distance', 'numerical'),
			array('StartNodeId, EndNodeId', 'length', 'max'=>20),
			array('EncodedPolyline', 'length', 'max'=>8000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, StartNodeId, EndNodeId, Distance, EncodedPolyline', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'StartNodeId' => 'Start Node',
			'EndNodeId' => 'End Node',
			'Distance' => 'Distance',
			'EncodedPolyline' => 'Encoded Polyline',
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
		$criteria->compare('StartNodeId',$this->StartNodeId,true);
		$criteria->compare('EndNodeId',$this->EndNodeId,true);
		$criteria->compare('Distance',$this->Distance);
		$criteria->compare('EncodedPolyline',$this->EncodedPolyline,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}