<?php
	include ("dbmem.php");
	
	function getStudent($str) {
		global $conmem;
		$sql = "select PERSON_ID, FIRST_NAME, LAST_NAME, STUDENT_NUMBER, USER_NAME " 
			. "from student_details_vw where student_number = '" . $str . "' union "
			. "select PERSON_ID, FIRST_NAME, LAST_NAME, STUDENT_NUMBER, USER_NAME " 
			. "from student_details_vw where barcode = '" . $str . "' union "
			. "select PERSON_ID, FIRST_NAME, LAST_NAME, STUDENT_NUMBER, USER_NAME " 
			. "from student_details_vw where person_id = '" . $str . "'";		
		$sqlp = ociparse($conmem, $sql);
		if (ociexecute($sqlp, OCI_DEFAULT)) {	
			if (ocifetchinto($sqlp, $data, OCI_ASSOC)) { return $data; }
			else { return false; }
		}
		else {
			$e = ocierror($sqlp);	
			printf ($e['message']);
		}	
	}	
	
	function getItem($str) {
		global $confeed;
		$sql = "select ASSET_ID, DESCRIPTION from CSE_ASSETS where barcode = '" . $str . "'";
		$sqlp = ociparse($confeed, $sql);
		
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			if (ocifetchinto($sqlp, $data, OCI_ASSOC)) { return $data; }
			else { return false; }
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
		}	
	}	
	
	function checkOut($stu, $it) {
		if (!checkItem($it)) {
			$student = getStudent($stu);
			$item = getItem($it);
			$person_id = $student['PERSON_ID'];
			$asset_id = $item['ASSET_ID'];
			$cout = date("d-M-Y h:m:sA");
			
			global $confeed;
			$sql = "select cse_trans_pk.nextval as tran_id from dual";
			$sqlp = ociparse($confeed, $sql);
			if (ociexecute($sqlp, OCI_DEFAULT)) {
				if (ocifetchinto($sqlp, $data, OCI_BOTH)) {
					$tran_id = $data['TRAN_ID'];
				}
			}
			else {
				$e = ocierror($sqlp);
				printf ($e['message']);
			}	
			
			$sql = "INSERT INTO CSE_TRANS (TRAN_ID, ASSET_ID, PERSON_ID, COUT) VALUES (:tran_id, :asset_id, :person_id, to_date(:cout, 'dd-mon-yyyy hh:mi:ss PM'))";
			$sqlp = ociparse($confeed, $sql);
			
			ocibindbyname($sqlp, ":tran_id", $tran_id);
			ocibindbyname($sqlp, ":asset_id", $asset_id);
			ocibindbyname($sqlp, ":person_id", $person_id);
			ocibindbyname($sqlp, ":cout", $cout);
			
			if (ociexecute($sqlp, OCI_DEFAULT)) {
				ocicommit($confeed);
				echo "Checkout Successful";
			}
			else {
				$e = ocierror($sqlp);
				printf ($e['message']);
			}
		} else {
			echo "Item already checked out";
		}
	}
	
	function checkItem($it) {
		$item = getItem($it);
		global $confeed;
		$sql = "select cin from cse_trans where asset_id = " . $item['ASSET_ID'];
		$sqlp = ociparse($confeed, $sql);
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			while (ocifetchinto($sqlp, $data, OCI_BOTH)) {
				if ($data['CIN'] == null) { return true; }
			}
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
			return false;
		}	
		return false;
	}
	
	function showCheckedOutItems() {
		$row = 0;
		global $confeed;
		$sql = 	"select t.tran_id, t.asset_id, t.person_id, to_char(t.cout, 'DD/MON/YY HH:MI AM') as co, a.barcode, a.description" . 
				" from cse_trans t inner join cse_assets a on t.asset_id = a.asset_id  where cin is null";
		$sqlp = ociparse($confeed, $sql);
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			echo "<div style='padding: 2px; border-bottom: 1px solid black; overflow: auto;'><div style='float: left; width: 30%'><h5>Name</h5></div><div style='width: 30%;float:left;'><h5>Item</h5></div><div style='width: 30%;float:left;'><h5>Checked Out At</h5></div></div>";
			while (ocifetchinto($sqlp, $data, OCI_BOTH)) {
				$student = getStudent($data['PERSON_ID']);
				$tran = $data['TRAN_ID'];
				if ($row % 2 == 1) {
					echo "<div style='background-color: #eee; padding: 2px; overflow: auto;'><div id=r".$row." style='float:left;display:none;'>".$tran."</div><div style='width: 30%;float:left;'>" 
						. $student['FIRST_NAME'] . " " . $student['LAST_NAME'] . "</div><div style='width: 30%; float:left;'>" 
						. $data["DESCRIPTION"] . "</div><div style='width: 25%; float:left;'>" 
						. $data["CO"] . "</div><div style='float:left;width: 15%;'><a href='#' onClick='checkIn(r".$row.".innerHTML);'>Check In</a></div><div id='c".$row."' style='float:left;width: 15%;'></div></div>";
				} else {
					echo "<div style='background-color: #fff; padding: 2px; overflow: auto;'><div id=r".$row." style='float:left;display:none'>".$tran."</div><div style='float:left; width: 30%;'>" 
						. $student['FIRST_NAME'] . " " . $student['LAST_NAME'] . "</div><div style='float:left;width: 30%'>" 
						. $data['DESCRIPTION'] . "</div><div style='width: 25%; float:left;'>" 
						. $data["CO"] . "</div><div style='float:left;width: 15%;'><a href='#' onClick='checkIn(r".$row.".innerHTML);'>Check In</a></div><div id='c".$row."' style='float:left;width: 15%;'></div></div>";
				}
				$row++;
			}
			echo "<div style='padding: 2px; border-top: 1px solid black; text-align: right;'><b>Total Items:</b> " . $row . "</div>";	
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
			return false;
		}		
	}	

	function checkIn($tid) {
		global $confeed;
		$cin = date("d-M-Y h:m:sA");
		$sql = 	"update cse_trans set CIN = to_date(:cin, 'dd-mon-yyyy hh:mi:ss PM') where tran_id = " . $tid; 
		$sqlp = ociparse($confeed, $sql);
		ocibindbyname($sqlp, ":cin", $cin);
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			ocicommit($confeed);
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
		}	
		showCheckedOutItems();
	}
	
	function showAllItems() {
		global $confeed;
		$sql = "select BARCODE, DESCRIPTION from CSE_ASSETS";
		$sqlp = ociparse($confeed, $sql);
		$row = 0;
		
		if(ociexecute($sqlp, OCI_DEFAULT)) {
			echo "<div style='padding: 2px; border-bottom: 1px solid black;'><div style='float: left; width: 20%'><h5>Barcode</h5></div><div><h5>Description</h5></div></div>";
			while(ocifetchinto($sqlp, $data, OCI_BOTH)) {
				if ($row % 2 == 1) {
					echo "<div style='background-color: #eee; padding: 2px;'><div style='float: left; width: 20%'>" . $data["BARCODE"] . "</div><div>" . $data["DESCRIPTION"] . "</div></div>";
				} else {
					echo "<div style='background-color: #fff; padding: 2px;'><div style='float: left; width: 20%'>" . $data["BARCODE"] . "</div><div>" . $data["DESCRIPTION"] . "</div></div>";
				}
				$row++;
			}
			echo "<div style='padding: 2px; border-top: 1px solid black; text-align: right;'><b>Total Items:</b> " . $row . "</div>";
		}
		else {
			$e = ocierror($sqlp);	
			printf ($e['message']);
		}
	}

	function chksByMonth() {
		global $confeed;
		$x = array();
		$r = 0;
		$sql = "select to_char(t.cout, 'Month') as month, count(*) as count from cse_trans t " . 
				"inner join cse_assets a on t.asset_id = a.asset_id group by to_char(t.cout, 'Month')";
		$sqlp = ociparse($confeed, $sql);
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			while(ocifetchinto($sqlp, $data, OCI_ASSOC)) {
				if (!$r) {
					array_push($x, array('item'=>'Month', 'count'=>'Count'));
					$r = 1;
				}
				array_push($x, array('item'=>$data['MONTH'], 'count'=>$data['COUNT']));
			}
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
		}
		return $x;
	}
	
	function chksByItem() {
		global $confeed;
		$x = array();
		$r = 0;
		$sql = "select a.description as item, count(*) as count from cse_trans t " . 
				"inner join cse_assets a on t.asset_id = a.asset_id group by a.description order by count(*) desc";
		$sqlp = ociparse($confeed, $sql);
		if (ociexecute($sqlp, OCI_DEFAULT)) {
			while (ocifetchinto($sqlp, $data, OCI_ASSOC)) {
				if (!$r) {
					array_push($x, array('item'=>'Item', 'count'=>'Count'));
					$r = 1;
				}
				array_push($x, array('item'=>$data['ITEM'], 'count'=>$data['COUNT']));
			}
		}
		else {
			$e = ocierror($sqlp);
			printf ($e['message']);
		}
		return $x;
	}
	
	function php2gchart($x) {
		$z = array();
		$r = 0;
		foreach ($x as $key=>$val) {
			if (!$r) {
				$z[] =  "[ '" . implode("', '", $val) . "' ]";
				$r = 1;
			} else {
			$z[] =  "[ '" . implode("', ", $val) . " ]";
			}
		}
		$str = "[ ". implode(',',$z) . " ]";
		return($str);
	}

?>