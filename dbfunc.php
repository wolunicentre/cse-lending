<?php

	putenv('ORACLE_HOME=/packages/oracle/9.2.0.1.0');
	putenv('ORA_NLS33=/packages/oracle/9.2.0.1.0/ocommon/nls/admin/data');
	$host = '(DESCRIPTION =(ADDRESS =(PROTOCOL = TCP)(HOST = corfu.its.uow.edu.au)(PORT = 1521))(CONNECT_DATA = (SID = NUIT)))';
	$connect = OCILogon("ucmember", "TALXcCYb", $host);



function getStuDetails($q) {
	

	$q = $_GET["q"];
	$error = 0;
	
	$sql = "select PERSON_ID, FIRST_NAME, LAST_NAME, STUDENT_NUMBER, USER_NAME 
		from student_details_vw where student_number = '" . $q . "' union
		select PERSON_ID, FIRST_NAME, LAST_NAME, STUDENT_NUMBER, USER_NAME 
		from student_details_vw where barcode = '" . $q . "'"; 
	$sqlp = ociparse($connect, $sql);
	
	ociexecute($sqlp, OCI_DEFAULT);
	ocifetchinto($sqlp, $data, OCI_BOTH);

	return $data;
}
?>