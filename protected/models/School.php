<?php

/**
 * This is the model class for table "t_school".
 *
 * The followings are the available columns in table 't_school':
 * @property string $Id
 * @property string $Name
 * @property string $Cost
 * @property string $Accreditation
 * @property string $Website
 * @property string $CategoryId
 * @property string $GoogleId
 * @property double $Latitude
 * @property double $Longitude
 *
 * The followings are the available model relations:
 * @property Lookup $category
 */
class School extends CActiveRecord {

    public function getDistance() {
        return 0;
    }

    public function afterFind() {
        $this->getMetaData()->columns = array_merge($this->getMetaData()->columns, array("CostDetails" => ""));
        $this->getMetaData()->columns = array_merge($this->getMetaData()->columns, array("Distance" => 0));

        $this->Distance = 0;

        $model = SchoolCostDetails::model()->findAll("SchoolId=" . $this->Id);
        foreach ($model as $value) {
            $this->CostDetails .= $value->Description . ", ";
        }

        $this->CostDetails = substr($this->CostDetails, 0, -2);
        return true;
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return School the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 't_school';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('Name', 'required'),
            array('Latitude, Longitude', 'numerical'),
            array('Name', 'length', 'max' => 150),
            array('Accreditation, CategoryId', 'length', 'max' => 20),
            array('Website', 'length', 'max' => 100),
            array('GoogleId', 'length', 'max' => 41),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('Id, Name, Accreditation, Website, CategoryId, GoogleId, Latitude, Longitude', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'category' => array(self::BELONGS_TO, 'Lookup', 'CategoryId'),
            'schoolCost' => array(self::HAS_MANY, 'SchoolCostDetails', 'SchoolId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Id' => 'ID',
            'Name' => 'Name',
            'Accreditation' => 'Accreditation',
            'Website' => 'Website',
            'CategoryId' => 'Category',
            'GoogleId' => 'Google ID',
            'Latitude' => 'Latitude',
            'Longitude' => 'Longitude',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('Id', $this->Id, true);
        $criteria->compare('Name', $this->Name, true);
        $criteria->compare('Accreditation', $this->Accreditation, true);
        $criteria->compare('Website', $this->Website, true);
        $criteria->compare('CategoryId', $this->CategoryId, true);
        $criteria->compare('GoogleId', $this->GoogleId, true);
        $criteria->compare('Latitude', $this->Latitude);
        $criteria->compare('Longitude', $this->Longitude);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}

