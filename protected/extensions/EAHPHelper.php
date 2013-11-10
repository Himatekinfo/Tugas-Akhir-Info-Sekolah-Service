<?php

class EAHPHelper {

	public static $IR = array(
		2 => 0.00,
		3 => 0.58,
		4 => 0.90,
		5 => 1.12,
		6 => 1.24,
		7 => 1.32,
		8 => 1.41
	);

	private $_matrix;

	private $_options;

	private $_nRate;

	public static function LoadData($optionList, $selectionData) {
		$out = new EAHPHelper($optionList);
		$out->setData($selectionData);

		return $out;
	}

	public static function GetRecomendationScore($criteria, $options, $alternative) {
		$out = null;

		foreach ($alternative as $value) {
			$out[$value] = 0;
		}

		$eigenCriteria = $criteria->getEigenVector(true);
		foreach ($options as $criteria => $eigenAlternative) {
			$tmp = $alternative;
			foreach ($eigenAlternative->getEigenVector() as $eigenValue) {
				print("<hr />");
				print("$criteria<br />");
				print_r($eigenValue);
				print("<hr />");
				$out[array_shift($tmp)] += $eigenValue * $eigenCriteria[$criteria];
			}
		}

		return $out;
	}

	public function __construct($optionList, $nRate = 9) {
		$n = count($optionList);
		$totalRow = ((($n * $n) + $n) / 2) - 1;
		$this->_options = $optionList;
		$this->_nRate = $nRate;
	}

	public function getOptionArray($asDouble=true) {
		$out = array();
		if ($asDouble) {
			$i = 1;
			foreach (array_slice($this->_options, 0, count($this->_options) - 1) as $value) {
				$data2 = array_slice($this->_options, $i++, count($this->_options));
				foreach ($data2 as $value2) {
					$out[0][] = $value;
					$out[1][] = $value2;
				}
			}
		} else {
			foreach ($this->_options as $value) {
				$out[] = $value;
			}
		}

		return $out;
	}

	public function setData($data) {
		if (count($data) <> $this->_getTotalRow()) {
			throw new CException("Total data(" . count($data) . ":" . $this->_getTotalRow() . ") is invalid.");
		}

		$this->_matrix = new Matrix($this->_getOptionCount(), $this->_getOptionCount());
		for ($i = 0; $i < $this->_matrix->Rows; $i++) {
			for ($j = 0; $j < $this->_matrix->Cols; $j++) {
				if ($i == $j) {
					$this->_matrix->SetValue($i, $j, 1);
				} elseif ($j > $i) {
					$val1 = reset($data) < 0 ? 1 / reset($data) * -1 : reset($data);

					$this->_matrix->SetValue($i, $j, 1 / $val1);
					$this->_matrix->SetValue($j, $i, $val1);
					array_shift($data);
				}
			}
		}

//		$this->_matrix->View();
	}

	public function getEigenVector($withOptions=false, $asSimple=false) {
		$out = array();
		$tmp = clone $this->_matrix;
		if ($asSimple) {
//			$tmp->View();
			$tmp->Normalize();
//			$tmp->View();

			$totals = $tmp->GetTotal(false);
//			print_r($totals);
		} else {
//		$tmp->View();
			$tmp->Normalize();
//		$tmp->View();
			$tmp->Square();
//		$tmp->View();

			$totals = $tmp->GetTotal(false);
//		print_r($totals);
		}
		for ($i = 0; $i < count($totals); $i++) {
			$out[] = $totals[$i] / array_sum($totals);
		}
		if ($withOptions) {
			$out = array_combine($this->getOptionArray(false), $out);
		}
//		print_r($out);

		return $out;
	}

	public function getCI() {
		$ev = $this->getEigenVector();

		$vjt = clone $this->_matrix;
		$vjt->Normalize();
		$vjt->Square();
		
		$vjt->ScalarMultiply(Matrix::FromArray($ev));
		$totals = $vjt->GetTotal(false);
//		for ($i = 0; $i < count($totals); $i++) {
//			$totals[$i] /= $ev[$i];
//		}

		return (( array_sum($totals) / count($totals)) - count($totals)) / (count($totals) - 1);
	}

	public function getRC() {
		return EAHPHelper::$IR[count($this->_options)];
	}

	public function getCR() {
		$out = 0;
		if($this->getRC() != 0)
		{
			$out = $this->getCI() / $this->getRC();
		}
		return $out;
	}

	private function _getTotalRow() {
		$n = count($this->_options);
		$totalRow = ((($n * $n) - $n) / 2);

		return $totalRow;
	}

	private function _getOptionCount() {
		return count($this->_options);
	}

}

class Matrix {

	private $_matrix = array();
	private $_rows = 0;
	private $_cols = 0;

	public function __get($name) {
		if ($name == "Data") {
			return $this->_matrix;
		} elseif ($name == "Rows" || $name == "Cols") {
			$out = "";
			eval("\$out = \$this->_" . strtolower($name) . ";");
			return $out;
		}

		throw new CException("$name is an invalid property.");
	}

	public function __construct($rows, $cols) {
		$this->_validateIndex($rows, $cols, false);

		$this->_rows = $rows;
		$this->_cols = $cols;
	}

	public static function FromArray($data) {
		$data = array_values($data);
		if (!is_array($data)) {
			throw new CException("Invalid data. Only array data type is allowed.");
		}

		// Check and validate multidimensional array
		array_walk($data, function(&$value) {
					if (is_array($value)) {
						$value = array_values($value);
					}
				});

		for ($i = 0; $i < count($data) - 1; $i++) {
			// check whether all data is array or not
			if (is_array($data[$i]) !== is_array($data[$i + 1])) {
				throw new CException("Invalid data. Check the consistency of data.");
			}

			// if array, check whether all sub-arrays have consistent count of data
			if (is_array($data[$i])) {
				if (count($data[$i]) != count($data[$i + 1])) {
					throw new CException("Invalid data. Check the consistency of data.");
				}
			}
		}

		// as 2 dimensional array
		$asMultiDimension = is_array($data[0]);
		$matrix = new Matrix(count($data), $asMultiDimension ? count($data[0]) : 1);
		if ($asMultiDimension) {
			for ($i = 0; $i < count($data); $i++) {
				for ($j = 0; $j < count($data[0]); $j++) {
					$matrix->SetValue($i, $j, $data[$i][$j]);
				}
			}
		} else {
			for ($i = 0; $i < count($data); $i++) {
				$matrix->SetValue($i, 0, $data[$i]);
			}
		}

		return $matrix;
	}

	public function SetValue($row, $col, $value = 0) {
		$this->_validateIndex($row, $col);
		$this->_matrix[$row][$col] = $value;
	}

	public function GetValue($row, $col) {
		$this->_validateIndex($row, $col);
		return $this->_matrix[$row][$col];
	}

	public function Multiply($matrix2) {
		if ($this->_cols <> $matrix2->Rows) {
			throw new CException("Column count (" . $this->_cols . ") of first matrix isn't equal to second matrixes row (" . $matrix2->Rows . ").");
		}

		$result = new Matrix($this->_rows, $matrix2->Cols);

		for ($i = 0; $i < $this->_rows; $i++) { // first matrix's rows
			for ($j = 0; $j < $matrix2->Cols; $j++) { // second matrix's cols
				$val = 0;
				for ($k = 0; $k < $matrix2->Rows; $k++) { // second matrix's rows
					$val += $this->GetValue($i, $k) * $matrix2->GetValue($k, $j);
				}
				$result->SetValue($i, $j, $val);
			}
		}
		$this->_matrix = $result->Data;
		$this->_cols = $result->Cols;

		return $this->_matrix;
	}

	public function ScalarMultiply($matrix2) {
		if (is_array($matrix2)) {
			if (is_array($matrix2[0])) {
				throw new CException("Multi dimensional array is not allowed in scalar multiplication.");
			}

			$matrix2 = Matrix::FromArray($matrix2);
		} elseif ($matrix2->_rows != $this->_rows) {
			throw new CException("Matrix dimension isn't compatible.");
		} elseif (is_numeric($matrix2)) {
			$tmp = new Matrix($this->_rows, 1);
			for ($i = 0; $i < $tmp->Rows; $i++) {
				$tmp->SetValue($i, 0, $matrix2);
			}
			$matrix2 = $tmp;
		}

		for ($i = 0; $i < $this->_rows; $i++) {
			for ($j = 0; $j < $this->_cols; $j++) {
				$this->SetValue($i, $j, $this->GetValue($i, $j) * $matrix2->GetValue($i, 0));
			}
		}

		return $this->_matrix;
	}

	public function Normalize() {
		$total = $this->GetTotal();
		for ($i = 0; $i < $this->_cols; $i++) {
			for ($j = 0; $j < $this->_rows; $j++) {
				$this->_matrix[$j][$i] = $this->_matrix[$j][$i] / $total[$i];
			}
		}

		return $this->_matrix;
	}

	// Debugging functions
	public function View() {
		echo "<table style='border:solid black thin'>";
		for ($i = 0; $i < $this->_rows; $i++) {
			echo "<tr>";
			for ($j = 0; $j < $this->_cols; $j++) {
				echo "<td style='border:solid black thin'>" . $this->_matrix[$i][$j] . "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}

	public function GetTotal($asVertical = true) {
		$out = array();

		$x1 = $asVertical ? $this->_cols : $this->_rows;
		$x2 = $asVertical ? $this->_rows : $this->_cols;

		for ($i = 0; $i < $x1; $i++) {
			$result = 0;
			for ($j = 0; $j < $x2; $j++) {
				$result += $asVertical ? $this->_matrix[$j][$i] : $this->_matrix[$i][$j];
			}
			$out[] = $result;
		}

		return $out;
	}

	public function Square() {
		$this->_matrix = $this->Multiply($this);
		return $this->_matrix;
	}

	private function _validateIndex($row, $col, $asValue = true) {
		if (!is_numeric($row) || !is_numeric($col) || $row < 0 || $col < 0) {
			throw new CException("Row and column must be a numeric value larger than 0.");
		}

		if ($asValue && ($this->_rows <= $row || $this->_cols <= $col)) {
			throw new CException("Row and column is out of range.");
		}
	}

}

?>
