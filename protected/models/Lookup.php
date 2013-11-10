<?php

/**
 * This is the model class for table "t_lookup".
 *
 * The followings are the available columns in table 't_lookup':
 * @property string $Id
 * @property string $LookupName
 * @property string $LookupValue
 * @property string $LookupGroup
 *
 * The followings are the available model relations:
 * @property School[] $schools
 */
class Lookup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Lookup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function scopes() {
            return array(
                "schoolCategory"=>array(
                    "select"=>"Id,LookupName,LookupValue",
                    "condition"=>"LookupGroup='SCHOOL_CATEGORY'",
                )
            );
        }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_lookup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('LookupName, LookupValue', 'required'),
			array('LookupName, LookupGroup', 'length', 'max'=>20),
			array('LookupValue', 'length', 'max'=>2000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, LookupName, LookupValue, LookupGroup', 'safe', 'on'=>'search'),
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
			'schools' => array(self::HAS_MANY, 'School', 'CategoryId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'LookupName' => 'Lookup Name',
			'LookupValue' => 'Lookup Value',
			'LookupGroup' => 'Lookup Group',
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
		$criteria->compare('LookupName',$this->LookupName,true);
		$criteria->compare('LookupValue',$this->LookupValue,true);
		$criteria->compare('LookupGroup',$this->LookupGroup,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}