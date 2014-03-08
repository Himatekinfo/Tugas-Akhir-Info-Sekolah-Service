<?php

/**
 * This is the model class for table "t_neighboring_node".
 *
 * The followings are the available columns in table 't_neighboring_node':
 * @property string $Id
 * @property string $NodeId
 * @property string $NeighboringNodeId
 * @property double $Distance
 *
 * The followings are the available model relations:
 * @property Node $node
 * @property Node $neighboringNode
 */
class NeighboringNode extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return NeighboringNode the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 't_neighboring_node';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('NodeId, NeighboringNodeId, Distance', 'required'),
            array('Distance', 'numerical'),
            array('NodeId, NeighboringNodeId', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('Id, NodeId, NeighboringNodeId, Distance', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'node' => array(self::BELONGS_TO, 'Node', 'NodeId'),
            'neighboringNode' => array(self::BELONGS_TO, 'Node', 'NeighboringNodeId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'Id' => 'ID',
            'NodeId' => 'Node',
            'NeighboringNodeId' => 'Neighboring Node',
            'Distance' => 'Distance',
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
        $criteria->compare('NodeId', $this->NodeId, true);
        $criteria->compare('NeighboringNodeId', $this->NeighboringNodeId, true);
        $criteria->compare('Distance', $this->Distance);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function createIfNotExist($nodeId, $neighboringNodeId, $distance = -1) {
        $firstNode = Node::model()->findByPk(min($nodeId, $neighboringNodeId));
        $secondNode = Node::model()->findByPk(max($nodeId, $neighboringNodeId));

        $model = NeighboringNode::model()->find("NodeId=$firstNode->Id AND NeighboringNodeId=$secondNode->Id");
        if ($model === null) {
            $model = new NeighboringNode();
            $model->NodeId = $firstNode->Id;
            $model->NeighboringNodeId = $secondNode->Id;
            $model->Distance = $distance;
            if ($distance == -1)
                $model->Distance = DistanceAlgorithm::DistanceBetweenPlaces($firstNode->Longitude, $firstNode->Latitude, $secondNode->Longitude, $secondNode->Latitude) * 1000;
            $model->save();
        }

        return $model;
    }

}
