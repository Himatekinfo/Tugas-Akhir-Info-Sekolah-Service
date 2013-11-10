<?php
/**
 * EMultiRatingView v.0.3beta
 * This extension is to create a view that is suitable to select multiple
 * ratings between a list of options. It was originally used to rate options
 * in an Analytical Hierarchy Process (AHP). But it created with general use
 * in mind.
 * 
 * @author Syakur Rahman bS.xx7_37@yahoo.com
 * @license New BSD License
 * @example
 * <?php
	$this->widget('application.extensions.EMultiRatingView', array(
           'Options'=>$data_ahp,
		   'Debug'=>true,
		   'Template'=> "{emrview}",
		   'WithFormWrapper'=>false,
		   'FormId'=>'ahp-form',
		));
	?>
 * 
 */
class EMultiRatingView extends CWidget {
	/**
	 * Defines available option.
	 * @var array	This should be a multidimensional array with two values,
	 *				the first value should be the left option while the second
	 *				value should be the right option. Eg:
	 *				array([0]=>array("opt1", "opt2", "opt3"), [1]=>array("opt4", "opt5", "opt6"))
	 */
	public $Options = array();

	/**
	 * Defines the score range. By default this is 9 which means from -9 to 9.
	 * @var int Defaults to 9.
	 */
	public $ScoreRange = 9;

	/**
	 * Defines the form action if view is wrapped in a form.
	 * @var string Defaults to empty.
	 */
	public $TargetRoute = "";

	/**
	 * Defines whether the extension should run in debug mode or not. When it is
	 * in debug mode, the value would automatically be populated.
	 * @var boolean Defaults to false.
	 */
	public $Debug = false;

	/**
	 * Defines the template for the view to be generated.
	 * @var string Defaults to '{emrview} {submit}'
	 */
	public $Template = "{emrview} {submit}";

	/**
	 * Defines the template for the view for each row to be generated
	 * @var string Defaults to '{emrrow}'
	 */
	public $TemplateRow = "{emrrow}";

	/**
	 * Defines whether the view should be generated within a form.
	 * @var boolean Defaults to true.
	 */
	public $WithFormWrapper = true;

	/**
	 * Defines the id that would be used for the form.
	 * @var string Defaults to 'emrviewform'
	 */
	public $FormId = "emrviewform";

	/**
	 * Defines the id that would be used to submit the data.
	 * @var string Defaults to 'emrviewdata'
	 */
	public $ValueId = "emrviewdata";

	/**
	 * Defines the message that will be shown in case not every option is selected.
	 * @var string
	 */
	public $IncompleteErrorMessage = "Every option must have a selection.";

	public $UseClassicView = false;

	public $DistanceBetweenOption = 10;

	/**
	 * Container to mark the middle index. This would be the 0 value of the score.
	 * @var integer
	 */
	private $_midIndex = 0;

	/**
	 * Container for unique id. This would be a random number.
	 * @var integer
	 */
	private $_uniqueId = 0;

	/**
	 * Constructor magic method.
	 */
	public function __construct() {
		$this->_uniqueId = $this->_genRandomNumber();
	}

	/**
	 * Generate a random number.
	 * @return integer
	 */
	private function _genRandomNumber() {
		return substr(md5(rand(100, 999)), 4, 8);
	}

	/**
	 * Registers css.
	 */
	private function _registerCss() {
		$cc = Yii::app()->clientScript;
		$cc->registerCss('emrview' . $this->_uniqueId, "
			#emrView$this->_uniqueId, #emrView$this->_uniqueId tr td { border: thin solid black; }
			#emrView$this->_uniqueId #option:hover { 
				background: lightblue;
				cursor: pointer;
			}
			#emrView$this->_uniqueId .selected { 
				background: darkblue;
				cursor: pointer;
			}
			#emrView$this->_uniqueId .selectedClassic {
				background: darkblue;
				cursor: pointer;
			}
			"
		);
	}

	/**
	 * Registers java scripts and dependencies.
	 */
	private function _registerScript() {
		Yii::app()->clientScript->registerCoreScript('jquery');
		if($this->UseClassicView)
		{
			Yii::app()->clientScript->registerScript('emrviewcore'  . $this->_uniqueId, '
				$(document).ready(function() {
					// create array
					emrviewdata'.$this->_uniqueId.' = new Array();

					$("#emrView'.$this->_uniqueId.' select:odd").each(function() {
						emrviewdata'.$this->_uniqueId.'.push(-1);
					});

					// hook the function
					$("#emrView'.$this->_uniqueId.' select").change(function() {
					  var pos = this.name.split("_")[2];
					  var row = this.name.split("_")[3];
					  var partnerSelect = "#emrView'.$this->_uniqueId.' input[id$=\"_" + (pos=="l"?"x1":"x2") + "_" + row + "\"]";

					  $(partnerSelect).click();
					});
					$("#emrView'.$this->_uniqueId.' input[id*=\'_x1_\']").click(function() {
					  var pos = this.name.split("_")[3];
					  var row = this.name.split("_")[4];
					  var current = "#emrView'.$this->_uniqueId.' select[id$=\"_l_" + row + "\"]";
					  var partnerSelect = "#emrView'.$this->_uniqueId.' select[id$=\"_r_" + row + "\"]";
					  var partnerSelect2 = "#emrView'.$this->_uniqueId.' input[id$=\"_x2_" + row + "\"]";
					  var val = (Number($(current).val()) + 1) * -1;

					  if(val==-1) val=1;

					  $(current).attr("disabled", false);
					  $(partnerSelect).attr("disabled", true);
					  $(partnerSelect).attr("value", 0);
					  $(partnerSelect2).attr("checked", false);

					  emrviewdata'.$this->_uniqueId.'[row] = val;
					  $("input[name=\''.$this->ValueId.'\']").val(emrviewdata'.$this->_uniqueId.');
					});


					$("#emrView'.$this->_uniqueId.' input[id*=\'_x2_\']").click(function() {
						  var pos = this.name.split("_")[3];
						  var row = this.name.split("_")[4];
						  var current = "#emrView'.$this->_uniqueId.' select[id$=\"_r_" + row + "\"]";
						  var partnerSelect = "#emrView'.$this->_uniqueId.' select[id$=\"_l_" + row + "\"]";
						  var partnerSelect2 = "#emrView'.$this->_uniqueId.' input[id$=\"_x1_" + row + "\"]";
					      var val = Number($(current).val()) + 1;

						  $(current).attr("disabled", false);
						  $(partnerSelect).attr("disabled", true);
						  $(partnerSelect).attr("value", 0);
						  $(partnerSelect2).attr("checked", false);

						  emrviewdata'.$this->_uniqueId.'[row] = val;
						  $("input[name=\''.$this->ValueId.'\']").val(emrviewdata'.$this->_uniqueId.');
					})

					// Prevents sending when not complete
					$("form#'.$this->FormId.'").submit(function(e) {
						if($(emrviewdata'.$this->_uniqueId.').length>0)
						{
							$(emrviewdata'.$this->_uniqueId.').each(function() {
								if(this == -1)
								{
									alert("' . $this->IncompleteErrorMessage . '");
									e.preventDefault();
									return false;
								}
							})
						} else {
							alert("' . $this->IncompleteErrorMessage . '");
							e.preventDefault();
							return false;
						}
					});
				});
			');
		} else {
			Yii::app()->clientScript->registerScript('emrviewcore'  . $this->_uniqueId, '
				$(document).ready(function() {
					// create array
					emrviewdata'.$this->_uniqueId.' = new Array();
					$("#emrView'.$this->_uniqueId.' #option:nth-child(' . $this->_midIndex . ')").each(function() {
						emrviewdata'.$this->_uniqueId.'.push(-1);
					});

					// hook the function
					$("#emrView'.$this->_uniqueId.' #option").click(function() {
					  var col = $(this).parent().children().index($(this)) - 1;
					  var row = $(this).parent().parent().children().index($(this).parent()) - 2;
					  var val = $(this).parent().parent().children("tr:nth-child(1)").children("th:eq(" + (col + 1) + ")").html();
					  if($(this).hasClass("selected")) {
						$(this).removeClass("selected");
						emrviewdata'.$this->_uniqueId.'[row] = -1;
					  } else {
						if(emrviewdata'.$this->_uniqueId.'[row] == -1) {
							$(this).addClass("selected");
							emrviewdata'.$this->_uniqueId.'[row] = val;
						}
					  }
					  $("input[name=\''.$this->ValueId.'\']").val(emrviewdata'.$this->_uniqueId.');
					})

					// Prevents sending when not complete
					$("form#'.$this->FormId.'").submit(function(e) {
						$(emrviewdata'.$this->_uniqueId.').each(function() {
							if(this == -1)
							{
								alert("' . $this->IncompleteErrorMessage . '");
								e.preventDefault();
								return false;
							}
						})
					});

				});
			');
		}
		if($this->Debug && !$this->UseClassicView)
		{
			if($this->UseClassicView)
			{
				Yii::app()->clientScript->registerScript('emrviewdebug'  . $this->_uniqueId, '
						$(document).ready(function() {
							// randomize value
							var valid = false;
							while(!valid)
							{
								$("#emrView'.$this->_uniqueId.' #option:nth-child(' . $this->_midIndex . ')").each(function() {
									var index = Math.floor(Math.random() * 20);
									$(this).parent().children("#option:nth-child(" + index +")").click();
								});
								valid = true;
								$(emrviewdata'.$this->_uniqueId.').each(function() {
									if(this == -1)
									{
										valid = false;
										return false;
									}
								})
							}
						});
					');
			} else {
				Yii::app()->clientScript->registerScript('emrviewdebug'  . $this->_uniqueId, '
						$(document).ready(function() {
							// randomize value
							var valid = false;
							while(!valid)
							{
								$("#emrView'.$this->_uniqueId.' #option:nth-child(' . $this->_midIndex . ')").each(function() {
									var index = Math.floor(Math.random() * 20);
									$(this).parent().children("#option:nth-child(" + index +")").click();
								});
								valid = true;
								$(emrviewdata'.$this->_uniqueId.').each(function() {
									if(this == -1)
									{
										valid = false;
										return false;
									}
								})
							}

						});
					');
			}
		}
	}

	/**
	 * Creates the view.
	 * @return string 
	 */
	private function _generateTable() {
		$out = "<table id='emrView$this->_uniqueId'>";
		$i = 0;

		// Value
		$out .= "<tr style='visibility:hidden;height:0px;font-size:0;'>";
		$out .= "<th style='padding:0;margin:0;height:0;'>First Value</th>";
		for ($j = (-$this->ScoreRange); $j < $this->ScoreRange + 1; $j++) {
			if ($j <> 0 && $j <> -1) {
				$out .= "<th style='padding:0;margin:0;height:0;'>" . $j . "</th>";
			}
		}
		$out .= "<th style='padding:0;margin:0;height:0;'>Second Value</th>";
		$out .= "</tr>";
		//header
		$out .= "<tr>";
		$out .= "<td>First Value</td>";
		for ($j = (-$this->ScoreRange); $j < $this->ScoreRange + 1; $j++) {
			if ($j <> 0 && $j <> -1) {
				$out .= "<td>" . ($j<0? -$j : $j) . "</td>";
			}
		}
		$out .= "<td>Second Value</td>";
		$out .= "</tr>";
		//body
		for ($j = 0; $j < count($this->Options[0]); $j++) {
			$out .= "<tr>";
			$out .= "<td>" . $this->Options[0][$j] . "</td>";
			for ($k = (-$this->ScoreRange); $k < $this->ScoreRange + 1; $k++) {
				if ($k <> 0 && $k <> -1) {
					$out .= "<td id='option'>&nbsp;</td>";
				}
			}
			$out .= "<td>" . $this->Options[1][$j] . "</td>";
			$out .= "</tr>";
		}
		$out .= "</table>";

		return $out;
	}

	private function _generateClassicTable() {
		$arrValue = array(1,2,3,4,5,6,7,8,9);

		// Predict width
		$spanWidth = 0;
		for ($j = 0; $j < count($this->Options[0]); $j++) {
			if($spanWidth < strlen($this->Options[0][$j]))
					$spanWidth = strlen($this->Options[0][$j]);
			if($spanWidth < strlen($this->Options[1][$j]))
					$spanWidth = strlen($this->Options[1][$j]);
		}
		$spanWidth *= 7;

		$out = "<div id='emrView$this->_uniqueId'>";

		//body
		for ($j = 0; $j < count($this->Options[0]); $j++) {
			$tmp = "";
			$tmp .= "<div>";
			$tmp .= "<span>" . CHtml::radioButton("input_$this->_uniqueId" . "_" . $this->Options[0][$j] . "_x1_" . $j) . "</span>";
			$tmp .= "<span style='width:". $spanWidth . "px; display:inline-block;'>" . $this->Options[0][$j] . "</span>";
			$tmp .= "<span>" . CHtml::dropDownList("option_" . $this->Options[0][$j] . "_l_" . $j, 0, $arrValue) . "</span>";
			$tmp .= "<span style='padding-left:". $this->DistanceBetweenOption . "px; display:inline-block;'>" . CHtml::radioButton("input_$this->_uniqueId" . "_" . $this->Options[0][$j] . "_x2_" . $j) . "</span>";
			$tmp .= "<span style='width:". $spanWidth . "px; display:inline-block;'>" . $this->Options[1][$j] . "</span>";
			$tmp .= "<span>" . CHtml::dropDownList("option_" . $this->Options[1][$j] . "_r_" . $j, 0, $arrValue) . "</span>";
			$tmp .= "</div>";

			$result = $this->TemplateRow;
			$result = str_replace("{emrrow}", $tmp, $result);
			$result = str_replace("{option1}", $this->Options[0][$j], $result);
			$result = str_replace("{option2}", $this->Options[1][$j], $result);

			$out .= $result;
		}
		$out .= "</div>";

		return $out;
	}

	/**
	 * Generates the whole view.
	 */
	private function _generateHTML() {
		$startForm = CHtml::form($this->TargetRoute, "post", array("id" => $this->FormId));
		if($this->UseClassicView)
		{
			$emrView = $this->_generateClassicTable();
		} else
		{
			$emrView = $this->_generateTable();
		}
		$hiddenField = CHtml::hiddenField($this->ValueId);
		$button = CHtml::submitButton();
		$closeForm = CHtml::endForm();
		
		if($this->WithFormWrapper)
		{
			$template = $startForm . $hiddenField . str_replace(array("{emrview}", "{submit}"), array($emrView, $button), $this->Template) . $closeForm;
		} else {
			$template = $hiddenField . str_replace(array("{emrview}", "{submit}"), array($emrView, $button), $this->Template);
		}
		echo $template;
	}

	/**
	 * Executes the widget.
	 */
	public function run() {
		// Prepare
		$this->_midIndex = $this->ScoreRange - 1;

		// Validate
		if (!is_array($this->Options)) {
			throw new CException("Options is supposed to be an array.");
		}
		if (count($this->Options) <= 1) {
			throw new CException("Options count can't be less than 1.");
		}
		if ($this->ScoreRange <= 1) {
			throw new CException("ScoreRange can't be less than 1.");
		}

		$this->_registerScript();
		$this->_registerCss();
		$this->_generateHTML();
	}
}

?>
