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
class SchoolCostDetails extends CActiveRecord {

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
        return 't_school_cost_details';
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'category' => array(self::BELONGS_TO, 'School', 'SchoolId'),
        );
    }

}

