<?php 
class BDFModel extends CI_model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
	}

	public function getCancelledAndTransfers($dateStart, $dateEnd) {
		$dateStart .= ' 00:00:00';		$dateEnd .=' 23:59:59';

		$sql = "SELECT B.payment_date,B.Id,B.parking_name,B.Park_name, B.total_paid,B.payment_type,B.filled_by FROM 
		(SELECT 
		CASE WHEN pjo.job_order LIKE '%expired%' THEN pjo.amount ELSE s.amount END AS total_paid,
		s.invoice_date AS payment_date,
		a.id AS Id,a.years_active,p.name AS parking_name,s.filled_by,
		(CASE WHEN a.years_active = 0 THEN 0 
		WHEN a.years_active = 1 THEN p.annual_rent*0.20 
		WHEN a.years_active = 2 THEN p.annual_rent * 0.25
		WHEN a.years_active = 3 THEN p.annual_rent * 0.30
		WHEN a.years_active = 4 THEN p.annual_rent * 0.35
		END )AS discount_amount,
		CASE WHEN pjo.job_order LIKE '%expired%' THEN pjo.job_order ELSE s.payment_mode END AS payment_type,
		a.create_date AS rent_from,a.expiry_date AS rent_to,
		c.CPR , CONCAT ( c.first_name, SPACE(1) ,c.last_name ) AS emp_name, ps.`name` AS Park_name
		FROM subs_transactions s
		INNER JOIN customers c ON s.customers_id = c.id
		INNER JOIN account a ON a.customers_id = c.id
		INNER JOIN reserved_space_history rsh ON a.id = rsh.account_id
		LEFT OUTER JOIN reserved_space_history p2 ON (	a.id = p2.account_id AND (rsh.id < p2.id )	)
		INNER JOIN park_space ps ON rsh.parkspace_id = ps.id
		INNER JOIN parking p ON p.id = ps.Parking_id
		LEFT JOIN parking_job_orders pjo ON pjo.subs_transactions_id = s.id
		WHERE s.invoice_date BETWEEN '$dateStart' AND '$dateEnd'
		AND pjo.job_order IN ('cancellation','Expired')
		AND p2.id IS NULL
		ORDER BY a.id  ASC
		) B
		LEFT JOIN 
		(SELECT 
		CASE WHEN pjo.job_order LIKE '%expired%' THEN pjo.amount ELSE s.amount END AS total_paid,
		s.invoice_date AS payment_date,
		a.id AS Id,a.years_active,p.name AS parking_name,s.filled_by,
		(CASE WHEN a.years_active = 0 THEN 0 
		WHEN a.years_active = 1 THEN p.annual_rent*0.20 
		WHEN a.years_active = 2 THEN p.annual_rent * 0.25
		WHEN a.years_active = 3 THEN p.annual_rent * 0.30
		WHEN a.years_active = 4 THEN p.annual_rent * 0.35
		END )AS discount_amount,
		CASE WHEN pjo.job_order LIKE '%expired%' THEN pjo.job_order ELSE s.payment_mode END AS payment_type,
		a.create_date AS rent_from,a.expiry_date AS rent_to,
		c.CPR , CONCAT ( c.first_name, SPACE(1) ,c.last_name ) AS emp_name, ps.`name` AS Park_name
		FROM subs_transactions s
		INNER JOIN customers c ON s.customers_id = c.id
		INNER JOIN account a ON a.customers_id = c.id
		INNER JOIN reserved_space rs ON a.id = rs.account_id
		INNER JOIN park_space ps ON rs.parkspace_id = ps.id
		INNER JOIN parking p ON p.id = ps.Parking_id
		LEFT JOIN parking_job_orders pjo ON pjo.subs_transactions_id = s.id
		WHERE s.invoice_date  BETWEEN '$dateStart' AND '$dateEnd'
		AND pjo.job_order IN ('cancellation','Expired')
		ORDER BY a.id  ASC
		) A

		ON B.Id  = A.Id
		where A.Id IS NULL";

		$records = $this->db->query($sql);

		$sqlTransfer = "SELECT 
		SUM(subs_transactions.amount) AS total_transfer
	  FROM
		subs_transactions 
	  WHERE subs_transactions.`invoice_date` BETWEEN '$dateStart' 
		AND '$dateEnd' 
		AND subs_transactions.`status` = 'transfer';";

		$totalTransfer = $this->db->query($sqlTransfer);

		$sqlRefund = "SELECT SUM(subs_transactions.amount) AS total_refund
					  FROM	subs_transactions 
					  WHERE subs_transactions.`invoice_date` BETWEEN '$dateStart' AND '$dateEnd' 
					  AND subs_transactions.`status` = 'refund'
					  AND subs_transactions.`payment_mode` NOT LIKE '%cancellation%'
					  ;";

		$totalRefund = $this->db->query($sqlRefund);

		$data['records']       = $records->result();
		$data['totalTransfer'] = $totalTransfer->result();
		$data['totalRefund']   = $totalRefund->result();

		return $data;

	}
	
	function removeMemberVehicle($account_id ,$car_plate_number,$car_make,$car_color,$car_country){
		$this->db->trans_start();
		$this->db->where('account_id',$account_id);
		$this->db->where("car_plate_number LIKE $car_plate_number");

		if($car_make != '' && $car_make != null){
			$this->db->where("car_make LIKE '%$car_make%'");
		}
		
		if($car_color != '' && $car_color != null){
			$this->db->where("car_color LIKE '%$car_color%'");
		}
		
		if($car_country != '' && $car_country != null){
			$this->db->where("car_country LIKE '%$car_country%'");
		}
		
		$this->db->delete('account_cars');
		return $this->db->trans_complete();
	}

	public function generateMembershipReverse($month = 0,$year = 0) {
		if($month >0 && $year >0){
            $idPrefix = 'BDF'.$month.$year;
            return $this->db->select('id')->from('customers')->like('id', $idPrefix)->order_by('id','DESC')->limit(1)->get();
        }
        $idPrefix = 'BDF'.date('m').date('y');
        return $this->db->select('id')->from('customers')->like('id', $idPrefix)->order_by('id','DESC')->limit(1)->get();	
    }

	public function getNonRenewedMembers() {
		$sql = "SELECT 
		account.`expiry_date`,
		account.`id`,
		account.`years_active` 
	   FROM
		 account 
	   WHERE account.`expiry_date` BETWEEN '2019-12-31 00:00:00' 
		 AND '2019-12-31 23:59:59' 
		 AND account.`id` LIKE 'BDF%';";

		 $result = $this->db->query($sql);

		 return $result->result();
	}

	public function getRenewedMembers($renewed) {

		$sql = "";
		if($renewed == '1') {

			$sql = "SELECT 
			customers.`first_name`,
			customers.`last_name`,
			park_space.`name`,
			customers.`id` AS membership_id
		  FROM
			customers 
			INNER JOIN account 
			  ON account.`id` = customers.`id` 
			INNER JOIN reserved_space 
			  ON reserved_space.`account_id` = account.id 
			INNER JOIN park_space 
			  ON park_space.id = reserved_space.`parkspace_id` 
		  WHERE expiry_date BETWEEN '2020-12-31 00:00:00' 
			AND '2020-12-31 23:59:59' 
			AND account.id LIKE 'BDF%';";

		} else if ($renewed == '2') {
			$sql = "SELECT 
			customers.`first_name`,
			customers.`last_name`,
			park_space.`name`,
			customers.`id` AS membership_id 
		  FROM
			customers 
			INNER JOIN account 
			  ON account.`id` = customers.`id` 
			INNER JOIN reserved_space 
			  ON reserved_space.`account_id` = account.id 
			INNER JOIN park_space 
			  ON park_space.id = reserved_space.`parkspace_id` 
		  WHERE expiry_date BETWEEN '2019-12-31 00:00:00' 
			AND '2019-12-31 23:59:59' 
			AND account.id LIKE 'BDF%';";
		} else {
			$sql = "SELECT 
			customers.`first_name`,
			customers.`last_name`,
			park_space.`name`,
			customers.`id` AS membership_id 
		  FROM
			customers 
			INNER JOIN account 
			  ON account.`id` = customers.`id` 
			INNER JOIN reserved_space 
			  ON reserved_space.`account_id` = account.id 
			INNER JOIN park_space 
			  ON park_space.id = reserved_space.`parkspace_id` 
		  WHERE account.id LIKE 'BDF%';";
		}

		$result = $this->db->query($sql);
		
		return $result;
	}
	
	public function getRefundAndTransfers($dateStart, $dateEnd) {		
			$sql = "SELECT 
			DATE(subs_transactions.`invoice_date`) AS `date`,
			subs_transactions.`customers_id`,
			parking.name AS parking_name,
			park_space.`name` AS parking_space,
			subs_transactions.amount,
			subs_transactions.`payment_mode`,
			subs_transactions.`filled_by` 
		FROM
			subs_transactions 
			INNER JOIN parking_job_orders pjo 
			ON pjo.subs_transactions_id = subs_transactions.`id` 
			-- INNER JOIN reserved_space 
			-- ON reserved_space.`account_id` = subs_transactions.`customers_id` 
			INNER JOIN park_space 
			ON park_space.`id` = pjo.`parkspace_id` 
			INNER JOIN parking 
			ON parking.`id` = park_space.`Parking_id` 
		WHERE subs_transactions.`invoice_date` BETWEEN '$dateStart' 
			AND '$dateEnd' 
			AND subs_transactions.`status` IN ('refund', 'transfer')
			AND subs_transactions.`payment_mode` NOT LIKE '%cancellation%' ;";
			//AND subs_transactions.`status` IN ('refund','transfer') ;";

			$records = $this->db->query($sql);

			$sqlTransfer = "SELECT 
			SUM(subs_transactions.amount) AS total_transfer
		FROM
			subs_transactions 
		WHERE subs_transactions.`invoice_date` BETWEEN '$dateStart' AND '$dateEnd' 
			AND subs_transactions.`status` IN ('transfer')
			AND subs_transactions.`payment_mode` NOT LIKE '%cancellation%';";

			$totalTransfer = $this->db->query($sqlTransfer);

			$sqlRefund = "  SELECT SUM(subs_transactions.amount) AS total_refund
							FROM subs_transactions 
							WHERE subs_transactions.`invoice_date` BETWEEN '$dateStart' AND '$dateEnd' 
							AND subs_transactions.`status` = 'refund'
							AND subs_transactions.`payment_mode` NOT LIKE '%cancellation%' ;";

			$totalRefund = $this->db->query($sqlRefund);

			$data['records']       = $records->result();
			$data['totalTransfer'] = $totalTransfer->result();
			$data['totalRefund']   = $totalRefund->result();

		return $data;

	}

	public function insertIntoWaitingList($parkingLot1 = "", $parkSpace1 = "",$parkingLot2= "", $parkSpace2 = "",$firstName = "", $lastName = "", $cpr = "", $email = "", $mobileInput = "", $membership = "") {

		$waitingList = []; 

		$customers = array('first_name' => $firstName, 'last_name' => $lastName, 'CPR' => $cpr, 'email' => $email, 'id' => $membership, 'mobile_number' => $mobileInput, 'create_date' => date('Y-m-d H:i:s'), 'client_id' => 16);

		$this->db->trans_start();

			$waiting_list_token = $this->getWaitingNumber($parkingLot1); 
			$token = $waiting_list_token['token']; 
			++$token;

			$waitingList1 = array('first_name' => $firstName, 'last_name' => $lastName, 'CPR' => $cpr, 'email' => $email, 'customers_id' => $membership,
			'mobile_number' => $mobileInput,'parking_id' => $parkingLot1, 'parkspace_id' => $parkSpace1, 'create_date' => date('Y-m-d H:i:s'),'filled_by'=>$_SESSION['admin_login']['user'],'token'=>$token);
			$this->db->insert('waitinglist', $waitingList1);
			$insert_id[] = $this->db->insert_id();
			
			if($parkingLot2 != null && $parkingLot2 != ""  && $parkingLot2 != 0){

				$waiting_list_token = $this->getWaitingNumber($parkingLot2); 
				$token = $waiting_list_token['token']; 
				++$token;

				$waitingList2 = array('first_name' => $firstName, 'last_name' => $lastName, 'CPR' => $cpr, 'email' => $email, 'customers_id' => $membership,
			'mobile_number' => $mobileInput,'parking_id' => $parkingLot2, 'parkspace_id' => $parkSpace2, 'create_date' => date('Y-m-d H:i:s'),'filled_by'=>$_SESSION['admin_login']['user'],'token'=>$token);
				$this->db->insert('waitinglist', $waitingList2);
				$insert_id[] = $this->db->insert_id();
			}
		
		$this->db->trans_complete();
		return $insert_id[0];			
	}

	public function recalculationOfCharges($parkingLot, $parkingSpace) {

			$where = "parking.name = $parkingLot AND park_space.id = $parkingSpace";
		
			$this->db->select('  
				customers.first_name,
				customers.last_name,
				customers.CPR,
				customers.id,
				customers.mobile_number,
				organization.department,
				organization.profession,
				subs_transactions.amount,
				subs_transactions.invoice_date,
				subs_transactions.payment_mode,
				annualDiscount.discount,
				account.create_date,
				account.expiry_date,
				account.years_active,
				account_cars.car_plate_number,
				parking.name AS parking_name,
				parking.annual_rent,
				park_space.name AS park_space_name 
			');
			$this->db->from('customers');
			$this->db->join('reserved_space', 'reserved_space.account_id = customers.id', 'inner');
			$this->db->join('park_space', 'park_space.id = reserved_space.`parkspace_id`', 'inner');
			$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
			$this->db->join('account', 'account.customers_id = customers.id', 'inner');
			$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
			$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'left');
			$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');
			$this->db->join('annualDiscount', 'parking.id = annualDiscount.parking_id', 'inner');
			$this->db->where('park_space.parking_id', $parkingLot);
			$this->db->where('park_space.id', $parkingSpace);
	
			return $this->db->get()->result();
	}

	public function salesOfBDF($date, $paymentType, $dateTwo,$group_cond) {
		
		$where = "";

		if($group_cond){
			$group_cond = "subs_transactions.`invoice_date`";
		}

		if($date && $dateTwo) {
			$where .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$where .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$where .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$where .= " AND (subs_transactions.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$where .= " AND (subs_transactions.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$where .= " AND (subs_transactions.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$where .= " AND (subs_transactions.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$where .= " AND (subs_transactions.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$where .= " AND (subs_transactions.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$where .= " AND (subs_transactions.`payment_mode` = 'cardiac')";
		} else {
			$where .= " AND (subs_transactions.`payment_mode` = 'Credit' OR subs_transactions.`payment_mode` = 'Cash')";
		}

		$where .= " AND (subs_transactions.`status` IS NULL)";
		
		$this->db->select("
			subs_transactions.`invoice_date`,
			subs_transactions.`customers_id`,
			parking.name AS parking_name,
			park_space.`name` AS parking_space,
			parking.`annual_rent`,
			(parking.`annual_rent` - subs_transactions.amount) AS discount,
			subs_transactions.amount,
			subs_transactions.`payment_mode`,
			subs_transactions.`filled_by`
		");
		$this->db->from('subs_transactions');
		$this->db->join('reserved_space', 'reserved_space.`account_id` = subs_transactions.`customers_id`', 'left');
		$this->db->join('park_space', 'park_space.`id` = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('account', 'account.`id` = reserved_space.`account_id`', 'inner');
		$this->db->where($where);
		$this->db->order_by($group_cond);

		$result = $this->db->get();
		$data['records'] = $result->result();

		$this->db->select("
			SUM(parking.`annual_rent`) AS total_rent,
			SUM(parking.`annual_rent` - subs_transactions.amount) AS total_discount,
			SUM(subs_transactions.amount) AS total_paid_amount
		");
		$this->db->from('subs_transactions');
		$this->db->join('reserved_space', 'reserved_space.`account_id` = subs_transactions.`customers_id`', 'left');
		$this->db->join('park_space', 'park_space.`id` = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('account', 'account.`id` = reserved_space.`account_id`', 'inner');
		$this->db->where($where);
		$this->db->order_by($group_cond);

		$total = $this->db->get();

		$data['total'] = $total->result();

		$condition = "";

		$this->db->select('SUM(subs_transactions.amount) AS refunds');
		$this->db->from('subs_transactions');

		if($date && $dateTwo) {
			$condition .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$condition .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$condition .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$condition .= " AND (subs_transactions.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$condition .= " AND (subs_transactions.`payment_mode` = 'cardiac')";
		} else {
			$condition .= " AND (subs_transactions.`payment_mode` = 'Credit' OR subs_transactions.`payment_mode` = 'Cash')";
		}

		$condition .= " AND (subs_transactions.`status` = 'refund')";
		
		$this->db->where($condition);
		//$this->db->group_by($group_cond);

		$refunds = $this->db->get();
		$data['refunds'] = $refunds->result();

		$conditionTransfer = "";

		$this->db->select('SUM(subs_transactions.amount) AS transfer');
		$this->db->from('subs_transactions');

		if($date && $dateTwo) {
			$conditionTransfer .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$conditionTransfer .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$conditionTransfer .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$conditionTransfer .= " AND (subs_transactions.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'cardiac')";
		} else {
			$conditionTransfer .= " AND (subs_transactions.`payment_mode` = 'Credit' OR subs_transactions.`payment_mode` = 'Cash')";
		}

		$conditionTransfer .= " AND (subs_transactions.`status` = 'transfer')";
		
		$this->db->where($conditionTransfer);
		//$this->db->group_by($group_cond);

		$transfer = $this->db->get();
		$data['transfer'] = $transfer->result();

		return $data;
		// echo $this->db->last_query();

	}
	
	public function salesOfBDFAll($date, $paymentType, $dateTwo,$group_cond,$subs_type =  '',$subs_year = 0,$refunds = '',$transfer = '') {
		
		$subs_year = (int)$subs_year;
		$where = "";	

		if($group_cond == ""){
			$group_cond = 's.invoice_date';	
		}
		if($date && $dateTwo) {
			$where .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$where .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$where .= "(s.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$where .= " AND (s.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$where .= " AND (s.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$where .= " AND (s.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$where .= " AND (s.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$where .= " AND (s.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$where .= " AND (s.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$where .= " AND (s.`payment_mode` = 'cardiac')";
		} else {
			$where .= " AND (s.`payment_mode` = 'Credit' OR s.`payment_mode` = 'Cash')";
		}

		$where .= " AND  (s.`status` IS NULL OR s.`status`)";		
		//$where .= " AND  (s.`status` IS NULL OR s.`status` NOT IN ('transfer','refund','cancellation'))";		
		
		$this->db->select("s.invoice_date,pjo.`account_id` AS customers_id,parking.`name` AS parking_name,ps.`name` AS parking_space,parking.`annual_rent`,(  parking.`annual_rent` - s.amount    ) AS discount,s.`amount`,s.`payment_mode`,pjo.`filled_by`,pjo.`job_order` AS status");
		$this->db->from("parking_job_orders pjo");
		$this->db->join("subs_transactions s","( pjo.`subs_transactions_id` = s.id)");
		//$this->db->join("subs_transactions s","( pjo.`subs_transactions_id` = s.id OR s.`invoice_date` LIKE pjo.`order_date`)");
		$this->db->join("park_space ps ","ps.`id` = pjo.`parkspace_id`");
		$this->db->join("parking ","parking.id = ps.`Parking_id`");
		

		if($subs_type == 'Renew' && $subs_year >= 2021 && $subs_year != 0){
	
			$renew_where = '';
			$this->db->join('renewal_sales rsl','rsl.`account_id` = s.`customers_id` AND rsl.`subs_transactions_id` = s.id');
			$this->db->where("DATE_FORMAT(rsl.renewal_date,'%Y') LIKE '%$subs_year%'");
			$this->db->where("s.`status` LIKE '%$subs_type%'");
			$this->db->where("s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'");
			$this->db->order_by($group_cond);
			$result = $this->db->get();
			$data['records'] = $result->result();

			$renew_where .= "s.`status` LIKE '%$subs_type%'";
			$renew_where .= "AND s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'";
			$total_amount =$this->db->query("
			SELECT 
			SUM(parking.`annual_rent`) AS total_amount,
			SUM(  parking.`annual_rent` - s.amount    ) AS total_discount,
			SUM(pjo.`amount`) AS total_paid

			FROM parking_job_orders pjo
			JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id )
			JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
			JOIN parking  ON parking.id = ps.`Parking_id`
			JOIN renewal_sales rsl ON rsl.subs_transactions_id = s.id
			WHERE ".$renew_where. "ORDER BY " .$group_cond);

			$data['total'] = $total_amount->result();
		}

		else if($subs_type == 'Renew' && $subs_year <= 2020 && $subs_year != 0){
			$renew_where = "DATE_FORMAT( s.invoice_date,'%Y') LIKE '%$subs_year%'";
			$renew_where .= "AND s.id IN 
							(
								SELECT s.id
								FROM subs_transactions s 
								LEFT JOIN renewal_sales rsl ON rsl.`subs_transactions_id` = s.`id`
								WHERE s.`status` LIKE '%Renew%'
								AND s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'
								AND ( DATE_FORMAT(rsl.`renewal_date`,'%Y') LIKE '%$subs_year%' OR rsl.`renewal_date` IS NULL)
								AND rsl.`subs_transactions_id` IS  NULL
							)";
			$result = $this->db->query("
										SELECT s.invoice_date,
										pjo.`account_id` AS customers_id,
										parking.`name` AS parking_name,
										ps.`name` AS parking_space,
										parking.`annual_rent`,
										(  parking.`annual_rent` - s.amount    ) AS discount,
										s.`amount`,s.`payment_mode`,pjo.`filled_by`,pjo.`job_order` AS status

										FROM parking_job_orders pjo
										JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id)
										JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
										JOIN parking  ON parking.id = ps.`Parking_id`
										WHERE  ".$renew_where."
										ORDER BY ".$group_cond
									);
			$data['records'] = $result->result();

		
			$renew_where .= "AND s.`status` LIKE '%$subs_type%'";
			$renew_where .= "AND s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'";
			$total_amount =$this->db->query("
			SELECT 
			SUM(parking.`annual_rent`) AS total_amount,
			SUM(  parking.`annual_rent` - s.amount    ) AS total_discount,
			SUM(pjo.`amount`) AS total_paid

			FROM parking_job_orders pjo
			JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id )
			JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
			JOIN parking  ON parking.id = ps.`Parking_id`
			WHERE ".$renew_where. "ORDER BY " .$group_cond);

			$data['total'] = $total_amount->result();

		}
		else if($subs_type == 'All'){
			if($group_cond == ""){
				$group_cond = 's.invoice_date';	
			}
			$all_where = '';
			if($date && $dateTwo) {
				$all_where .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
				AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
			} else if($date) {
				$all_where .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
				AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
			} else {
				$all_where .= "(s.customers_id LIKE 'BDF%')";
			}
			if($paymentType == 'all') {
				$all_where .= " AND (s.`filled_by` IS NOT NULL)";
			}else if($paymentType == 'online') {
				$all_where .= " AND (s.`payment_mode` = 'Online')";
			} else if($paymentType == 'cash') {
				$all_where .= " AND (s.`payment_mode` = 'Cash')";
			} else if($paymentType == 'credit') {
				$all_where .= " AND (s.`payment_mode` = 'Credit')";
			} else if($paymentType == 'debit') {
				$all_where .= " AND (s.`payment_mode` = 'Debit')";
			} else if($paymentType == 'BDFRMS') {
				$all_where .= " AND (s.`payment_mode` = 'BDFRMS')";
			} else if($paymentType == 'cardiac') {
				$all_where .= " AND (s.`payment_mode` = 'cardiac')";
			} else {
				$all_where .= " AND (s.`payment_mode` = 'Credit' OR s.`payment_mode` = 'Cash')";
			}

			//$all_where = "(s.`status` IS NULL OR s.`status` NOT IN ('transfer','refund','cancellation'))";
			$all_where .= "AND DATE_FORMAT( s.invoice_date,'%Y') LIKE '%$subs_year%'";
			
			$result = $this->db->query("
										SELECT s.invoice_date,
										pjo.`account_id` AS customers_id,
										parking.`name` AS parking_name,
										ps.`name` AS parking_space,
										parking.`annual_rent`,
										(  parking.`annual_rent` - s.amount    ) AS discount,
										s.`amount`,s.`payment_mode`,pjo.`filled_by`,pjo.`job_order` AS status

										FROM parking_job_orders pjo
										JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id )
										JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
										JOIN parking  ON parking.id = ps.`Parking_id`
										WHERE  ".$all_where."
										ORDER BY ".$group_cond
									);
			$data['records'] = $result->result();

		
			$all_where .= "AND s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'";
			$total_amount =$this->db->query("
			SELECT 
			SUM(parking.`annual_rent`) AS total_amount,
			SUM(  parking.`annual_rent` - s.amount    ) AS total_discount,
			SUM(pjo.`amount`) AS total_paid

			FROM parking_job_orders pjo
			JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id )
			JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
			JOIN parking  ON parking.id = ps.`Parking_id`
			WHERE ".$all_where. "ORDER BY " .$group_cond);

			$data['total'] = $total_amount->result();

		}

		else{
			$this->db->where($where);
			$this->db->where("pjo.job_order NOT LIKE '%expired%'");
			$this->db->where("pjo.job_order NOT LIKE '%renew%'");
			$this->db->order_by($group_cond);
			$result = $this->db->get();
			$data['records'] = $result->result();


			$where .= "AND pjo.job_order NOT LIKE '%expired%'";
			$where .= "AND pjo.job_order NOT LIKE '%renew%'";

			$total_amount =$this->db->query("
				SELECT 
				SUM(parking.`annual_rent`) AS total_amount,
				SUM(  parking.`annual_rent` - s.amount    ) AS total_discount,
				SUM(pjo.`amount`) AS total_paid

				FROM parking_job_orders pjo
				JOIN subs_transactions s ON ( pjo.`subs_transactions_id` = s.id )
				JOIN park_space ps ON ps.`id` = pjo.`parkspace_id`
				JOIN parking  ON parking.id = ps.`Parking_id`
				WHERE ".$where. "ORDER BY " .$group_cond);

			$data['total'] = $total_amount->result();
		}

		if($refunds != '' && $refunds != null){
			$data['refunds'] = $this->get_refunds($date,$paymentType,$dateTwo,$group_cond,$subs_type,$subs_year,$refunds,$transfer);
		}		

		if($transfer != '' && $transfer != null){
			if($subs_type != 'Renew' && $subs_year < 2021){
				$data['transfer'] = $this->get_transfers($date,$paymentType,$dateTwo,$group_cond,$subs_type,$subs_year,$refunds,$transfer);
			}else{
				$data['transfer'] = 0;
			}
		}	

		return $data;
	}

	public function get_transfers($date, $paymentType, $dateTwo,$group_cond,$subs_type =  '',$subs_year = 0,$refunds = '',$transfer = ''){

		//transfer
		$conditionTransfer = "";
		$this->db->reset_query();
		$this->db->select('SUM(s.amount) AS transfer')->from('subs_transactions s');
		if($date && $dateTwo) {
			$conditionTransfer .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$conditionTransfer .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$conditionTransfer .= "(s.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$conditionTransfer .= " AND (s.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$conditionTransfer .= " AND (s.`payment_mode` = 'cardiac')";
		} else {
			$conditionTransfer .= " AND (s.`payment_mode` = 'Credit' OR s.`payment_mode` = 'Cash')";
		}
		$conditionTransfer .= " AND (s.`status` = 'transfer')";		
		$this->db->where($conditionTransfer);
		$transfer = $this->db->get();
		
		return $transfer->result();
	}

	public function get_refunds($date, $paymentType, $dateTwo,$group_cond,$subs_type =  '',$subs_year = 0,$refunds = '',$transfer = ''){

		//refunds

		$condition = "";

		$this->db->reset_query();
		$refunds = $this->db->select('SUM(s.amount) AS refunds')->from('subs_transactions s');
		if($date && $dateTwo) {
			$condition .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$condition .= "(s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$condition .= "(s.customers_id LIKE 'BDF%')";
		}
		if($paymentType == 'all') {
			$condition .= " AND (s.`filled_by` IS NOT NULL)";
		}else if($paymentType == 'online') {
			$condition .= " AND (s.`payment_mode` = 'Online')";
		} else if($paymentType == 'cash') {
			$condition .= " AND (s.`payment_mode` = 'Cash')";
		} else if($paymentType == 'credit') {
			$condition .= " AND (s.`payment_mode` = 'Credit')";
		} else if($paymentType == 'debit') {
			$condition .= " AND (s.`payment_mode` = 'Debit')";
		} else if($paymentType == 'BDFRMS') {
			$condition .= " AND (s.`payment_mode` = 'BDFRMS')";
		} else if($paymentType == 'cardiac') {
			$condition .= " AND (s.`payment_mode` = 'cardiac')";
		} else {
			$condition .= " AND (s.`payment_mode` = 'Credit' OR s.`payment_mode` = 'Cash')";
		}

		if($subs_type == 'Renew' && $subs_year >= 2021){
			$this->db->join('renewal_sales rsl','rsl.`account_id` = s.`customers_id`');
			$this->db->join('refund_details rfd','rfd.`s_refund_id` = s.`id`');
			$this->db->where("rsl.`renewal_date` LIKE '$subs_year%'");
		}
		else if( $subs_type == 'Renew' || $subs_type == 'New Membership'){
			$renewal_refund = "
			SELECT s.id FROM subs_transactions s 
			JOIN refund_details rd ON rd.`s_refund_id` = s.id
			JOIN account a ON a.id = s.`customers_id`
			WHERE s.`customers_id` IS NOT NULL
			AND s.`customers_id` != ''
			AND s.`status` LIKE '%refund%' AND s.`payment_mode` NOT LIKE '%cancellation%'
			AND s.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."'
			AND DATE_FORMAT(a.`expiry_date` ,'%Y') > $subs_year
			";
			$this->db->where("s.id NOT IN ($renewal_refund)"); 
		}
		else{

		}

		$condition .= " AND (s.`status` = 'refund')";		
		$this->db->where("s.`payment_mode` NOT LIKE '%cancellation%'");		
		$refunds = $this->db->where($condition);	
		$refunds = $this->db->get();
		return  $refunds->result();			
		
	}


	public function salesOfBDFParkingWise($date, $dateTwo,$parking) {
		
		$where = "";
		$bdf_location_id = 19;

		if($date && $dateTwo) {
			$where .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$where .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$where .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}

		if($parking != '' && $parking!= NULL){
			if($parking == 0){
				$where .= "AND parking.`location_id` = ".$bdf_location_id;	
			}
			else{
				$where .= "AND park_space.`Parking_id` = ".$parking;	
			}
		}

		
		$this->db->select("  
		subs_transactions.amount AS total_paid, subs_transactions.invoice_date AS payment_date,
		account.id,account.years_active,
		(CASE
		WHEN account.years_active = 0 THEN 0 
		WHEN account.years_active = 1 THEN parking.annual_rent*0.20 
		WHEN account.years_active = 2 THEN parking.annual_rent * 0.25
		WHEN account.years_active = 3 THEN parking.annual_rent * 0.30
		WHEN account.years_active = 4 THEN parking.annual_rent * 0.35
		END )AS discount_amount		
		,subs_transactions.payment_mode AS 'Payment_Mode',
		CASE WHEN subs_transactions.`status` IS NULL AND payment_mode LIKE '%cardiac%' THEN 'Cardiac' WHEN subs_transactions.`status` IS NULL THEN 'New Membership' ELSE subs_transactions.`status` END AS Type,
		(CASE WHEN account.create_date LIKE '2019-11%' OR account.create_date LIKE '2019-12%' THEN '2020-01-01 08:00:00' ELSE account.create_date END) AS rent_from,
		account.expiry_date AS rent_to,
		customers.CPR , CONCAT ( customers.first_name, SPACE(1) ,customers.last_name ) AS emp_name, park_space.`name` AS Park_name");

		$this->db->from('subs_transactions');
		$this->db->join('customers', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('reserved_space', 'account.id = reserved_space.account_id', 'left');
		$this->db->join('park_space', 'reserved_space.parkspace_id = park_space.id', 'inner');
		$this->db->join('parking', 'parking.id = park_space.Parking_id', 'inner');
		$this->db->where($where);
		$this->db->order_by('account.id', 'ASC');

		$result = $this->db->get();
		$data['records'] = $result;

		$this->db->select("
			SUM(parking.`annual_rent`) AS total_rent,
			SUM(parking.`annual_rent` - subs_transactions.amount) AS total_discount,
			SUM(subs_transactions.amount) AS total_paid_amount
		");
		$this->db->from('subs_transactions');
		$this->db->join('reserved_space', 'reserved_space.`account_id` = subs_transactions.`customers_id`', 'left');
		$this->db->join('park_space', 'park_space.`id` = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('account', 'account.`id` = reserved_space.`account_id`', 'inner');
		$this->db->where($where);
		
		$total = $this->db->get();

		$data['total'] = $total->result();

		$condition = "";

		$this->db->select('SUM(subs_transactions.amount) AS refunds');
		$this->db->from('subs_transactions');
		$this->db->join('reserved_space', 'reserved_space.`account_id` = subs_transactions.`customers_id`', 'left');
		$this->db->join('park_space', 'park_space.`id` = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('account', 'account.`id` = reserved_space.`account_id`', 'inner');
		

		if($date && $dateTwo) {
			$condition .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$condition .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$condition .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}
		// if($paymentType == 'all') {
		// 	$condition .= " AND (subs_transactions.`filled_by` IS NOT NULL)";
		// }else if($paymentType == 'online') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'Online')";
		// } else if($paymentType == 'cash') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'Cash')";
		// } else if($paymentType == 'credit') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'Credit')";
		// } else if($paymentType == 'debit') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'Debit')";
		// } else if($paymentType == 'BDFRMS') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'BDFRMS')";
		// } else if($paymentType == 'cardiac') {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'cardiac')";
		// } else {
		// 	$condition .= " AND (subs_transactions.`payment_mode` = 'Credit' OR subs_transactions.`payment_mode` = 'Cash')";
		// }

		$condition .= " AND (subs_transactions.`status` = 'refund')";
		if($parking == 0){
			$condition .= "AND parking.`location_id` = ".$bdf_location_id;	
		}
		else{
			$condition .= "AND park_space.`Parking_id` = ".$parking;
		}

		$this->db->where($condition);
		$refunds = $this->db->get();
		$data['refunds'] = $refunds->result();


		$this->db->select("  
		SUM(subs_transactions.amount) AS total,
		SUM(CASE
		WHEN account.years_active = 0 THEN 0 
		WHEN account.years_active = 1 THEN parking.annual_rent*0.20 
		WHEN account.years_active = 2 THEN parking.annual_rent * 0.25
		WHEN account.years_active = 3 THEN parking.annual_rent * 0.30
		WHEN account.years_active = 4 THEN parking.annual_rent * 0.35
		END) AS discount		
		");
		$this->db->from('subs_transactions');
		$this->db->join('customers', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('reserved_space', 'account.id = reserved_space.account_id', 'left');
		$this->db->join('park_space', 'reserved_space.parkspace_id = park_space.id', 'inner');
		$this->db->join('parking', 'parking.id = park_space.Parking_id', 'inner');
		$this->db->where($where);
		$this->db->order_by('account.id', 'ASC');
		$result = $this->db->get();
		$data['calculatedSum'] = $result;

		$this->db->select("parking.*");
		$this->db->from('parking');

		if($parking == 0){
			$this->db->where('parking.`location_id`',$bdf_location_id);	
		}else{
			$this->db->where('id',$parking);
		}

		$data['parkingDetails'] = $this->db->get();

		//total and vacant
		$this->db->select("
		SUM(CASE WHEN park_space.vacant = 'free' AND park_space.status = 1  THEN 1 ELSE 0 END) AS vacant,
		SUM(CASE WHEN park_space.vacant = 'busy' AND park_space.status = 0  THEN 1  ELSE 0 END) AS occupied,
		COUNT(park_space.id) as capacity
		");
		$this->db->from('park_space');
		
		if($parking == 0){
			$this->db->where('parking.`location_id`',$bdf_location_id);	
		}else{
			$this->db->where('Parking_id',$parking);
		}
		$this->db->join('parking','parking.id = park_space.Parking_id');
		$data['vacancy'] = $this->db->get();
		
		//new total and vacant
		$occupiedWhere = '';
		$this->db->select('invoice_date');
		$this->db->from('subs_transactions');
		$whereStmt = "invoice_date != 'NULL'";
		$this->db->where($whereStmt);
		$this->db->order_by('invoice_date','ASC');
		$this->db->limit(1);
		
		//$subsTransactionsDateQuery = 'SELECT  invoice_date FROM subs_transactions WHERE invoice_date != 'NULL' ORDER BY invoice_date ASC LIMIT 1;';
		$data['date'] = $this->db->get();
		$subs_transactions_date = $data['date']->row()->invoice_date;
		
		$subs_transactions_date = '2019-11-01 00:00:00';
		if($date && $dateTwo) {
			$occupiedWhere .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($subs_transactions_date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($dateTwo))."')";
		} else if($date) {
			$occupiedWhere .= "(subs_transactions.`invoice_date` BETWEEN '".date('Y-m-d 00:00:00', strtotime($subs_transactions_date))."' 
			AND '".date('Y-m-d 23:59:59', strtotime($date))."')";
		} else {
			$occupiedWhere .= "(subs_transactions.customers_id LIKE 'BDF%')";
		}

		if($parking != '' && $parking!= NULL){
			if($parking == 0){
				$occupiedWhere .= "AND parking.location_id =' $bdf_location_id'";
			}
		}
		
		$occupiedWhere .= " AND park_space.status != 1 AND park_space.vacant LIKE '%busy%'";
		
		$this->db->select("  
		COUNT(DISTINCT park_space.`name`) AS totalOccupied 
		");
		$this->db->from('subs_transactions');
		$this->db->join('customers', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('reserved_space', 'account.id = reserved_space.account_id', 'left');
		$this->db->join('park_space', 'reserved_space.parkspace_id = park_space.id', 'inner');
		$this->db->join('parking', 'parking.id = park_space.Parking_id', 'inner');
		$this->db->where($occupiedWhere);
		$this->db->order_by('account.id', 'ASC');

		$result = $this->db->get();
		$data['occupied'] = $result;

		return $data;
	}


	public function searchReceipts($membership, $parkingLot, $parkingSpace, $cpr, $memberCar, $approved,$vehicleNumber = '') {

		$where = "";

		if($membership) {
			$where .= "customers.id = '$membership'";
		} else {
			$where .= "customers.id LIKE 'BDF%'";
		}
		if($parkingLot) {
			$where .= " AND parking.id = '$parkingLot'";
		}
		if($parkingSpace) {
			$where .= " AND park_space.id = '$parkingSpace'";
		}
		if($cpr) {
			$where .= " AND customers.CPR = '$cpr'";
		}
		if($memberCar) {
			// $where .= " AND account_cars.car_plate_number = '$memberCar'";
		}
		if($approved) {
			
		}

		if($vehicleNumber != null && $vehicleNumber != '') {
			$where .= " AND account_cars.car_plate_number = '$vehicleNumber'";
		}

		$this->db->select("
							customers.first_name,
							customers.last_name,
							customers.CPR,
							customers.id,
							customers.mobile_number,
							organization.department,
							organization.profession,
							subs_transactions.amount,
							subs_transactions.invoice_date,
							subs_transactions.payment_mode,
							subs_transactions.`id` AS invoice_number,
							account.create_date,
							account.expiry_date,
							parking.name,
							park_space.name AS park_name
						");

		$this->db->from('customers');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('reserved_space', 'account.id = reserved_space.`account_id`', 'left');
		$this->db->join('park_space', 'park_space.id = reserved_space.parkspace_id', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('location', 'location.id = parking.`location_id`', 'inner');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');

		$this->db->where($where);
		$this->db->order_by('subs_transactions.invoice_date', 'DESC');

		return $this->db->get();

	}

	public function getBDFCars() {

		$sql = "SELECT * FROM account_cars WHERE account_id LIKE 'BDF%'";

		return $this->db->query($sql);

	}

	public function getBDFMembersCPRs() {

		// return $this->db->select('*')->from('customers')->like('CPR', 'BDF', 'after')->get();

		$sql = "SELECT * FROM customers WHERE id LIKE 'BDF%'";

		return $this->db->query($sql);

	}

	public function getBDFMembersMobile() {

		// return $this->db->select('*')->from('customers')->like('CPR', 'BDF', 'after')->get();

		$sql = "SELECT * FROM mobile WHERE customers_id LIKE 'BDF%'";

		return $this->db->query($sql);

	}

	public function getSubstransactionStatus() {
		$sql = "SELECT * FROM subs_transactions WHERE customers_id LIKE 'BDF%' GROUP BY status";
		return $this->db->query($sql);
	}

	public function getFilteredParkingSpaces($parkingLot = null, $parkingSpace = null, $vacant = null, $status = null) {

		$where = "";
		$bdf_location_id = 19;

		if($parkingLot) {
			$where .= "parking_id = '$parkingLot'";
		} else {
			$where .="location.id = $bdf_location_id";
		}
		if($parkingSpace) {
			$where .= " AND park_space.id = '$parkingSpace'";
		} 
		if($status == '1') {
			$where .= " AND `status` = '1'";
		} else if ($status == '2') {
			$where .= " AND `status` = '0'";
		}
		if($vacant) {
			$where .= " AND vacant = '$vacant'";
		}

		if(isset($parkingLot) || isset($parkingSpace) || isset($status) || isset($vacant)) {
			return $this->db->select('park_space.*, parking.name AS parkingLotName')
							->from('park_space')
							->join('parking', 'parking.id = park_space.`Parking_id`', 'inner')
							->join('location', 'parking.location_id = location.id', 'inner')
							->where($where)
							->get();
		} else {
			return $this->db->select("park_space.*, parking.name AS parkingLotName")
							->from("park_space")
							->join("parking", "parking.id = park_space.`Parking_id`", "inner")
							->join("location", "parking.location_id = location.id", "inner")
							->where("location.id = $bdf_location_id")
							->get();
		}

	}

	public function getAllParkingSpacesPerParkingLot($id) {

		$where = "parking_id = $id";
		return $this->db->select('*')->from('park_space')->where($where)->get();

	}

	public function cancelAccountSubscription($id,$refundAmount) {
		
		$lastInsertedIdQuery = $this->db->select('id')->from('subs_transactions')->order_by('id','DESC')->get();
		$lastInsertedId     = $lastInsertedIdQuery->row();
		$newid = $lastInsertedId->id;
		++$newid;
		//strval($newid);
		if(strlen(strval($newid))<6){
			$newid = '0'.$newid;
		}

		$todaysDate = date('Y-m-d H:i:s');

		$account_expiry_info = array(
			'expiry_date' => $todaysDate,
			'active' => 0
		);
		$status = "refund";
		if(!$refundAmount || $refundAmount == 0){
			$refundAmount = 0;
			$status = "cancellation";
		}

		$subs_transaction = array(
			'id' => $newid,
			'invoice_date' => $todaysDate,
			'customers_id' => $id,
			'amount' =>$refundAmount,
			'payment_mode' => 'Cancellation',
			'filled_by'=>$_SESSION['admin_login']['user'],
			'status'=>$status
		);

		$parkSearch = $this->db->select('reserved_space.*')->from('reserved_space')->where('reserved_space.account_id', $id)->get();
		$result     = $parkSearch->result();

		$park_history_old = array(
			'account_id' => $result['0']->account_id,
			'parkspace_id' => $result['0']->parkspace_id,
			'to_date' => date('Y-m-d H:i:s'),
		);

		$data = array(
			'vacant' => 'free',
			'status' => '1'
		);

		$parking_job_order = array(
			'account_id' => $id,
			'job_order' => 'cancellation',
			'subs_transactions_id' => $newid,
			'parkspace_id' => $result['0']->parkspace_id,
			'order_date' => date('Y-m-d H:i:s'), 
			'filled_by' => $_SESSION['admin_login']['user'],
			'amount' => $refundAmount,
			'status' => 1
		);

		//old membership type
		$query = $this->db->get_where('accounts_membership_types',array('account_id'=>$id,'active'=>1));
		$membership_type = $query->row_array();

		$amt_data = array(
			'active'=>0
		);

		$amt_cncl_record = array(
			'account_id' =>  $id,
			'membership_type_id' => $membership_type['membership_type_id'],
			'subs_transactions_id' => $membership_type['subs_transactions_id'],
			'parkspace_id' => $membership_type['parkspace_id'],
			'active' => 0
		);
	
		$this->db->where('id', $result['0']->parkspace_id);
		$this->db->update('park_space', $data);
		
		$this->db->trans_start();
			$this->db->where('customers_id',$id);
			$this->db->update('account',$account_expiry_info);

			$this->db->update('accounts_membership_types', $amt_data, array('account_id' => $id));

			$this->db->insert('subs_transactions',$subs_transaction);

			$this->db->insert('accounts_membership_types',$amt_cncl_record);

			$this->db->insert('parking_job_orders',$parking_job_order);
			$this->db->delete('reserved_space', array('account_id' => $id));
			$this->db->insert('reserved_space_history', $park_history_old);

		$query_result = $this->db->trans_complete();
		$waiting_result = $this->inform_waitinglist_admin($result['0']->parkspace_id);
		return ($query_result || $waiting_result);

	}

	public function transferAccountSubscription($id, $parkingLot, $parkingSpace, $invoiceNumber, $price) {
		$parkSearch = $this->db->select('reserved_space.*')->from('reserved_space')->where('reserved_space.account_id', $id)->get();
		$result     = $parkSearch->result();

		if(!isset($result)) {
			redirect(base_url('transfer'));
		}

		$accId = $result['0']->account_id;
		$pId = $result['0']->parkspace_id;


		$park_history_old = array(
			'account_id' => $result['0']->account_id,
			'parkspace_id' => $result['0']->parkspace_id,
			'to_date' => date('Y-m-d H:i:s'),
		);

		$park_history_new = array(
			'account_id' => $id,
			'parkspace_id' => $parkingSpace,
			'from_date' => date('Y-m-d H:i:s'),
		);

		$subs_transactions = array(
			'id' => $invoiceNumber,
			'invoice_date' => date('Y-m-d H:i:s'), 
			'customers_id' => $id,
			'amount' => $price,
			'payment_mode' => 'transfer',
			'filled_by' => $_SESSION['admin_login']['user'],
			'status' => 'transfer'
		);

		$parking_job_order = array(
			'account_id' => $id,
			'job_order' => 'transfer',
			'subs_transactions_id' => $invoiceNumber,
			'parkspace_id' => $parkingSpace,
			'order_date' => date('Y-m-d H:i:s'), 
			'filled_by' => $_SESSION['admin_login']['user'],
			'amount' => $price,
			'status' => 1
		);


		$data = array(
			'vacant' => 'free',
			'status' => '1'
		);		

		//old membership type
		$query = $this->db->get_where('accounts_membership_types',array('account_id'=>$id,'active'=>1));
		$membership_type = $query->row_array();

		//set old active to 0
		$this->db->where(array('account_id'=>$id,'active'=>1));
		$this->db->set('active', 0);
		$this->db->update('accounts_membership_types');

		//insert new  
		$accounts_membership_types = array(
			'account_id'=>$id,
			'membership_type_id'=>$membership_type['membership_type_id'],
			'subs_transactions_id'=>$invoiceNumber,
			'parkspace_id'=>$parkingSpace,
			'active'=>1,
		);
	
		$this->db->where('id', $result['0']->parkspace_id);
		$this->db->update('park_space', $data);
		
		// $this->db->trans_start();
		// 	$this->db->delete('reserved_space', array('account_id' => $id));
		// $this->db->trans_complete();

		$park_space = array('vacant' => 'busy', 'status' => 0);
		$reserved_space = array('account_id' => $id, 'parkspace_id' => $parkingSpace);

		$this->db->trans_start();
			$this->db->where('id', $parkingSpace);
			$this->db->update('park_space', $park_space);
		// $this->db->trans_complete();
		// $this->db->trans_start();
			$this->db->delete('reserved_space', array('account_id' => $id));
			$this->db->insert('reserved_space', $reserved_space);
			$this->db->insert('subs_transactions', $subs_transactions);
			$this->db->insert('accounts_membership_types', $accounts_membership_types);
			$this->db->insert('parking_job_orders', $parking_job_order);
			$this->db->insert('reserved_space_history', $park_history_old);
			$this->db->insert('reserved_space_history', $park_history_new);
		$query_reslt = $this->db->trans_complete();

		$waiting_list_Result = $this->inform_waitinglist_admin($result['0']->parkspace_id);

		return($query_reslt || $waiting_list_Result);

	}

	public function getInvoiceCancellation($membership,$vehicleNumber = '') {
		$this->db->select('  
		customers.first_name,customers.last_name,customers.CPR,customers.id,customers.mobile_number,
		organization.department,organization.profession,
		subs_transactions.amount,subs_transactions.invoice_date,subs_transactions.payment_mode,
		account.create_date,account.expiry_date,account.years_active,account.customers_id,
		account_cars.car_plate_number,parking.name AS parking_name,park_space.name AS park_space_name,
		amt.membership_type_id,mt.* 
		');
		$this->db->from('customers');
		$this->db->join('reserved_space', 'reserved_space.account_id = customers.id', 'inner');
		$this->db->join('park_space', 'park_space.id = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'left');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');
		
		$this->db->join('accounts_membership_types amt', 'amt.account_id = customers.id', 'left');
		$this->db->join('membership_types mt', 'mt.id = amt.membership_type_id', 'left');
		
		if($membership != null && $membership != ''){
			$this->db->where('customers.id', $membership);
		}

		if($vehicleNumber != null && $vehicleNumber != ''){
			$this->db->where('account_cars.car_plate_number', $vehicleNumber);
		}
		//$this->db->where('subs_transactions.status IS NULL');
		$this->db->order_by('subs_transactions.invoice_date');
		$this->db->limit(1);
		return $this->db->get();
	}

	public function getInvoiceTransfer($membership = '',$parkingLot = 0,$parkingSpace = 0,$vehicleNumber = '',$cpr_number = 123456789,$mobile_number = 123456789) {
		$this->db->select('  
		customers.first_name,customers.last_name,customers.CPR,customers.id,customers.mobile_number,
		organization.department,organization.profession,
		subs_transactions.amount,subs_transactions.invoice_date,subs_transactions.payment_mode,subs_transactions.status,
		annualDiscount.discount,
		account.create_date,account.expiry_date,account.years_active,
		account_cars.car_plate_number,
		parking.id as parkingId,parking.name AS parking_name,parking.annual_rent,
		park_space.name AS park_space_name,park_space.id AS ps_id,amt.membership_type_id,mt.*
		');
		$this->db->from('customers');
		$this->db->join('reserved_space', 'reserved_space.account_id = customers.id', 'inner');
		$this->db->join('park_space', 'park_space.id = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('annualDiscount', 'annualDiscount.parking_id = parking.id', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'left');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');
		
		$this->db->join('accounts_membership_types amt', 'amt.account_id = customers.id', 'left');
		$this->db->join('membership_types mt', 'mt.id = amt.membership_type_id', 'left');
		
		if($membership != null && $membership != ''){
			$this->db->where('customers.id', $membership);
		}

		if($vehicleNumber != null && $vehicleNumber != ''){
			$this->db->where('account_cars.car_plate_number', $vehicleNumber);
		}

		if($parkingLot != null && $parkingLot != '' && $parkingLot != 0){
			$this->db->where('parking.id', $parkingLot);
		}

		if($parkingSpace != null && $parkingSpace != '' && $parkingSpace != 0){
			$this->db->where('park_space.id', $parkingSpace);
		}

		if($cpr_number != null && $cpr_number != '' && $cpr_number != 0){
			$this->db->where("customers.CPR LIKE '%$cpr_number%'");
		}

		if($mobile_number != null && $mobile_number != '' && $mobile_number != 0){
			$this->db->where("customers.mobile_number LIKE '%$mobile_number%'");
		}

		// $this->db->order_by('subs_transactions.invoice_date', 'DESC');
		// $this->db->limit(1);

		return $this->db->get();
	}

	public function get_member_details($membership = '',$parkingLot = 0,$parkingSpace = 0,$vehicleNumber = '',$cpr_number = 123456789,$mobile_number = 123456789,$sub_status = null) {
		$this->db->select('  
		customers.first_name,customers.last_name,customers.CPR,customers.id as c_id,customers.mobile_number,
		organization.department,organization.profession,
		subs_transactions.amount,subs_transactions.invoice_date,subs_transactions.payment_mode,subs_transactions.status,subs_transactions.id as s_id,
		annualDiscount.discount,
		account.create_date,account.expiry_date,account.years_active,
		account_cars.car_plate_number,
		parking.id as parkingId,parking.name AS parking_name,parking.annual_rent,
		park_space.name AS park_space_name,park_space.id AS ps_id,amt.membership_type_id,mt.*
		');
		$this->db->from('customers');
		$this->db->join('reserved_space', 'reserved_space.account_id = customers.id', 'inner');
		$this->db->join('park_space', 'park_space.id = reserved_space.`parkspace_id`', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('annualDiscount', 'annualDiscount.parking_id = parking.id', 'inner');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');
		
		$this->db->join('accounts_membership_types amt', 'amt.account_id = customers.id', 'left');
		$this->db->join('membership_types mt', 'mt.id = amt.membership_type_id', 'left');
		
		if($membership != null && $membership != ''){
			$this->db->where('customers.id', $membership);
		}

		if($vehicleNumber != null && $vehicleNumber != ''){
			$this->db->where('account_cars.car_plate_number', $vehicleNumber);
		}

		if($parkingLot != null && $parkingLot != '' && $parkingLot != 0){
			$this->db->where('parking.id', $parkingLot);
		}

		if($parkingSpace != null && $parkingSpace != '' && $parkingSpace != 0){
			$this->db->where('park_space.id', $parkingSpace);
		}

		if($cpr_number != null && $cpr_number != '' && $cpr_number != 0){
			$this->db->where("customers.CPR LIKE '%$cpr_number%'");
		}

		if($mobile_number != null && $mobile_number != '' && $mobile_number != 0){
			$this->db->where("customers.mobile_number LIKE '%$mobile_number%'");
		}

		if($sub_status != ''){
			if($sub_status != null ){
				$this->db->where("subs_transactions.status like '%$sub_status%'");
			}
			else{
				$this->db->where("subs_transactions.status IS $sub_status");
			}
		}

		// $this->db->order_by('subs_transactions.invoice_date', 'DESC');
		// $this->db->limit(1);

		return $this->db->get();
	}

	public function refund_member($subs_id = 000000,$members_id = '',$amount_received = 0 ,$actual_amount = 0,$amount_refunded = 0,$cause = '',$comment = '',$payment_mode = '',$parkspace_id = 0){
		//get subs_transactions_record
		$subs_record = $this->db->get_where('subs_transactions',array('id'=>$subs_id));
		$subs_orginal = $subs_record->row_array();

		$record = '';
		$record .= '/'+$subs_orginal['id'] .'/'.$subs_orginal['customers_id'].'/'.$subs_orginal['invoice_date'].'/'.$subs_orginal['amount'].'/'.$subs_orginal['payment_mode'].'/'.$subs_orginal['filled_by'].'/'.$subs_orginal['status'].'/'.$subs_orginal['altered_by'].'/'.$subs_orginal['altered_at'];


		//alter subs_transactions
		$subs_data = array(
			'altered_by' => $_SESSION['admin_login']['user'],
			'altered_at' => date('Y-m-d h:i:s')
		);

		$gen_subs_id = $this->generateInvoiceNumber()->row_array();
		$temp = (int)$gen_subs_id['id'];
		$temp++;
		$new_subs_id = str_pad($temp, 6, "0", STR_PAD_LEFT);

		$subs_refund_data = array(
			'id' => $new_subs_id,
			'customers_id' => $members_id,
			'invoice_date' => date('Y-m-d h:i:s'),
			'amount' => $amount_refunded,
			'payment_mode' => $payment_mode,
			'status' => 'refund',
			'filled_by' => $_SESSION['admin_login']['user']
		);

		$pjo_data = array(
			'account_id' => $members_id,
			'job_order' => 'Refund',
			'subs_transactions_id' => $new_subs_id,
			'parkspace_id' => $parkspace_id,
			'order_date' => date('Y-m-d H:i:s'), 
			'filled_by' => $_SESSION['admin_login']['user'],
			'amount' => $amount_refunded,
			'status' => 1
		);

				
		//insert in refund_details
		$refund_record = array(
			's_id'=>$subs_orginal['id'],
			's_refund_id'=>$new_subs_id,
			'amount_collected'=>$amount_received,
			'actual_amount'=>$actual_amount,
			'refund_amount'=>$amount_refunded,
			'payment_mode'=>$payment_mode,
			'cause'=>$cause,
			'comments'=>$comment,
			's_record'=>$record,
			'filled_by'=>$_SESSION['admin_login']['user'],
			'date'=>date('Y-m-d h:i:s')
		);

		$this->db->trans_start();
			$this->db->insert('refund_details', $refund_record);
			$this->db->where('id', $subs_orginal['id']);
			$this->db->update('subs_transactions', $subs_data);
			$this->db->insert('subs_transactions', $subs_refund_data);
			$this->db->insert('parking_job_orders', $pjo_data);
		return $this->db->trans_complete();		
	}

	public function getAllEmails() {
		return $this->db->get('customers');
	}

	public function getAllMemberships() {
		return $this->db->select('id')->from('customers')->like('id', 'BDF')->get();
	}

	public function getAllParkingLots() {
		return $this->db->get_where('parking', array('location_id' => 19));
	}

	public function getAllParkingSpaces() {
		$bdf_location_id = 19;
        $sql = "SELECT park_space .*
				FROM park_space 
				INNER JOIN parking ON parking.id = park_space.`Parking_id`
				INNER JOIN location ON location.id = parking.location_id
				WHERE parking.location_id = $bdf_location_id";
        return $this->db->query($sql);
	}

	public function getParkingPrice($parkingId) {
		$result = $this->db->get_where('parking', array('id' => $parkingId));
		return $result->result();
	}

	public function getParkingPriceRenewal($parkingId) {
		$result = $this->db->get_where('parking', array('id' => $parkingId));
		return $result->result();
	}

	public function getPriceFromParkSpaceName($parkSpace) {
		
		$sql = "SELECT 
		parking_id,
		parking.`annual_rent` 
	  FROM
		park_space 
		INNER JOIN parking 
		  ON parking.`id` = park_space.`Parking_id` 
	  WHERE park_space.`name` = '$parkSpace'";

	  $result = $this->db->query($sql);

	  return $result->result();

	}
	public function get_disc_parking($parkingId){
		$query = $this->db->get_where('parking', array('id' => $parkingId));
		return $query->result();
	}
	
	public function getParkingParkSpaceDetails($parkSpace) {
		
		$sql = "SELECT * ,park_space.name as park_name 
	  			FROM park_space 
				INNER JOIN parking ON parking.`id` = park_space.`Parking_id` 
	  			WHERE park_space.`id` = '$parkSpace'";

	  $result = $this->db->query($sql);
	  return $result->result();
	}

	public function getParkingLotNameAndParkingSpaceName($parkingSpace) {
		$this->db->select('park_space.name AS park_name, parking.`name`');
		$this->db->from('park_space');
		$this->db->join('parking', 'parking.id = park_space.parking_id', 'inner');
		$this->db->where('park_space.name', $parkingSpace);
		$result = $this->db->get();
		return $result->result();
	}

	public function checkMemberPark($membership = "", $parkingLot="", $parkingSpace="",$vehicleNumber="",$cpr="") {
		$this->db->select('
			park_space.`name`,park_space.id AS ps_id,park_space.Parking_id,
			parking.name AS parking_lot,parking.annual_rent,parking.discount_2020 as parking_discount,
			reserved_space.account_id,
			customers.`first_name`,	customers.`last_name`,customers.CPR,customers.`mobile_number`,customers.email,customers.gender,customers.create_date,
			account.years_active,account.expiry_date,
			annualDiscount.discount,
			account_cars.*,o.*,s.*,amt.membership_type_id,mt.*
			');
			$this->db->from('park_space');
			$this->db->join('reserved_space', 'reserved_space.`parkspace_id` = park_space.`id`', 'inner');
			$this->db->join('customers', 'customers.id = reserved_space.`account_id`', 'inner');
			$this->db->join('account', 'account.id = customers.id', 'inner');
			$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
			$this->db->join('annualDiscount', 'parking.id = annualDiscount.parking_id', 'inner');
			$this->db->join('subs_transactions s', 's.customers_id = customers.id', 'inner');
			$this->db->join('account_cars', 'account_cars.account_id = account.id', 'left');
			$this->db->join('organization o', 'o.customers_id = account.id', 'left');

			$this->db->join('accounts_membership_types amt', 'amt.account_id = customers.id', 'left');
			$this->db->join('membership_types mt', 'mt.id = amt.membership_type_id', 'left');
		

		//$this->db->where('account_cars.car_plate_number', $vehicleNumber);
		
		if($parkingLot != "" && $parkingLot != NULL){
			$this->db->where('park_space.parking_id', $parkingLot);
		}

		if($parkingSpace !="" && $parkingSpace != NULL ){
			$this->db->where('park_space.id', $parkingSpace);
		}
		
		if($vehicleNumber != "" && $vehicleNumber != NULL){
			$this->db->where('account_cars.car_plate_number', $vehicleNumber);
		}

		if($cpr != "" && $cpr != NULL){
			$this->db->where('customers.CPR', $cpr);
		}

		if($membership !="" && $membership != NULL){
			$this->db->where('customers.id', $membership);
		}
		//$this->db->order_by('customers.create_date','DESC');
		//$this->db->limit(1);
		$results = $this->db->get();

		return $results->result();
	}

	public function checkCurrentMemberPark($membership = "", $parkingLot="", $parkingSpace="",$vehicleNumber="") {
		if($vehicleNumber){
				$this->db->select('
				park_space.*,
				parking.name AS parking_lot,
				parking.annual_rent,
				reserved_space.account_id,
				customers.*,
				account.*,organization.*,
				annualDiscount.discount,
				account_cars.*,subs_transactions.*
				');
				$this->db->from('park_space');
				$this->db->join('reserved_space', 'reserved_space.`parkspace_id` = park_space.`id`', 'inner');
				$this->db->join('customers', 'customers.id = reserved_space.`account_id`', 'inner');
				$this->db->join('account', 'account.id = customers.id', 'inner');
				$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
				$this->db->join('annualDiscount', 'parking.id = annualDiscount.parking_id', 'inner');
				$this->db->join('account_cars', 'account_cars.account_id = account.id', 'inner');
				$this->db->join('organization', 'organization.customers_id = account.id', 'inner');
				$this->db->join('subs_transactions', 'subs_transactions.customers_id = account.id', 'inner');

				$this->db->where('account_cars.car_plate_number', $vehicleNumber);
			if($parkingLot){
				$this->db->where('park_space.parking_id', $parkingLot);
			}
			if($parkingSpace){
				$this->db->where('park_space.id', $parkingSpace);
			}
			$results = $this->db->get();

			return $results->result();
		}
		elseif($parkingLot && $parkingSpace) {
			$this->db->select('
				park_space.*,
				parking.name AS parking_lot,
				parking.annual_rent,
				reserved_space.account_id,
				customers.*,
				account.*,organization.*,
				annualDiscount.discount,
				account_cars.*,subs_transactions.*
			');
			$this->db->from('park_space');
			$this->db->join('reserved_space', 'reserved_space.`parkspace_id` = park_space.`id`', 'inner');
			$this->db->join('customers', 'customers.id = reserved_space.`account_id`', 'inner');
			$this->db->join('account', 'account.id = customers.id', 'inner');
			$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
			$this->db->join('annualDiscount', 'parking.id = annualDiscount.`parking_id`', 'inner');
			$this->db->join('account_cars', 'account_cars.account_id = account.id', 'inner');
			$this->db->join('organization', 'organization.customers_id = account.id', 'inner');
			$this->db->join('subs_transactions', 'subs_transactions.customers_id = account.id', 'inner');

			$this->db->where('park_space.parking_id', $parkingLot);
			$this->db->where('park_space.id', $parkingSpace);

			if($vehicleNumber){
				$this->db->where('account_cars.car_plate_number', $vehicleNumber);
			}
			
			$results = $this->db->get();

			return $results->result();

		} else {
			return FALSE;
		}
	}

	public function checkExpiredMemberPark($membership = "", $parkingLot="", $parkingSpace="",$vehicleNumber="") {
		if($vehicleNumber){
				$this->db->select('
				park_space.*,
				parking.name AS parking_lot,
				parking.annual_rent,
				reserved_space_history.account_id,
				customers.*,
				account.*,organization.*,
				annualDiscount.discount,
				account_cars.*,subs_transactions.*
				');
				$this->db->from('park_space');
				$this->db->join('reserved_space_history', 'reserved_space_history.`parkspace_id` = park_space.`id`', 'inner');
				$this->db->join('customers', 'customers.id = reserved_space_history.`account_id`', 'inner');
				$this->db->join('account', 'account.id = customers.id', 'inner');
				$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
				$this->db->join('annualDiscount', 'parking.id = annualDiscount.parking_id', 'inner');
				$this->db->join('account_cars', 'account_cars.account_id = account.id', 'inner');
				$this->db->join('organization', 'organization.customers_id = account.id', 'inner');
				$this->db->join('subs_transactions', 'subs_transactions.customers_id = account.id', 'inner');

				$this->db->where('account_cars.car_plate_number', $vehicleNumber);
			if($parkingLot){
				$this->db->where('park_space.parking_id', $parkingLot);
			}
			if($parkingSpace){
				$this->db->where('park_space.id', $parkingSpace);
			}
			$results = $this->db->get();

			return $results->result();
		}
		elseif($parkingLot && $parkingSpace) {
			$this->db->select('
				park_space.*,
				parking.name AS parking_lot,
				parking.annual_rent,
				reserved_space_history.account_id,
				customers.*,
				account.*,organization.*,
				annualDiscount.discount,
				account_cars.*,subs_transactions.*
			');
			$this->db->from('park_space');
			$this->db->join('reserved_space_history', 'reserved_space_history.`parkspace_id` = park_space.`id`', 'inner');
			$this->db->join('customers', 'customers.id = reserved_space_history.`account_id`', 'inner');
			$this->db->join('account', 'account.id = customers.id', 'inner');
			$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
			$this->db->join('annualDiscount', 'parking.id = annualDiscount.`parking_id`', 'inner');
			$this->db->join('account_cars', 'account_cars.account_id = account.id', 'inner');
			$this->db->join('organization', 'organization.customers_id = account.id', 'inner');
			$this->db->join('subs_transactions', 'subs_transactions.customers_id = account.id', 'inner');
			
			$this->db->where('park_space.parking_id', $parkingLot);
			$this->db->where('park_space.id', $parkingSpace);

			if($vehicleNumber){
				$this->db->where('account_cars.car_plate_number', $vehicleNumber);
			}
			
			$results = $this->db->get();

			return $results->result();

		} else {
			return FALSE;
		}
	}
	
	public function getReceiptInfo($id) {
		$this->db->select('customers.first_name,customers.last_name,customers.CPR,customers.id,customers.email,customers.mobile_number,customers.create_date as joining_date,
		(CASE WHEN account.`years_active` = 0 THEN "" ELSE p.annual_rent - subs_transactions.amount  END ) AS discount, 
		(CASE WHEN account.`years_active` = 0 THEN "" ELSE ((p.annual_rent - subs_transactions.amount)/ p.annual_rent) * 100 END ) AS discount_percentage, 
		organization.department,organization.profession,
		subs_transactions.amount,subs_transactions.filled_by,
		DATE_FORMAT(subs_transactions.invoice_date,"%d/%m/%Y") AS invoice_date,
		subs_transactions.payment_mode,subs_transactions.`id` AS invoice_number,

		(CASE 
		WHEN pjo.job_order LIKE "%Renew membership%" THEN pjo.order_date 
		WHEN pjo.job_order LIKE "%New membership%" THEN account.`create_date` 
		ELSE account.create_date		
		END )AS create_date,
		(CASE 
		WHEN pjo.job_order LIKE "%renew%"   THEN account.`expiry_date` 
		WHEN pjo.job_order LIKE "New membership" AND (
														SELECT COUNT(pjo.id) FROM parking_job_orders pjo 
														WHERE pjo.job_order LIKE "Renew membership" 
														AND pjo.account_id IN 
																(SELECT account_id FROM parking_job_orders WHERE subs_transactions_id IN ('.$id.'))
														) > 0
		
		THEN (
														SELECT pjo.order_date FROM parking_job_orders pjo 
														WHERE pjo.job_order NOT LIKE "New membership" 
														AND pjo.account_id IN 
																			(SELECT account_id FROM parking_job_orders WHERE subs_transactions_id IN ('.$id.'))
														)
		ELSE account.expiry_date END )AS expiry_date,
		account_cars.car_plate_number');
		$this->db->from('customers');
		$this->db->join('account', 'account.customers_id = customers.id ', 'INNER');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'INNER');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'INNER');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'INNER');
		$this->db->join('parking_job_orders pjo', 'pjo.subs_transactions_id = subs_transactions.id', 'LEFT');

		$this->db->join('reserved_space rs', 'rs.account_id = customers.id', 'LEFT');
		$this->db->join('park_space ps', 'ps.id = rs.parkspace_id', 'LEFT');
		$this->db->join('parking p', 'p.id = ps.Parking_id', 'LEFT');

		$this->db->where('subs_transactions.id', $id);

		return $this->db->get();
	}

	public function getReceiptPark($id) {
		$this->db->select("parking.name,
		park_space.name AS park_name");
		$this->db->from('location');
		$this->db->join('parking', 'parking.location_id = location.id', 'INNER');
		$this->db->join('park_space', 'park_space.Parking_id = parking.id', 'INNER');
		$this->db->join('reserved_space', 'reserved_space.parkspace_id = park_space.id', 'INNER');
		$this->db->join('subs_transactions', 'subs_transactions.`customers_id` = reserved_space.`account_id`', 'INNER');
		$this->db->where('location.id', 19);
		$this->db->where('subs_transactions.id', $id);

		return $this->db->get();
	}

    public function getFreeParkSpots($id) {
        $result = $this->db->get_where('park_space', array('parking_id' => $id, 'status' => '1', 'vacant' => 'free'));

       return $result;
	}

    public function getAllParkSpots($id) {
        $result = $this->db->get_where('park_space', array('parking_id' => $id));

       return $result;
	}

	public function getParkingTypes($parkingId){
        $result = $this->db->get_where('park_types', array('parking_id' => $parkingId));
		return $result;
	}
	
    public function getFreeParkSpotsReleased($id, $membership) {
        
		$sql = "SELECT 
		parking.`annual_rent`,
		park_space.`name`
		FROM
		reserved_space_history 
		INNER JOIN account 
			ON account.`id` = reserved_space_history.`account_id` 
		INNER JOIN park_space 
			ON park_space.`id` = reserved_space_history.`parkspace_id` 
		INNER JOIN parking 
			ON parking.`id` = park_space.`Parking_id` 
		WHERE account.id = '$membership';";

			$sqlTwo = "SELECT 
			parking.`annual_rent` 
		FROM
			parking 
		WHERE parking.`id` = '$id';";

		$result = $this->db->query($sql);
		$resultTwo = $this->db->query($sqlTwo);
		$data['old'] = $result->result();
		$data['new'] = $resultTwo->result();

		return $data;
    }

    public function checkEmail($email,$prefix = 'BDF%') {
		$active = 1;
		$bdf_prefix = $prefix;
		
		$this->db->select('*');
		$this->db->from('customers');
		$this->db->join('account','account.customers_id = customers.id','inner');
		$this->db->where('customers.email',$email);
		$this->db->where('account.active',$active);
		$this->db->where("customers.id LIKE '$bdf_prefix'");
		
		$result = $this->db->get();
		//$result = $this->db->get_where('customers', array('email' => $email));

		if($result->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	public function checkMobile($mobile,$prefix = 'BDF%') {
		$active = 1;
		$bdf_prefix = $prefix;

		$this->db->select('*');
		$this->db->from('customers');
		$this->db->join('account','account.customers_id = customers.id','inner');
		$this->db->where('customers.mobile_number',$mobile);
		$this->db->where('account.active',$active);
		$this->db->where("customers.id LIKE '$bdf_prefix'");
		
		$result = $this->db->get();

		//$result = $this->db->get_where('customers', array('mobile_number' => $mobile));

		if($result->num_rows() > 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	} 

    public function generateMembership() {
        return $this->db->select('id')->from('customers')->like('id', 'BDF')->order_by('id','DESC')->limit(1)->get();
	}
	
	public function generateInvoiceNumber() {
		return $this->db->select('id')->from('subs_transactions')->order_by('id', 'DESC')->limit(1)->get();
	}

	public function getBDFParkings(){
		$sql ="SELECT * FROM parking WHERE parking.location_id = 19"; 
		return $this->db->query($sql);
	}

    public function getBDFAnnualParking() {

        $bdf_location_id = 19;
        $active = 1;
        $sql = "SELECT 
				parking.name,
				park_space.parking_id,
				SUM( CASE WHEN vacant = 'free' AND `status` = '1' AND parking.location_id = $bdf_location_id THEN 1 ELSE 0 END ) AS capacity
				FROM park_space
				INNER JOIN parking ON parking.id = park_space.`Parking_id`
				INNER JOIN location ON location.id = parking.location_id
				WHERE parking.location_id = $bdf_location_id
				AND parking.active = $active
				GROUP BY parking_id";
        return $this->db->query($sql);
	}

	public function getMonthlyFee($month,$parkingId){

		$result = $this->db->select('price')->from('park_types')
				->where(array('duration' => $month,'parking_id'=>$parkingId))->get();
		// $result = $this->db->get_where('price','park_types', array('duration' => $month));
		if($result->row()){
			return $result->row();
		}
		else{
			return 0;
		}
	}

	//compare the prices of old and new park and send true if same price
	//else send the both prices for comparison 
	public function compareParkingPrices($id1,$id2,$duration){
		if($id1 == $id2){
			return true;
		}

		$query1 = $this->db->get_where('park_types', array('parking_id' => $id1,'duration'=>$duration));
		$parking1_result = $query1->row_array();
		$priceofPark1 = $parking1_result['price'];

		$query2 = $this->db->get_where('park_types', array('parking_id' => $id2,'duration'=>$duration));
		$parking2_result = $query2->row_array();
		$priceofPark2 = $parking2_result['price'];
		
		if($priceofPark1 == "" || $priceofPark2 == "" || $priceofPark1 == null || $priceofPark2 == null){
			return false;
		}
		else{
		
			if($priceofPark1 == $priceofPark2){
				return true;
			}

			return $priceofPark1.'-'.$priceofPark2;
		}

	}
	
	public function searchOrders($membership, $parkingLot, $parkingSpace, $cpr, $memberCar, $approved,$vehicleNumber = '') {

		//use where
		$where = "";

		if($membership) {
			$where .= "customers.id = '$membership'";
		} else {
			$where .= "customers.id LIKE 'BDF%'";
		}
		if($parkingLot) {
			$where .= " AND parking.id = '$parkingLot'";
		}
		if($parkingSpace) {
			$where .= " AND park_space.id = '$parkingSpace'";
		}
		if($cpr) {
			$where .= " AND customers.CPR = '$cpr'";
		}
		if($memberCar) {
			// $where .= " AND account_cars.car_plate_number = '$memberCar'";
		}
		if($approved) {
			
		}
		
		if($vehicleNumber != null && $vehicleNumber != '') {
			$where .= " AND account_cars.car_plate_number = '$vehicleNumber'";
		}

		$this->db->select("
							customers.first_name,
							customers.last_name,
							customers.CPR,
							customers.id,
							customers.mobile_number,
							organization.department,
							organization.profession,
							subs_transactions.amount,
							subs_transactions.invoice_date,
							subs_transactions.payment_mode,
							subs_transactions.`id` AS invoice_number,
							account.create_date,
							account.expiry_date,
							parking.name,
							park_space.name AS park_name
		");

		$this->db->from('customers');
		$this->db->join('account', 'account.customers_id = customers.id', 'inner');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'left');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'inner');
		$this->db->join('parking_job_orders', 'parking_job_orders.subs_transactions_id = subs_transactions.`id`', 'inner');
		$this->db->join('park_space', 'park_space.id = parking_job_orders.parkspace_id', 'inner');
		$this->db->join('parking', 'parking.id = park_space.`Parking_id`', 'inner');
		$this->db->join('location', 'location.id = parking.`location_id`', 'inner');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'left');
		$this->db->where($where);
		$this->db->order_by('subs_transactions.invoice_date', 'DESC');

		return $this->db->get();

	}

	public function getExpiredAccounts($data){

			$this->datatables->select ("customers.`id` AS 'customer_id',CONCAT(first_name,' ',last_name) AS 'Name',
									email,CPR,mobile_number,account.create_date AS 'created',account.expiry_date AS 'expired',
									parking.`name` AS 'parkingName',park_space.`name` AS 'parkSpaceName',
									vacant AS 'vacancy',park_space.status as Status ");
		$this->datatables->from('customers');
		$this->datatables->join('account','account.customers_id=customers.id');
		$this->datatables->join('account_cars','account_cars.account_id = customers.id');
		$this->datatables->join('reserved_space','customers.id=reserved_space.`account_id`');
		$this->datatables->join('park_space','park_space.`id`=reserved_space.parkspace_id');
		$this->datatables->join('parking','parking.id=park_space.Parking_id');
		
		//get todays date
		// if($data[0] == '' || $data[0]== null)
		// {
		// 	$currentDate = date('Y-m-d');
		// 	$data[0] = $currentDate."%";

		// 	$this->datatables->where("account.`expiry_date` LIKE",$data[0]);
		// }

		 if($data[0] != '' && $data[0] != null) {
			$dates = explode(' / ',$data[0]);
			$date1 = $dates[0].' 00:00:00';
			$date2 = $dates[1].' 00:00:00';

			$this->datatables->where('account.`expiry_date`>=',$date1);	
			$this->datatables->where('account.`expiry_date` <=',$date2);	
		}

		if($data[1]){
			$this->datatables->where('customers.id LIKE',$data[1]); 
		}

		if($data[2]){
			$this->datatables->where('customers.first_name LIKE',$data[2]); 
		}

		if($data[3]){
			$this->datatables->where('customers.CPR LIKE',$data[3]); 
		}
		if($data[4]){
			$this->datatables->where('account_cars.car_plate_number LIKE',$data[4]); 
		}
		
		//$this->datatables->where($where);
       			 
	    return $this->datatables->generate();
	}

	
	public function getOrdersInfo($id) { //getting customer information
		
		$this->db->select('
		customers.first_name,customers.last_name,customers.CPR,customers.id,customers.email,customers.mobile_number,
		customers.create_date as joining_date,account.`years_active`,p.annual_rent,	ad.discount as discount_array,
		(CASE WHEN account.`years_active` = 0 THEN "" ELSE p.annual_rent - subs_transactions.amount  END ) AS discount, 
		(CASE WHEN account.`years_active` = 0 THEN "" ELSE ((p.annual_rent - subs_transactions.amount)/ p.annual_rent) * 100 END ) AS discount_percentage, 
		organization.department,organization.profession,subs_transactions.filled_by,
		subs_transactions.amount,DATE_FORMAT(subs_transactions.invoice_date,"%d/%m/%Y") AS invoice_date
		,subs_transactions.payment_mode,subs_transactions.`id` AS invoice_number,
		(CASE 
		WHEN pjo.job_order LIKE "%Renew membership%" THEN ren_sl.renewal_date 
		WHEN pjo.job_order LIKE "%New membership%" THEN account.`create_date` 
		ELSE account.create_date		
		END )AS create_date,
		(CASE 
		WHEN pjo.job_order LIKE "%renew%"   THEN account.`expiry_date` 
		WHEN pjo.job_order LIKE "New membership" AND (
														SELECT COUNT(pjo.id) FROM parking_job_orders pjo 
														WHERE pjo.job_order LIKE "Renew membership" 
														AND pjo.account_id IN 
																(SELECT account_id FROM parking_job_orders WHERE subs_transactions_id IN ('.$id.'))
														) > 0
		
		THEN (
														SELECT pjo.order_date FROM parking_job_orders pjo 
														WHERE pjo.job_order NOT LIKE "New membership" 
														AND pjo.account_id IN 
																			(SELECT account_id FROM parking_job_orders WHERE subs_transactions_id IN ('.$id.'))
														ORDER BY pjo.id DESC LIMIT 1
														)
		ELSE account.expiry_date END )AS expiry_date,
		account_cars.car_plate_number,irn.`it_ref_name`
		');
		$this->db->from('customers');
		$this->db->join('account', 'account.customers_id = customers.id ', 'INNER');
		$this->db->join('organization', 'organization.customers_id = customers.id', 'INNER');
		$this->db->join('subs_transactions', 'subs_transactions.customers_id = customers.id', 'INNER');
		$this->db->join('account_cars', 'account_cars.account_id = customers.id', 'INNER');
		$this->db->join('parking_job_orders pjo', 'pjo.subs_transactions_id = subs_transactions.id', 'LEFT');
		$this->db->join('renewal_sales ren_sl', 'ren_sl.account_id = subs_transactions.customers_id', 'LEFT');

		$this->db->join('reserved_space rs', 'rs.account_id = customers.id', 'LEFT');
		$this->db->join('park_space ps', 'ps.id = rs.parkspace_id', 'LEFT');
		$this->db->join('parking p', 'p.id = ps.Parking_id', 'LEFT');
		$this->db->join('it_reference_names irn', 'p.id = irn.`parking_id`');
		$this->db->join('annualDiscount ad', 'ad.parking_id = p.id', 'LEFT');

		$this->db->where('subs_transactions.id', $id);
		$this->db->where("irn.`begin_range` <= ps.`id` AND irn.`end_range` >= ps.`id`");

		return $this->db->get();
	}

	public function getOrderPark($id,$job_order="") { //getting park information

		$this->db->select("parking.name,
		park_space.name AS park_name");
		$this->db->from('location');
		$this->db->join('parking', 'parking.location_id = location.id', 'INNER');
		$this->db->join('park_space', 'park_space.Parking_id = parking.id', 'INNER');
		$this->db->join('parking_job_orders', 'parking_job_orders.parkspace_id = park_space.id', 'INNER');
		$this->db->where('location.id', 19);
		$this->db->where('parking_job_orders.subs_transactions_id', $id);
		if($job_order != "" && $job_order != null){
			$this->db->where('parking_job_orders.job_order', $job_order);
		}
		return $this->db->get();
	}
	
	public function getParkSpaceId($parkSpaceName){
		$query = $this->db->get_where('park_space', array('name' => $parkSpaceName));
		return $query->row_array();
	}

	
	public function getClientLocations($clientId){
		$query = $this->db->get_where('location',array('client_id'=>$clientId)); 
		return $query;
	}

	public function getLocationParkings($locationId){
		$query = $this->db->get_where('parking',array('location_id'=>$locationId));
		return $query; 
	}

	public function getBDFAnnualParkingNew() {

		$bdf_location_id = 19;
        $sql = "SELECT 
				parking.name,
				park_space.parking_id,
				SUM( CASE WHEN vacant = 'free' AND `status` = '1' AND parking.location_id = $bdf_location_id THEN 1 ELSE 0 END ) AS capacity
				FROM park_space
				INNER JOIN parking ON parking.id = park_space.`Parking_id`
				INNER JOIN location ON location.id = parking.location_id
				WHERE parking.location_id = $bdf_location_id
				GROUP BY parking_id";
        return $this->db->query($sql);
	}

	public function getParkingTypesNew($parkingId){
        $result = $this->db->get_where('park_duration', array('parking_id' => $parkingId));
		return $result;
	}

	public function get_all_parkings(){

		$bdf_location_id = 19;
		$query = $this->db->get_where('parking', array('location_id' => $bdf_location_id));

		return $query->result_array();
	}

	public function get_all_parkspaces($id = 0) {
        $result = $this->db->get_where('park_space', array('parking_id' => $id));
       	return $result;
	}
	
	//$this->BDFModel->export_waiting_list_members($membership,$mobile_number,$cpr,$parkingSelect);
	public function export_waiting_list_members($membership,$mobile_number,$cpr,$parkingSelect){

		$this->db->select('waitinglist.*,parking.name,waitinglist.customers_id as cid');
		$this->db->from('waitinglist');
		$query = $this->db->join('parking','parking.id = waitinglist.parking_id');

		if($membership != "" && $membership != null){
			$this->db->where('customers_id',$membership);
		}
		
		if($cpr != "" && $cpr != null){
			$this->db->where('cpr',$cpr);
		}
		
		if($mobile_number != "" && $mobile_number != null){
			$this->db->where('mobile_number',$mobile_number);
		}

		if($parkingSelect != "" && $parkingSelect != null){
			$this->db->where('parking.id',$parkingSelect);
		}
	
		$waitingMembers = $query;
		$this->export($waitingMembers,'WaitingMembersList');
	}

	function export($result,$fname)
	{   
		$this->load->dbutil();
		$this->load->helper('file');
		$this->load->helper('download');
		$delimiter = ",";
		$newline = "\r\n";
		$filename = $fname.".csv";
		$data = $this->dbutil->csv_from_result($result->get(), $delimiter, $newline);
		force_download($filename, $data);
	}
	// public function getWaitingListMemberDetails($membershipId = ""){
	// 	$this->db->select('waitinglist.*,parking.name');
	// 	$this->db->from('waitinglist');
	// 	$this->db->join('parking','parking.id = waitinglist.parking_id');
		
	// 	if($membershipId != "" && $membershipId != null){
	// 		$this->db->where('customers_id',$membershipId);
	// 	}		
	// 	$query = $this->db->get();

	// 	// $query = $this->db->get_where('waitinglist',array('customers_id'=>$membershipId));
	// 	return $query->row_array();
	// }
	public function getMemberDetails($membershipId = ""){

		$query = $this->db->get_where('customers',array('id'=>$membershipId));
		return $query->row_array();
	}

	public function getWaitingListMemberDetails($id = ""){
		$this->db->select('waitinglist.*,parking.name');
		$this->db->from('waitinglist');
		$this->db->join('parking','parking.id = waitinglist.parking_id');
		//$this->db->join('park_space','park_space.id = waitinglist.parkspace_id');
		
		if($id != "" && $id != null){
			$this->db->where("waitinglist.id = $id");
		}
		else{
			$this->db->order_by('waitinglist.id','DESC');
			$this->db->limit(1);
		}		
		$query = $this->db->get();

		// $query = $this->db->get_where('waitinglist',array('customers_id'=>$membershipId));
		return $query->row_array();
	}
	public function get_waiting_list_members($data){
		$this->datatables->select('waitinglist.*,parking.name,waitinglist.customers_id as cid,waitinglist.id as wid');
		$this->datatables->from('waitinglist');
		$this->datatables->join('parking','parking.id = waitinglist.parking_id');
		
		if($data[0] != "" && $data[0] != null){
			$this->datatables->where('customers_id',$data[0]);
		}
		
		if($data[1] != "" && $data[1] != null){
			$this->datatables->where('cpr',$data[1]);
		}
		
		if($data[2] != "" && $data[2] != null){
			$this->datatables->where('mobile_number',$data[2]);
		}

		if($data[3] != "" && $data[3] != null){
			$this->datatables->where('parking.id',$data[3]);
		}
		
		$view='<a href="'.('waitingListReceipt/$1').'" class="btn btn-info" data-code="$1">View</a>';
	
		$this->datatables->add_column('view',$view,'wid');
		return $this->datatables->generate();
	}
	public function notify_member($status,$membershipId){
		$this->db->trans_start();
			$this->db->where('customers_id',$membershipId);
			$this->db->update('waitinglist',array('status'=>$status));
		return $this->db->trans_complete();
	}

	public function get_member_by_vehicle($car_plate_alpha = "",$car_plate_number = "",$parkingLot = "",$parkingSpace = "",$membership = "",$member_name = "",$mobile_number = "",$cpr = ""){
		
		$this->db->select('*,ps.name as parkspace,p.name as parking');
		$this->db->from('customers c');
		$this->db->join('account a','a.id = c.id');
		$this->db->join('account_cars ac','ac.account_id = c.id','left');
		$this->db->join('reserved_space rs','rs.account_id = a.`id`');
		$this->db->join('park_space ps','ps.id = rs.parkspace_id`');
		$this->db->join('parking p','p.id = ps.Parking_id');
		$this->db->join('organization o','o.customers_id = c.id','left');
		
		if($car_plate_number != "" && $car_plate_number != null){
			if($car_plate_alpha != "" && $car_plate_alpha != null){
				$car_number = $car_plate_alpha.$car_plate_number;
				$this->db->where("ac.car_plate_number LIKE '$car_number%'");
			}
			else{
				$this->db->where('ac.car_plate_number',$car_plate_number);
			}
		}
		
		if($parkingLot != "" && $parkingLot != null){
			$this->db->where('p.id',$parkingLot);
		}
		
		if($parkingSpace != "" && $parkingSpace != null){
			$this->db->where('ps.id',$parkingSpace);
		}
		
		if($membership != "" && $membership != null){
			$this->db->where("c.id",$membership);			
		}

		if($member_name != "" && $member_name != null){
			$this->db->where("c.first_name LIKE '$member_name%'");
		}

		if($mobile_number != null && $mobile_number != ""){
			$this->db->where("c.mobile_number LIKE '$mobile_number%'");
		}

		if($cpr != "" && $cpr != null){
			$this->db->where('c.CPR',$cpr);	
		}

		if($car_plate_number == "" && $mobile_number == "" && $parkingLot == "" && $membership == "" && $parkingSpace == "" && $cpr == "" && $member_name == ""){
			$this->db->where('a.active',1);
			$this->db->order_by('c.id');
			$this->db->limit(1);
		}

		return $this->db->get()->result(); 
	}

	public function get_waitinglist($parking_id,$parkspace_id){
		$status = 0; //not informed
	
		$this->db->select('*')->from('waitinglist');
		$this->db->where(array('parking_id'=>$parking_id,'status'=>$status));
		$this->db->where("parkspace_id IN (0,$parkspace_id)");
		$query = $this->db->order_by('create_date')->get();
	
		return $query->result_array();
	}


	public function freeze_parkspace($parkspace_id){

		$data = array(
			'vacant' => 'pending',
			'status' => '2'
		);
		
		$this->db->trans_start();
			$this->db->where('id', $parkspace_id);
			$this->db->update('park_space', $data);
		return $this->db->trans_complete();
	}


		public function inform_waitinglist_admin($parkspace_id = 0){

		if($parkspace_id != null && $parkspace_id != 0){

			//get parkspace details
			$this->db->select('ps.*,p.name as parking_name')->from('park_space ps');
			$this->db->join('parking p','p.id = ps.Parking_id');
			$this->db->where('ps.id',$parkspace_id);
			$query = $this->db->get();

			//$query = $this->db->get_where('park_space',array('id'=>$parkspace_id));
			$parkspace_details = $query->row_array();
			$parking_id = $parkspace_details['parking_name'];
			$parkspace_name = $parkspace_details['name'];

			//check if waitinglist has member waiting for this
			$waiting_list_member_details = $this->BDFModel->get_waitinglist($parking_id,$parkspace_id);
			$data['parking_id'] = $parking_id;
			$data['parkspace_id'] = $parkspace_id;
			$data['parkspace_name'] = $parkspace_name;

			if( isset($waiting_list_member_details) && !empty($waiting_list_member_details) && sizeof($waiting_list_member_details) != 0 ){

				$data['waiting_list_member'] = $waiting_list_member_details;

				//keep park pending
				$park_space_data = array(
					'vacant' => 'pend',
					'status' => '2'
				);		
			
				$this->db->trans_start();

					$this->db->where(array('id'=>$parkspace_id,'status'=>1,'vacant'=>'free'));
					$this->db->update('park_space', $park_space_data);
				$this->db->trans_complete();

					$count_rows = $this->db->affected_rows();
				$this->db->trans_start();

					//insert in pendning parks to be relased in  1week
					$pending_parks = array('parkspace_id'=>$parkspace_id,'status'=>0,'create_date'=>date('Y-m-d H:i:s')) ;
					$this->db->insert('pending_parkspaces', $pending_parks);
				
				$this->db->trans_complete();

				//inform admin
				$this->load->library('email');
              
			  $admin_email = 'vinith@park-point.com';
			                
              $this->load->library('mail');
             
              try{

				$mail = new PHPMailer();					
				$mail->isSMTP();					
				$mail->SMTPDebug = SMTP::DEBUG_SERVER;
				$mail->Host       = "smtp.gmail.com";
				$mail->Port       = 587; //you could use port 25, 587, 465 for googlemail					
				$mail->SMTPAuth   = true; 
				$mail->SMTPSecure = "tls";  //tls
				
				$mail->SMTPOptions = array(
										'ssl' => array(
											'verify_peer' => false,
											'verify_peer_name' => false,
											'allow_self_signed' => true
										)
									);

				$mail->Username   = "info@park-pass.com";
				$mail->Password   = "abcd@0987";

				$mail->setFrom('info@park-pass.com','Park Point');
				$mail->addReplyTo('no-reply@park-point.com');

				$mail->addAddress($admin_email,"Admin");
				$mail->AddCC('ali@park-point.com', 'IT Admin');

				$mail->Subject = 'Waiting List - Vacant Park';
					
				$mail->CharSet = 'UTF-8';
				$mail->isHTML(true);
				$mail->SMTPDebug =2;
				$mail->Body = $this->load->view('Admin/Pages/BDF/waiting_list_mail',$data,true);				
				
				$mail_response  = 0;
				if(!$mail->send())
				{
					$errorMessage = $mail->ErrorInfo;
					//echo json_encode($errorMessage);
					$mail_response = 0;
				}
				else
				{
					$msg=array('status'=>'success','msg'=>'Email has been sent successfully');
					//echo json_encode($msg);
					$mail_response = 1;
				}
				
				
				return true;				
              }
              catch(phpmailerException $e)
              {
                //echo $e->errorMessage();
				return false;
			  }
				return true;			  
			}
			else{
				return false;
			}			
        }
        else{
			return false;
		}     
	}
    
	public function get_pending_parks($data){
		$this->datatables->select('pp.*,ps.*,ps.id as pid,p.name as parking_name');
		$this->datatables->from('park_space ps');
		$this->datatables->join('parking p','p.id = ps.Parking_id');
		$this->datatables->join('pending_parkspaces pp','pp.parkspace_id = ps.id');
		$this->datatables->where('p.location_id',19);
		$this->datatables->where('ps.vacant','pend');
		$this->datatables->where('ps.status',2);
		$this->datatables->where('pp.status',0);

		if($data[0] != "" && $data[0] != null){
			$this->datatables->where("p.id", $data[0]);
		}
		
		if($data[1] != "" && $data[1] != null){
			$this->datatables->where('ps.id',$data[1]);
		}
		
		$view='<a href="'.('release_park/$1').'" class="btn btn-info" data-code="$1">Release</a>';
	
		$this->datatables->add_column('view',$view,'pid');
		return $this->datatables->generate();
	}

	public function set_park_free($parkspace_id = 0){

		$pending_data = array(
			'release_date' => date('Y-m-d H:i:s'),
			'status' => 1,
			'released_by'=>$_SESSION['admin_login']['user']
		);
		
		$parkspace_data = array(
			'vacant'=>'free',
			'status'=>1
		);
	
		$this->db->trans_start();

			$this->db->where(array('parkspace_id'=>$parkspace_id));
			$this->db->update('pending_parkspaces', $pending_data);

			$this->db->where(array('id'=>$parkspace_id,'vacant'=>'pend','status'=>2));
			$this->db->update('park_space', $parkspace_data);

		return $this->db->trans_complete();	
	}
	
	public function getCancellationInvoiceNumber($membership_id = "",$payment_mode = ""){
		
		$query = $this->db->get_where('subs_transactions',array('customers_id'=>$membership_id,'payment_mode'=>$payment_mode));
		return $query->row_array();	
	}

	public function getWaitingNumber($parking_id = 0){
		$waitingtoken = "SELECT COUNT(id) AS token FROM waitinglist WHERE parking_id  = $parking_id";
		$query = $this->db->query($waitingtoken);
		return $query->row_array();
	} 

	public function update_accessories($data){

		$this->db->trans_start();

		//update in accessories_accounts
			foreach($data['old_accessories'] as $old_accessory){
				$acc_cat_park = $this->get_acc_cat_parking_details($old_accessory->id);

				$old_accessories_accounts_data = array(
					'acc_status' => ($old_accessory->status == 3 ? 'Lost' :'Return'),
					'status'=> ($old_accessory->status == 3 ? 3 :1),
					'acc_charges'=> $old_accessory->charges 
				);
				
				$this->db->where(array('acc_id'=>$old_accessory->id,'account_id' => $data['account_id']));//acc_id is unique so no need for parkspace_id
				$this->db->update('accessories_accounts', $old_accessories_accounts_data);
				
				// unset($accessories_accounts_data);

				$accessories_parkspaces_data = array(
					'status'=>($old_accessory->status == 3 ? 3:1)
				);

				$this->db->where(array('acc_id'=>$old_accessory->id,'status'=>2));//acc_id is unique so no need for parkspace_id
				$this->db->update('accessories_parkspaces',$accessories_parkspaces_data);


				$accessories_data = array(
					'acc_status'=> ($old_accessory->status == 3 ? 'Lost' :'Return'),
					'status'=>($old_accessory->status == 3 ? 3:1),
					'comments'=>($old_accessory->status == 3 ? 'Lost' :'Return') 
				);

				$this->db->where(array('id'=>$old_accessory->id,'status'=>2));//a_id is unique and status =2 in use
				$this->db->update('accessories',$accessories_data);


				if($old_accessory->status == 4){
					$this->db->where(array('parking_id'=>$acc_cat_park['parking_id'],'acc_cat_id'=>$acc_cat_park['ac_id'],'status'=>1));
					$this->db->set('quantity', 'quantity + 1', FALSE);
					$this->db->update('accessories_parking');
				}

			}

			foreach($data['new_accessories'] as $new_accessory){

				$accessories_category = $this->get_accessory_charge($new_accessory);

				$new_accessories_account_data = array(
				'account_id' => $data['account_id'],
				'acc_id' => $new_accessory,
				'acc_charges' => $accessories_category['deposit_charges'],
				'acc_status' => 'In Use',
				'filled_by'=>$_SESSION['admin_login']['user'],	
				'date' => date('Y-m-d H:i:s'),
				'status' => 2
				);

				$this->db->insert('accessories_accounts', $new_accessories_account_data);
				unset($accessories_accounts_data);

				$accessories_parkspaces_data = array(
					'status'=>2
				);

				$this->db->where(array('acc_id'=>$new_accessory,'parkspace_id'=>$data['new_parkspace_id'],'status'=>1));
				$this->db->update('accessories_parkspaces',$accessories_parkspaces_data);

				// $accessories_parking_data = array(
				// 	'quantity'=> quantity - 1 
				// );

				$this->db->where(array('parking_id'=>$data['new_parking_id'],'acc_cat_id'=>$accessories_category['ac_id'],'status'=>1));
				$this->db->set('quantity','quantity - 1',FALSE);
				$this->db->update('accessories_parking');

				$accessories_data = array(
					'acc_status'=> 'In Use',
					'status'=>2,
					'comments'=>'In Use' 
				);

				$this->db->where(array('id'=>$new_accessory,'status'=>1));//a_id is unique and status =1 active
				$this->db->update('accessories',$accessories_data);
			}

		return $this->db->trans_complete();	

	}

	public function get_accessory_charge($a_id){
		$status = 1;//active acc category
		$this->db->select('*,ac.id as ac_id');
		$this->db->from('accessories_categories ac');
		$this->db->join('accessories a','a.acc_cat_id = ac.id');
		$this->db->where('a.id',$a_id);
		$this->db->where('ac.status',$status);
		$query = $this->db->get();
		return $query->row_array();

	}

	public function get_acc_cat_parking_details($a_id){
		$status = 1;//active acc category
		
		$this->db->select('*,ac.id as ac_id,ps.id as ps_id,ps.Parking_id as parking_id');
		$this->db->from('accessories_categories ac');
		$this->db->join('accessories a','a.acc_cat_id = ac.id');
		$this->db->join('accessories_parkspaces ap','ap.`acc_id` = a.`id`');
		$this->db->join('park_space ps','ps.id = ap.`parkspace_id`');
		
		$this->db->where('a.id',$a_id);
		$this->db->where('ac.status',$status);
		$this->db->where('a.status',2);
		$this->db->where('ap.status',1);
		
		$query = $this->db->get();
		return $query->row_array();

	}

	public function cancel_member_accessories($data){
		$this->db->trans_start();

			foreach($data['old_accessories'] as $old_accessory){
				$acc_cat_park = $this->get_acc_cat_parking_details($old_accessory->id);

				$old_accessories_accounts_data = array(
					'acc_status' => ($old_accessory->status == 3 ? 'Lost' :'Return'),
					'status'=> ($old_accessory->status == 3 ? 3 :1),
					'acc_charges'=> $old_accessory->charges 
				);
				
				$this->db->where(array('acc_id'=>$old_accessory->id,'account_id' => $data['account_id']));//acc_id is unique so no need for parkspace_id
				$this->db->update('accessories_accounts', $old_accessories_accounts_data);
				
				$accessories_parkspaces_data = array(
					'status'=>($old_accessory->status == 3 ? 3:1)
				);

				$this->db->where(array('acc_id'=>$old_accessory->id,'status'=>2));//acc_id is unique so no need for parkspace_id
				$this->db->update('accessories_parkspaces',$accessories_parkspaces_data);

				$accessories_data = array(
					'acc_status'=> ($old_accessory->status == 3 ? 'Lost' :'Return'),
					'status'=>$old_accessory->status,
					'comments'=>($old_accessory->status == 3 ? 'Lost' :'Return') 
				);

				$this->db->where(array('id'=>$old_accessory->id,'status'=>2));//a_id is unique and status =2 in use
				$this->db->update('accessories',$accessories_data);

				if($old_accessory->status == 4){
					$this->db->where(array('parking_id'=>$acc_cat_park['parking_id'],'acc_cat_id'=>$acc_cat_park['ac_id'],'status'=>1));
					$this->db->set('quantity', 'quantity + 1', FALSE);
					$this->db->update('accessories_parking');
				}				

			}

		return $this->db->trans_complete();	
	}

	public function transfer_accessories($data){			//renewal

		//print_r($data);
		$this->db->trans_start();

			//if($data['new_ps'] == true){
				$in_use_status = 2;

				if(isset($data['old_accessories'])){


				foreach($data['old_accessories'] as $old_accessory){ //returning old

					$accessories_data = array(
						'acc_status'=> ($old_accessory->status == 3 ) ? 'Lost':'Active',
						'status'=> $old_accessory->status,
						'comments'=> ($old_accessory->status == 3 ) ? 'Lost':'Active'
					);	
					$this->db->where(array('id'=>$old_accessory->id,'status'=>2));//a_id is unique and status =2 in use
					$this->db->update('accessories',$accessories_data);//OK

					$new_accessories_account_data = array(
						'acc_charges' => $old_accessory->charges,
						'acc_status' => ($old_accessory->status == 3 ) ? 'Lost':'Active',
						'filled_by'=>$_SESSION['admin_login']['user'],	
						'date' => date('Y-m-d H:i:s'),
						'status' =>$old_accessory->status
					);	
					$this->db->update('accessories_accounts', $new_accessories_account_data, array('account_id' => $data['account_id'],'acc_id' => $old_accessory->id,'status'=>$in_use_status)); //OK

					$accessories_parkspaces_data = array(
						'status'=>($old_accessory->status == 3 ? 3:1)
					);	
					$this->db->where(array('acc_id'=>$old_accessory->id,'parkspace_id'=>$data['new_parkspace_id'],'status'=>1));
					$this->db->update('accessories_parkspaces',$accessories_parkspaces_data); // OK
	
	
					$this->db->where(array('parking_id'=>$data['new_parking_id'],'acc_cat_id'=>$old_accessory->acc_cat_id,'status'=>1));
					if($old_accessory->status == 3 ){
						$this->db->set('quantity','quantity - 1',FALSE);
					}else{
						$this->db->set('quantity','quantity + 1',FALSE);
					}
					$this->db->update('accessories_parking');	
				}
			}

			if(isset($data['new_accessories'])){

				foreach($data['new_accessories'] as $new_accessory){ //assigning new

					$accessories_category = $this->get_accessory_charge($new_accessory->id);

					$accessories_data = array(
						'acc_status'=> 'In Use',
						'status'=>2,
						'comments'=>'In Use' 
					);	
					$this->db->where(array('id'=>$new_accessory->id,'status'=>1));//a_id is unique and status =1 active
					$this->db->update('accessories',$accessories_data);//OK
	
					$new_accessories_account_data = array(
					'account_id' => $data['account_id'],
					'acc_id' => $new_accessory->id,
					'acc_charges' => 0,
					'acc_status' => 'In Use',
					'filled_by'=>$_SESSION['admin_login']['user'],	
					'date' => date('Y-m-d H:i:s'),
					'status' => 2
					);
	
					$this->db->insert('accessories_accounts', $new_accessories_account_data);//OK
					unset($accessories_accounts_data);

					$acc_ps_data_new = array(
						'acc_id'=> $new_accessory->id,
						'parkspace_id'=>$data['new_parkspace_id'],
						'status'=> 2,
						'filled_by'=>$_SESSION['admin_login']['user'],
					);
						
					$this->db->insert('accessories_parkspaces',$acc_ps_data_new);//OK
					
					$this->db->where(array('parking_id'=>$data['new_parking_id'],'acc_cat_id'=>$new_accessory->acc_cat_id,'status'=>1));
					$this->db->set('quantity','quantity - 1',FALSE);
					$this->db->update('accessories_parking');//OK
		
				}
			}
			//}
		return $this->db->trans_complete();	
	}

	public function get_membership_type($park_type_id = 0,$payment_type = 'Cash'){
		if($payment_type == 'Cardiac' || $payment_type == 'BDFRMS'){
			$query = $this->db->select('*')->from('membership_types')->where('membership_duration',$park_type_id)->where("membership_type like '$payment_type'")->get();
		}
		else{
			$query = $this->db->select('*')->from('membership_types')->where('membership_duration',$park_type_id)->get();
		}
		return $query->row_array();
	}

	public function get_upgraded_parkings($id = 0) {
		
		$query 					= $this->db->get_where('parking',array('id'=>$id));
		$current_parking 		= $query->row_array();
		$current_parking_rent 	= $current_parking['annual_rent'];

		$location_id =  19;//bdf

		$this->db->select("parking.*,SUM(CASE WHEN status = 1 AND vacant LIKE '%free%' THEN 1 ELSE 0 END) AS capacity ");
		$this->db->from('parking');
		$this->db->join('park_space ps','ps.Parking_id = parking.id');
		$this->db->where("annual_rent >= $current_parking_rent ");
		$this->db->where("location_id = $location_id ");
		$this->db->where("parking.active",1);
		$this->db->group_by("parking.id ");
		$result = $this->db->get();

       return $result;
	}

	public function get_replacement_accessories($acc_cat_id = 0,$parking_id = 0){
		
		if($acc_cat_id != 2 && $acc_cat_id != 0){
			$query = $this->db->select('*')->from('accessories_parking')->where(array('parking_id'=>$parking_id,'acc_cat_id'=>$acc_cat_id))->get();
			$acc_parking_id_query = $query->row_array();
			$acc_parking_id = $acc_parking_id_query['id'];
		}		

		$status = 1;

		$this->db->select('*,a.id as a_id,a.name as a_name,ac.id as ac_id,ac.name as acc_cat_name');
		$this->db->from ('accessories a');
		$this->db->join ('accessories_categories ac','ac.id = a.acc_cat_id');
		
		if($acc_cat_id != 2 && $acc_cat_id != 0){
			$this->db->where('a.acc_parking_id',$acc_parking_id);
		}

		$this->db->where('a.acc_cat_id',$acc_cat_id);
		
		$this->db->where('a.status',$status);
		$this->db->where('ac.status',$status);
		$result = $this->db->get();

		return $result;
	}
	
	public function replaceLostAccessoryWithNew($old_accessory,$new_acc_id = 0,$parkspace_id,$membershipGenerated){

		//print_r($new_acc_id);
		$acc_ps_data = array(
			'status'=>$old_accessory->status
		);
		$in_use_status = 2;
		
		$this->db->select('a.*,ac.*,a.name as acc_name')->from('accessories a');
		$this->db->join('accessories_categories ac','a.acc_cat_id = ac.id');
		$this->db->where(array('a.id'=>$old_accessory->id,'a.status'=>2));
		$query = $this->db->get();
		$acc_category = $query->row_array();

		$acc_account_data = array(
			'status'=>$old_accessory->status,
			'acc_status'=>($old_accessory->status == 3)?'Lost':'Return',
			'acc_charges'=>($old_accessory->status == 3) ? $acc_category['lost_charges'] :  $acc_category['deposit_charges']
		);
		
		$acc_account_data_new = array(
			'account_id'=>$membershipGenerated,
			'acc_id'=>$new_acc_id,
			'acc_charges'=>$acc_category['selling_charges'],
			'acc_status'=>'In Use',
			'filled_by'=>$_SESSION['admin_login']['user'],
			'date'=>date('Y-m-d h:i:s'),
			'status'=>2
		);

		$acc_ps_data_new = array(
			'acc_id'=>$new_acc_id,
			'parkspace_id'=>$parkspace_id,
			'status'=> 2,
			'filled_by'=>$_SESSION['admin_login']['user'],
		);

		$acc_data = array(
			'status'=>$old_accessory->status,
			'acc_status'=>($old_accessory->status == 3) ? 'Lost':'Return',
			'comments'=>"Lost/Replaced by $new_acc_id",
		);

		$query = $this->db->get_where('accessories',array('id'=>$new_acc_id,'status'=>1));
		$new_accessory = $query->row_array();

		$acc_data_new = array(
			"acc_status"=>"In Use",
			"status"=>2,
			"comments"=>"In Use"
		);

		
		
		$this->db->trans_start();
			$this->db->update('accessories_parkspaces', $acc_ps_data, array('acc_id' => $old_accessory->id,'parkspace_id'=>$parkspace_id,'status'=>$in_use_status));//OK --added
			$this->db->update('accessories_accounts', $acc_account_data, array('account_id' => $membershipGenerated,'acc_id' => $old_accessory->id,'status'=>$in_use_status)); //OK
			$this->db->insert('accessories_accounts',$acc_account_data_new);//OK
			$this->db->insert('accessories_parkspaces',$acc_ps_data_new);//OK
			$this->db->update('accessories', $acc_data, array('id' => $old_accessory->id,'status'=>$in_use_status));//old one lost OK
			
			$this->db->where(array('id'=>$new_accessory['acc_parking_id'],'status'=>1));
			$this->db->set('quantity', 'quantity - 1', FALSE);
			$this->db->update('accessories_parking'); //NOT OK

			$this->db->update('accessories', $acc_data_new, array('id' => $new_acc_id,'status'=>1));//no renaming and used OK
		return $this->db->trans_complete();

	}

	public function get_charges($account_id = ''){

		if($account_id != null && $account_id != ''){
			$this->db->select('*');
			$this->db->from('subs_transactions s');
			$this->db->join('accessories_accounts aa','aa.`account_id` = s.`customers_id`');
			$this->db->where(' s.`customers_id`',$account_id);
			$this->db->where("s.`payment_mode` LIKE '%cancellation%'");
			$this->db->get();

		}
	}

	public function get_subs_year(){

		$this->db->select("DATE_FORMAT(s.`invoice_date`,'%Y') AS years");
		$this->db->from("subs_transactions s");
		$this->db->where("s.`invoice_date` IS NOT NULL");
		$this->db->where("s.`invoice_date` NOT LIKE '00%'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_subs_status(){

		$this->db->select("*,(CASE WHEN s.status IS NOT NULL AND  s.`status` NOT LIKE '' THEN s.status ELSE 'New Membership' END) AS membership_types");
		$this->db->from("subs_transactions s");
		$this->db->where("s.`status` NOT IN ('refund','cancellation','transfer')");
		$this->db->group_by("s.`status`");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_renewal_years($membership_type){

		if($membership_type == 'Renew'){
			$query = $this->db->query("SELECT DATE_FORMAT(s.`invoice_date`,'%Y') AS years 
			FROM subs_transactions s 
			WHERE s.`invoice_date` IS NOT NULL 
			AND s.`invoice_date` NOT LIKE '00%'
			
			UNION
			
			SELECT DATE_FORMAT(rsl.renewal_date,'%Y') AS years 
			FROM  renewal_sales rsl 
			WHERE rsl.renewal_date IS NOT NULL 
			AND rsl.renewal_date  NOT LIKE '00%'");
		}
		else if ($membership_type == 'New Membership'){
			$query = $this->db->query("SELECT DATE_FORMAT(s.`invoice_date`,'%Y') AS years 
			FROM subs_transactions s 
			WHERE s.`invoice_date` IS NOT NULL 
			AND s.`invoice_date` NOT LIKE '00%'
			GROUP BY DATE_FORMAT(s.`invoice_date`,'%Y')");
		}
		else{

		}		

		return $query->result_array();
	}

}

?>